<?php
/**
 * Registro, exibição e gravação dos campos (meta boxes) com revisões.
 */

defined( 'ABSPATH' ) || exit;

require_once FCK_CORE_DIR . 'includes/schemas/fields.php';
require_once FCK_CORE_DIR . 'includes/schemas/templates.php';
require_once FCK_CORE_DIR . 'includes/schemas/post-types.php';
require_once FCK_CORE_DIR . 'includes/schemas/globals.php';

/** Grupos de campos aplicáveis a um post (por tipo, modelo de página ou chave global). */
function fck_groups_for( WP_Post $post, ?string $template = null ): array {
	if ( 'page' === $post->post_type ) {
		$template = $template ?? fck_template_of( $post->ID );
		return fck_template_schemas()[ $template ] ?? array();
	}
	if ( 'fck_global' === $post->post_type ) {
		return fck_global_schemas()[ $post->post_name ] ?? array();
	}
	return fck_post_type_schemas()[ $post->post_type ] ?? array();
}

/** Registra todos os metas com revisões (histórico, restauração e preview). */
add_action(
	'init',
	static function (): void {
		$by_type = array( 'page' => array(), 'fck_global' => array() );
		foreach ( fck_template_schemas() as $groups ) {
			foreach ( $groups as $g ) {
				$by_type['page'][ $g['id'] ] = true;
			}
		}
		foreach ( fck_global_schemas() as $groups ) {
			foreach ( $groups as $g ) {
				$by_type['fck_global'][ $g['id'] ] = true;
			}
		}
		foreach ( fck_post_type_schemas() as $type => $groups ) {
			foreach ( $groups as $g ) {
				$by_type[ $type ][ $g['id'] ] = true;
			}
		}
		foreach ( $by_type as $type => $ids ) {
			foreach ( array_keys( $ids ) as $id ) {
				register_post_meta(
					$type,
					'_fck_' . $id,
					array(
						'type'              => 'array',
						'single'            => true,
						'show_in_rest'      => false,
						'revisions_enabled' => true,
						'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
					)
				);
			}
		}
	},
	20
);

/** Editor de blocos só nos artigos; o restante usa os campos estruturados. */
add_filter(
	'use_block_editor_for_post_type',
	static fn( bool $use, string $type ) => 'fck_article' === $type ? $use : false,
	10,
	2
);

add_action(
	'init',
	static function (): void {
		remove_post_type_support( 'page', 'editor' );
		remove_post_type_support( 'page', 'comments' );
		remove_post_type_support( 'page', 'thumbnail' );
	},
	30
);

add_action(
	'add_meta_boxes',
	static function ( string $post_type, $post ): void {
		if ( ! $post instanceof WP_Post ) {
			return;
		}
		$groups = fck_groups_for( $post );

		if ( 'page' === $post_type && ! fck_template_of( $post->ID ) ) {
			add_meta_box(
				'fck_choose_template',
				'Modelo da página',
				static function (): void {
					echo '<p>Escolha um <strong>Modelo</strong> no painel "Atributos da página" e clique em <strong>Salvar rascunho</strong>. Os campos do modelo aparecem em seguida.</p>';
				},
				'page',
				'normal',
				'high'
			);
		}

		foreach ( $groups as $group ) {
			add_meta_box(
				'fck_' . $group['id'],
				$group['title'],
				static function ( WP_Post $p ) use ( $group ): void {
					$values = get_post_meta( $p->ID, '_fck_' . $group['id'], true );
					FCK_Fields::render_group( $group, is_array( $values ) ? $values : array(), 'fck[' . $group['id'] . ']' );
				},
				$post_type,
				$group['context'] ?? 'normal',
				$group['priority'] ?? 'high'
			);
		}
		if ( $groups ) {
			add_meta_box( 'fck_nonce', 'fck', static fn() => wp_nonce_field( 'fck_save', 'fck_nonce' ), $post_type, 'side', 'low' );
		}
	},
	10,
	2
);

// Esconde a caixa técnica do nonce.
add_action(
	'admin_head',
	static fn() => print '<style>#fck_nonce{display:none}</style>'
);

/**
 * Grava os grupos. Em pré-visualização de conteúdo publicado o WordPress cria
 * um autosave: os campos vão para ele (o site lê o autosave no preview).
 */
