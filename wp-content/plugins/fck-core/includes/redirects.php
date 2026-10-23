<?php
/**
 * Redirecionamentos 301.
 * - Automáticos quando o endereço de uma página publicada muda.
 * - Plataformas, cases e artigos usam o redirect nativo do WordPress para
 *   slugs antigos; mudanças na página-base geram redirect de prefixo.
 * - Manuais (ex.: endereços do site atual) em Ferramentas › Redirecionamentos.
 */

defined( 'ABSPATH' ) || exit;

/** @return array<int,array{from:string,to:string,match:string}> */
function fck_redirects(): array {
	$list = get_option( 'fck_redirects', array() );
	return is_array( $list ) ? $list : array();
}

function fck_normalize_path( string $path ): string {
	$path = '/' . ltrim( (string) wp_parse_url( $path, PHP_URL_PATH ), '/' );
	return '/' === $path ? $path : trailingslashit( $path );
}

function fck_add_redirect( string $from, string $to, string $match = 'exact' ): void {
	$from = fck_normalize_path( $from );
	$to   = preg_match( '#^https?://#', $to ) ? $to : fck_normalize_path( $to );
	if ( $from === $to ) {
		return;
	}
	$list = array();
	foreach ( fck_redirects() as $r ) {
		if ( $r['from'] === $from || $r['from'] === $to ) {
			continue; // substitui o anterior; remove o que conflitaria com o novo endereço.
		}
		if ( $r['to'] === $from ) {
			$r['to'] = $to; // evita cadeias A → B → C.
		}
		$list[] = $r;
	}
	$list[] = array( 'from' => $from, 'to' => $to, 'match' => 'prefix' === $match ? 'prefix' : 'exact' );
	update_option( 'fck_redirects', $list, false );
}

/** Páginas publicadas: redirect automático quando o endereço muda. */
add_action(
	'post_updated',
	static function ( int $post_id, WP_Post $after, WP_Post $before ): void {
		if ( 'page' !== $after->post_type || 'publish' !== $before->post_status || wp_is_post_revision( $post_id ) ) {
			return;
		}
		$old = '/' . get_page_uri( $before ) . '/';
		if ( $before->post_parent !== $after->post_parent || $before->post_name !== $after->post_name ) {
			$new = '/' . get_page_uri( $after ) . '/';
			if ( $old !== $new && 'publish' === $after->post_status ) {
				fck_add_redirect( $old, $new );
			}
		}
	},
	10,
	3
);

add_action(
	'template_redirect',
	static function (): void {
		if ( ! is_404() ) {
			return;
		}
		$path = fck_normalize_path( (string) ( $_SERVER['REQUEST_URI'] ?? '/' ) ); // phpcs:ignore
		$qs   = (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_QUERY ); // phpcs:ignore
		foreach ( fck_redirects() as $r ) {
			$target = null;
			if ( 'exact' === $r['match'] && $r['from'] === $path ) {
				$target = $r['to'];
			} elseif ( 'prefix' === $r['match'] && str_starts_with( $path, $r['from'] ) ) {
				$target = rtrim( $r['to'], '/' ) . '/' . substr( $path, strlen( $r['from'] ) );
			}
			if ( null !== $target ) {
				$url = preg_match( '#^https?://#', $target ) ? $target : home_url( $target );
				wp_safe_redirect( $url . ( $qs ? '?' . $qs : '' ), 301, 'fck-core' );
				exit;
			}
		}
	},
	1
);

// Destinos externos dos redirects manuais são permitidos pelo wp_safe_redirect.
add_filter(
	'allowed_redirect_hosts',
	static function ( array $hosts ): array {
		foreach ( fck_redirects() as $r ) {
			if ( preg_match( '#^https?://#', $r['to'] ) ) {
				$hosts[] = (string) wp_parse_url( $r['to'], PHP_URL_HOST );
			}
		}
		return $hosts;
	}
);

// ---------------------------------------------------------------------------
// Tela Ferramentas › Redirecionamentos
// ---------------------------------------------------------------------------

add_action(
	'admin_menu',
	static function (): void {
		add_management_page( 'Redirecionamentos', 'Redirecionamentos', 'edit_pages', 'fck-redirects', 'fck_render_redirects_page' );
	}
);

add_action(
	'admin_post_fck_redirects',
	static function (): void {
		if ( ! current_user_can( 'edit_pages' ) ) {
			wp_die( 'Sem permissão.', 403 );
		}
		check_admin_referer( 'fck_redirects' );
		$action = sanitize_key( $_POST['fck_action'] ?? '' );
		if ( 'add' === $action ) {
			$from = sanitize_text_field( wp_unslash( $_POST['from'] ?? '' ) );
			$to   = sanitize_text_field( wp_unslash( $_POST['to'] ?? '' ) );
			if ( str_starts_with( $from, '/' ) && ( str_starts_with( $to, '/' ) || preg_match( '#^https://#', $to ) ) ) {
				fck_add_redirect( $from, $to, sanitize_key( $_POST['match'] ?? 'exact' ) );
			}
		} elseif ( 'delete' === $action ) {
			$from = fck_normalize_path( sanitize_text_field( wp_unslash( $_POST['from'] ?? '' ) ) );
			update_option( 'fck_redirects', array_values( array_filter( fck_redirects(), fn( $r ) => $r['from'] !== $from ) ), false );
		}
		wp_safe_redirect( admin_url( 'tools.php?page=fck-redirects' ) );
		exit;
	}
);

function fck_render_redirects_page(): void {
	echo '<div class="wrap"><h1>Redirecionamentos</h1>';
	echo '<p>Criados automaticamente quando o endereço de uma página publicada muda. Use também para preservar endereços do site atual (SEO).</p>';
	echo '<table class="widefat striped"><thead><tr><th>De</th><th>Para</th><th>Tipo</th><th></th></tr></thead><tbody>';
	$list = fck_redirects();
	if ( ! $list ) {
		echo '<tr><td colspan="4">Nenhum redirecionamento.</td></tr>';
	}
	foreach ( $list as $r ) {
		printf(
			'<tr><td><code>%s</code></td><td><code>%s</code></td><td>%s</td><td><form method="post" action="%s">%s<input type="hidden" name="action" value="fck_redirects"><input type="hidden" name="fck_action" value="delete"><input type="hidden" name="from" value="%s"><button class="button-link-delete">Excluir</button></form></td></tr>',
			esc_html( $r['from'] ),
			esc_html( $r['to'] ),
			'prefix' === $r['match'] ? 'Prefixo (inclui subpáginas)' : 'Exato',
			esc_url( admin_url( 'admin-post.php' ) ),
			wp_nonce_field( 'fck_redirects', '_wpnonce', true, false ), // phpcs:ignore
			esc_attr( $r['from'] )
		);
	}
	echo '</tbody></table>';
	printf( '<h2>Adicionar</h2><form method="post" action="%s">', esc_url( admin_url( 'admin-post.php' ) ) );
	wp_nonce_field( 'fck_redirects' );
	echo '<input type="hidden" name="action" value="fck_redirects"><input type="hidden" name="fck_action" value="add">';
	echo '<p><label>De (caminho antigo) <input type="text" name="from" placeholder="/servicos/branding" required class="regular-text"></label></p>';
	echo '<p><label>Para <input type="text" name="to" placeholder="/cases ou https://..." required class="regular-text"></label></p>';
	echo '<p><label>Tipo <select name="match"><option value="exact">Exato</option><option value="prefix">Prefixo</option></select></label></p>';
	submit_button( 'Adicionar redirecionamento' );
	echo '</form></div>';
}
