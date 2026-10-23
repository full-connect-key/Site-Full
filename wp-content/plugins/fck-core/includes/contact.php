<?php
/**
 * Formulário de contato: validação no servidor, honeypot, limite de tentativas
 * por IP, gravação em "Mensagens de contato" e envio por e-mail quando houver
 * destinatário configurado. Funciona com e sem JavaScript.
 */

defined( 'ABSPATH' ) || exit;

const FCK_CONTACT_LIMITS = array(
	'name'    => 120,
	'email'   => 254,
	'company' => 120,
	'message' => 4000,
);

function fck_contact_settings(): array {
	$s = get_option( 'fck_contact_settings', array() );
	return wp_parse_args( is_array( $s ) ? $s : array(), array( 'recipients' => array(), 'retention_months' => 12 ) );
}

/** E-mails de destino para um assunto (específicos + "todos os assuntos"). */
function fck_contact_recipients( string $subject ): array {
	$emails = array();
	foreach ( (array) fck_contact_settings()['recipients'] as $r ) {
		if ( ( 'all' === $r['subject'] || $subject === $r['subject'] ) && is_email( $r['email'] ) ) {
			$emails[] = $r['email'];
		}
	}
	return array_values( array_unique( $emails ) );
}

/**
 * Valida os dados. Retorna [dados limpos, erros por campo].
 * As mensagens de erro vêm dos textos de interface (painel).
 */
function fck_contact_validate( array $raw ): array {
	$data   = array(
		'subject' => sanitize_key( (string) ( $raw['subject'] ?? '' ) ),
		'name'    => sanitize_text_field( (string) ( $raw['name'] ?? '' ) ),
		'email'   => sanitize_email( (string) ( $raw['email'] ?? '' ) ),
		'company' => sanitize_text_field( (string) ( $raw['company'] ?? '' ) ),
		'message' => sanitize_textarea_field( (string) ( $raw['message'] ?? '' ) ),
		'consent' => ! empty( $raw['consent'] ),
	);
	$errors = array();
	if ( ! array_key_exists( $data['subject'], fck_contact_subjects() ) ) {
		$errors['subject'] = fck_ui( 'form', 'required' );
	}
	foreach ( array( 'name', 'message' ) as $f ) {
		if ( '' === trim( $data[ $f ] ) ) {
			$errors[ $f ] = fck_ui( 'form', 'required' );
		}
	}
	if ( '' === trim( (string) ( $raw['email'] ?? '' ) ) ) {
		$errors['email'] = fck_ui( 'form', 'required' );
	} elseif ( ! is_email( $data['email'] ) ) {
		$errors['email'] = fck_ui( 'form', 'invalid_email' );
	}
	foreach ( FCK_CONTACT_LIMITS as $f => $max ) {
		if ( ! isset( $errors[ $f ] ) && mb_strlen( (string) $data[ $f ] ) > $max ) {
			$errors[ $f ] = fck_fill( fck_ui( 'form', 'too_long' ), array( 'max' => $max ) );
		}
	}
	if ( ! $data['consent'] ) {
		$errors['consent'] = fck_ui( 'form', 'consent_required' );
	}
	return array( $data, $errors );
}

function fck_client_ip_hash(): string {
	$ip = (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ); // phpcs:ignore -- usado só como chave de limite (hash).
	return hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) );
}

/** Limite: 5 envios a cada 10 minutos por IP. */
function fck_contact_rate_limited(): bool {
	$key   = 'fck_rl_' . substr( fck_client_ip_hash(), 0, 32 );
	$count = (int) get_transient( $key );
	if ( $count >= (int) apply_filters( 'fck_contact_rate_limit', 5 ) ) {
		return true;
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );
	return false;
}

add_action( 'admin_post_nopriv_fck_contact', 'fck_handle_contact' );
add_action( 'admin_post_fck_contact', 'fck_handle_contact' );