add_action(
	'save_post',
	static function ( int $post_id, WP_Post $post ): void {
		if ( empty( $_POST['fck_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['fck_nonce'] ) ), 'fck_save' ) ) {
			return;
		}
		$target = $post;
		$parent = wp_is_post_revision( $post_id );
		if ( $parent ) {
			if ( ! wp_is_post_autosave( $post_id ) ) {
				return; // Revisões comuns copiam os metas automaticamente.
			}
			$target = get_post( $parent );
		} elseif ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! $target || ! current_user_can( 'edit_post', $target->ID ) ) {
			return;
		}
		$template = 'page' === $target->post_type && isset( $_POST['page_template'] )
			? ( preg_match( '#^templates/([a-z]+)\.php$#', sanitize_text_field( wp_unslash( $_POST['page_template'] ) ), $m ) ? $m[1] : '' )
			: null;
		$input    = isset( $_POST['fck'] ) && is_array( $_POST['fck'] ) ? wp_unslash( $_POST['fck'] ) : array(); // phpcs:ignore -- sanitizado por grupo.

		foreach ( fck_groups_for( $target, $template ) as $group ) {
			if ( ! array_key_exists( $group['id'], $input ) ) {
				continue;
			}
			$key      = '_fck_' . $group['id'];
			$previous = get_post_meta( $target->ID, $key, true );
			$clean    = FCK_Fields::sanitize_group( $group, (array) $input[ $group['id'] ], is_array( $previous ) ? $previous : array() );
			// update_metadata direto: update_post_meta redirecionaria a revisão para o post pai.
			update_metadata( 'post', $post_id, $key, wp_slash( $clean ) );
		}

		$errors = FCK_Fields::take_errors();
		if ( $errors ) {
			set_transient( 'fck_notice_' . get_current_user_id(), array( 'type' => 'error', 'messages' => $errors ), 60 );
		}
	},
	10,
	2
);

/** Impede publicar com campos obrigatórios vazios (volta para rascunho com aviso). */
add_filter(
	'wp_insert_post_data',
	static function ( array $data, array $postarr ): array {
		if ( ! in_array( $data['post_status'], array( 'publish', 'future' ), true ) || empty( $postarr['ID'] ) ) {
			return $data;
		}
		if ( empty( $_POST['fck_nonce'] ) || empty( $_POST['fck'] ) || ! is_array( $_POST['fck'] ) ) { // phpcs:ignore -- nonce verificado em save_post.
			return $data;
		}
		$post = get_post( (int) $postarr['ID'] );
		if ( ! $post || wp_is_post_revision( $post ) ) {
			return $data;
		}
		$template = isset( $_POST['page_template'] ) && preg_match( '#^templates/([a-z]+)\.php$#', sanitize_text_field( wp_unslash( $_POST['page_template'] ) ), $m ) ? $m[1] : null;
		$input    = wp_unslash( $_POST['fck'] ); // phpcs:ignore
		$missing  = array();
		foreach ( fck_groups_for( $post, $template ) as $group ) {
			$values  = isset( $input[ $group['id'] ] ) ? (array) $input[ $group['id'] ] : array();
			$missing = array_merge( $missing, array_map( fn( $l ) => $group['title'] . ' › ' . $l, FCK_Fields::missing_required( $group, $values ) ) );
		}
		if ( $missing ) {
			$data['post_status'] = 'draft';
			set_transient(
				'fck_notice_' . get_current_user_id(),
				array( 'type' => 'warning', 'messages' => array_merge( array( 'Não publicado: preencha os campos obrigatórios.' ), $missing ) ),
				60
			);
		}
		return $data;
	},
	10,
	2
);

add_action(
	'admin_notices',
	static function (): void {
		$key    = 'fck_notice_' . get_current_user_id();
		$notice = get_transient( $key );
		if ( ! $notice ) {
			return;
		}
		delete_transient( $key );
		printf( '<div class="notice notice-%s is-dismissible"><ul>', esc_attr( $notice['type'] ) );
		foreach ( $notice['messages'] as $msg ) {
			echo '<li>' . esc_html( $msg ) . '</li>';
		}
		echo '</ul></div>';
	}
);

add_action(
	'admin_enqueue_scripts',
	static function ( string $hook ): void {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'fck-fields', FCK_CORE_URL . 'admin/fields.css', array(), FCK_CORE_VERSION );
		wp_enqueue_script( 'fck-fields', FCK_CORE_URL . 'admin/fields.js', array( 'jquery' ), FCK_CORE_VERSION, true );
	}
);