function fck_handle_contact(): void {
	$wants_json = str_contains( (string) ( $_SERVER['HTTP_ACCEPT'] ?? '' ), 'application/json' ); // phpcs:ignore
	$back       = wp_get_referer() ?: home_url( '/' );

	$respond = static function ( int $status, array $body ) use ( $wants_json, $back ): void {
		if ( $wants_json ) {
			wp_send_json( $body, $status );
		}
		$args = array( 'contato' => $body['ok'] ? 'sucesso' : 'erro' );
		wp_safe_redirect( add_query_arg( $args, remove_query_arg( 'contato', $back ) ) . '#formulario', 303 );
		exit;
	};

	if ( ! isset( $_POST['fck_contact_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['fck_contact_nonce'] ) ), 'fck_contact' ) ) {
		$respond( 400, array( 'ok' => false, 'message' => fck_ui( 'form', 'send_error' ) ) );
	}

	// Honeypot e tempo mínimo de preenchimento: robôs recebem "sucesso" e nada é salvo.
	$started = (int) ( $_POST['fck_started'] ?? 0 );
	if ( ! empty( $_POST['website'] ) || ( $started && time() - $started < 3 ) ) {
		$respond( 200, array( 'ok' => true ) );
	}

	if ( fck_contact_rate_limited() ) {
		$respond( 429, array( 'ok' => false, 'message' => fck_ui( 'form', 'rate_limited' ) ) );
	}

	[ $data, $errors ] = fck_contact_validate( wp_unslash( $_POST ) ); // phpcs:ignore -- sanitizado em fck_contact_validate.
	if ( $errors ) {
		$respond( 422, array( 'ok' => false, 'errors' => $errors, 'message' => fck_ui( 'form', 'error_summary' ) ) );
	}

	$result = fck_store_and_send_contact( $data );
	if ( ! $result ) {
		$respond( 500, array( 'ok' => false, 'message' => fck_ui( 'form', 'send_error' ) ) );
	}
	$respond( 200, array( 'ok' => true ) );
}

/** Salva a mensagem e envia e-mail (se houver destinatário). Retorna false só se não conseguiu salvar. */
function fck_store_and_send_contact( array $data ): bool {
	$subjects = fck_contact_subjects();
	$post_id  = wp_insert_post(
		array(
			'post_type'   => 'fck_submission',
			'post_status' => 'private',
			'post_title'  => sprintf( '%s — %s', $subjects[ $data['subject'] ], $data['name'] ),
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		return false;
	}
	update_post_meta( $post_id, '_fck_submission', array_merge( $data, array( 'consent_at' => gmdate( 'c' ) ) ) );

	$to = fck_contact_recipients( $data['subject'] );
	if ( ! $to ) {
		update_post_meta( $post_id, '_fck_delivery', 'stored' );
		return true;
	}

	$body = implode(
		"\n",
		array(
			'Assunto: ' . $subjects[ $data['subject'] ],
			'Nome: ' . $data['name'],
			'E-mail: ' . $data['email'],
			'Empresa: ' . ( $data['company'] ?: '—' ),
			'',
			$data['message'],
			'',
			'Consentimento registrado em ' . wp_date( 'd/m/Y H:i' ),
		)
	);
	$sent = wp_mail(
		$to,
		'[Site] ' . $subjects[ $data['subject'] ] . ' — ' . $data['name'],
		$body,
		array( 'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>' )
	);
	update_post_meta( $post_id, '_fck_delivery', $sent ? 'sent' : 'failed' );
	// Falha de e-mail não perde a mensagem: ela fica salva no painel.
	return true;
}

/**
 * SMTP. Em desenvolvimento usa o Mailpit (FCK_SMTP_HOST no wp-config.php).
 * Em produção, configure as constantes só após aprovação e credenciais.
 */
add_action(
	'phpmailer_init',
	static function ( $mailer ): void {
		if ( ! defined( 'FCK_SMTP_HOST' ) || ! FCK_SMTP_HOST ) {
			return;
		}
		$mailer->isSMTP();
		$mailer->Host       = FCK_SMTP_HOST;
		$mailer->Port       = defined( 'FCK_SMTP_PORT' ) ? (int) FCK_SMTP_PORT : 587;
		$mailer->SMTPAuth   = defined( 'FCK_SMTP_USER' ) && FCK_SMTP_USER;
		$mailer->Username   = defined( 'FCK_SMTP_USER' ) ? FCK_SMTP_USER : '';
		$mailer->Password   = defined( 'FCK_SMTP_PASS' ) ? FCK_SMTP_PASS : '';
		$mailer->SMTPSecure = defined( 'FCK_SMTP_SECURE' ) ? FCK_SMTP_SECURE : '';
		$mailer->SMTPAutoTLS = defined( 'FCK_SMTP_SECURE' ) && FCK_SMTP_SECURE;
		if ( defined( 'FCK_MAIL_FROM' ) && FCK_MAIL_FROM ) {
			$mailer->setFrom( FCK_MAIL_FROM, defined( 'FCK_MAIL_FROM_NAME' ) ? FCK_MAIL_FROM_NAME : '', false );
		}
	}
);

// ---------------------------------------------------------------------------
// Retenção
// ---------------------------------------------------------------------------

function fck_schedule_retention(): void {
	if ( ! wp_next_scheduled( 'fck_contact_retention' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'fck_contact_retention' );
	}
}
add_action( 'init', 'fck_schedule_retention' );

add_action(
	'fck_contact_retention',
	static function (): void {
		$months = max( 1, (int) fck_contact_settings()['retention_months'] );
		$old    = get_posts(
			array(
				'post_type'      => 'fck_submission',
				'post_status'    => 'any',
				'posts_per_page' => 200,
				'fields'         => 'ids',
				'date_query'     => array( array( 'before' => "{$months} months ago" ) ),
			)
		);
		foreach ( $old as $id ) {
			wp_delete_post( $id, true );
		}
	}
);

// ---------------------------------------------------------------------------
// Painel: leitura das mensagens e configurações (somente administradores)
// ---------------------------------------------------------------------------

add_filter(
	'manage_fck_submission_posts_columns',
	static fn() => array(
		'cb'       => '<input type="checkbox">',
		'title'    => 'Mensagem',
		'email'    => 'E-mail',
		'delivery' => 'Envio',
		'date'     => 'Data',
	)
);

add_action(
	'manage_fck_submission_posts_custom_column',
	static function ( string $col, int $id ): void {
		$d = (array) get_post_meta( $id, '_fck_submission', true );
		if ( 'email' === $col ) {
			echo esc_html( $d['email'] ?? '' );
		} elseif ( 'delivery' === $col ) {
			$labels = array( 'sent' => 'Enviado por e-mail', 'stored' => 'Salvo (sem destinatário)', 'failed' => 'Falha no e-mail' );
			echo esc_html( $labels[ get_post_meta( $id, '_fck_delivery', true ) ] ?? '—' );
		}
	},
	10,
	2
);

add_action(
	'add_meta_boxes_fck_submission',
	static function (): void {
		remove_meta_box( 'submitdiv', 'fck_submission', 'side' );
		add_meta_box(
			'fck_submission_view',
			'Mensagem',
			static function ( WP_Post $post ): void {
				$d        = (array) get_post_meta( $post->ID, '_fck_submission', true );
				$subjects = fck_contact_subjects();
				echo '<table class="form-table"><tbody>';
				$rows = array(
					'Assunto'       => $subjects[ $d['subject'] ?? '' ] ?? '',
					'Nome'          => $d['name'] ?? '',
					'E-mail'        => $d['email'] ?? '',
					'Empresa'       => $d['company'] ?? '',
					'Consentimento' => ! empty( $d['consent'] ) ? 'Sim, em ' . ( $d['consent_at'] ?? '' ) : 'Não',
				);
				foreach ( $rows as $k => $v ) {
					printf( '<tr><th>%s</th><td>%s</td></tr>', esc_html( $k ), esc_html( (string) $v ) );
				}
				printf( '<tr><th>Mensagem</th><td>%s</td></tr>', nl2br( esc_html( (string) ( $d['message'] ?? '' ) ) ) );
				echo '</tbody></table>';
			},
			'fck_submission',
			'normal',
			'high'
		);
	}
);

add_action(
	'admin_menu',
	static function (): void {
		add_submenu_page(
			'edit.php?post_type=fck_submission',
			'Destinatários e retenção',
			'Destinatários e retenção',
			'manage_options',
			'fck-contact-settings',
			'fck_render_contact_settings'
		);
	}
);

add_action(
	'admin_post_fck_contact_settings',
	static function (): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Sem permissão.', 403 );
		}
		check_admin_referer( 'fck_contact_settings' );
		$recipients = array();
		foreach ( (array) ( $_POST['recipients'] ?? array() ) as $row ) {
			$email   = sanitize_email( wp_unslash( $row['email'] ?? '' ) );
			$subject = sanitize_key( $row['subject'] ?? '' );
			if ( is_email( $email ) && ( 'all' === $subject || array_key_exists( $subject, fck_contact_subjects() ) ) ) {
				$recipients[] = array( 'subject' => $subject, 'email' => $email );
			}
		}
		update_option(
			'fck_contact_settings',
			array(
				'recipients'       => $recipients,
				'retention_months' => min( 60, max( 1, absint( $_POST['retention_months'] ?? 12 ) ) ),
			),
			false
		);
		wp_safe_redirect( admin_url( 'edit.php?post_type=fck_submission&page=fck-contact-settings&updated=1' ) );
		exit;
	}
);

function fck_render_contact_settings(): void {
	$s       = fck_contact_settings();
	$options = array_merge( array( 'all' => 'Todos os assuntos' ), fck_contact_subjects() );
	echo '<div class="wrap"><h1>Formulário de contato — destinatários e retenção</h1>';
	echo '<p>Sem destinatário, as mensagens ficam salvas apenas em "Mensagens de contato" e nenhum e-mail é enviado.</p>';
	printf( '<form method="post" action="%s">', esc_url( admin_url( 'admin-post.php' ) ) );
	wp_nonce_field( 'fck_contact_settings' );
	echo '<input type="hidden" name="action" value="fck_contact_settings"><table class="widefat"><thead><tr><th>Assunto</th><th>E-mail</th></tr></thead><tbody>';
	$rows   = array_values( (array) $s['recipients'] );
	$rows[] = array( 'subject' => 'all', 'email' => '' );
	foreach ( $rows as $i => $r ) {
		echo '<tr><td><select name="recipients[' . (int) $i . '][subject]">';
		foreach ( $options as $v => $l ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $v ), selected( $r['subject'], $v, false ), esc_html( $l ) );
		}
		printf( '</select></td><td><input type="email" class="regular-text" name="recipients[%d][email]" value="%s"></td></tr>', (int) $i, esc_attr( $r['email'] ) );
	}
	echo '</tbody></table><p class="description">Para remover, apague o e-mail. Uma linha vazia é adicionada a cada gravação.</p>';
	printf(
		'<p><label>Apagar mensagens após <input type="number" min="1" max="60" name="retention_months" value="%d" class="small-text"> meses</label><br><span class="description">Padrão sugerido: 12 meses — decisão a validar com o jurídico.</span></p>',
		(int) $s['retention_months']
	);
	submit_button( 'Salvar' );
	echo '</form></div>';
}
