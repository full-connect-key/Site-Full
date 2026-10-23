<?php
/**
 * Painel: organização dos menus, pendências de conteúdo e ajustes de usabilidade.
 */

defined( 'ABSPATH' ) || exit;

/** Pendências detectadas no banco (espelha docs/CONTENT_GAPS.md). */
function fck_content_gaps(): array {
	$gaps  = array();
	$add   = static function ( string $p, string $item, string $where ) use ( &$gaps ): void {
		$gaps[] = array( 'priority' => $p, 'item' => $item, 'where' => $where );
	};
	$brand = fck_site( 'brand' );
	if ( empty( $brand['logo_dark'] ) || empty( $brand['logo_light'] ) ) {
		$add( 'P0', 'Logos oficiais da Full (SVG)', 'Cabeçalho e rodapé (hoje: nome em texto)' );
	}
	if ( empty( $brand['logo_compact_dark'] ) ) {
		$add( 'P1', 'Logo compacto da Full para fundo escuro (SVG)', 'Cabeçalho (hoje: logo completo)' );
	}
	foreach ( fck_posts( 'fck_platform' ) as $p ) {
		$identity = fck_meta( $p->ID, 'identity' );
		if ( empty( $identity['logo'] ) || empty( $identity['logo_light'] ) ) {
			$add( 'P0', 'Logo oficial (escuro e claro) — ' . $p->post_title, 'Páginas do ecossistema (hoje: nome em texto)' );
		}
	}
	foreach ( array( 'privacy' => 'Política de Privacidade', 'cookies' => 'Política de Cookies' ) as $key => $label ) {
		$pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'meta_key' => '_fck_seed_key', 'meta_value' => 'page:' . $key, 'fields' => 'ids' ) ); // phpcs:ignore
		if ( ! $pages ) {
			$add( 'P0', $label . ' (texto jurídico aprovado)', 'Rodapé e consentimento do formulário' );
		}
	}
	if ( ! fck_contact_settings()['recipients'] ) {
		$add( 'P0', 'Destinatários do formulário de contato', 'Contato (mensagens ficam só no painel)' );
	}
	if ( count( fck_posts( 'fck_partner' ) ) < 3 ) {
		$add( 'P1', 'Três sócios (nome, cargo, bio, foto)', 'Home › Os Sócios (seção oculta)' );
	}
	if ( ! fck_posts( 'fck_team' ) ) {
		$add( 'P1', 'Time (fotos e frases "Conecta X a Y.")', 'Home › O Time (seção oculta)' );
	}
	if ( ! fck_posts( 'fck_testimonial', array( 'meta_query' => array() ) ) ) {
		$add( 'P1', 'Depoimentos autorizados', 'Home › Depoimentos (seção oculta)' );
	}
	if ( ! fck_posts( 'fck_case' ) ) {
		$add( 'P1', 'Cases com KVs autorizados', 'Home › Cases e página Cases' );
	}
	if ( ! fck_posts( 'fck_client' ) ) {
		$add( 'P1', 'Logos de clientes autorizados', 'Home › faixa de logos (oculta)' );
	}
	if ( ! fck_posts( 'fck_article' ) ) {
		$add( 'P2', 'Artigos reais', 'Home › Insights e página Insights' );
	}
	if ( ! fck_posts( 'fck_press' ) ) {
		$add( 'P2', 'Matérias de imprensa reais', 'Full na Mídia (estado vazio)' );
	}
	$contacts = fck_site( 'contacts' );
	if ( empty( $contacts['email'] ) && empty( $contacts['phone'] ) && empty( $contacts['socials'] ) ) {
		$add( 'P2', 'Canais oficiais de contato e redes', 'Rodapé' );
	}
	return $gaps;
}

add_action(
	'wp_dashboard_setup',
	static function (): void {
		wp_add_dashboard_widget(
			'fck_gaps',
			'Pendências de conteúdo do site',
			static function (): void {
				$gaps = fck_content_gaps();
				if ( ! $gaps ) {
					echo '<p>Nenhuma pendência detectada.</p>';
					return;
				}
				echo '<p>Enquanto estes itens não forem preenchidos, as seções correspondentes ficam ocultas ou usam um substituto neutro.</p><table class="widefat striped"><tbody>';
				foreach ( $gaps as $g ) {
					printf( '<tr><td><strong>%s</strong></td><td>%s<br><span class="description">%s</span></td></tr>', esc_html( $g['priority'] ), esc_html( $g['item'] ), esc_html( $g['where'] ) );
				}
				echo '</tbody></table>';
			}
		);
	}
);

// Lista de configurações globais: sem "Adicionar novo".
add_action(
	'admin_head-edit.php',
	static function (): void {
		if ( 'fck_global' === get_current_screen()?->post_type ) {
			echo '<style>.page-title-action{display:none}</style>';
		}
	}
);

// Ordem manual (menu_order) como padrão nas listas do painel.
add_action(
	'pre_get_posts',
	static function ( WP_Query $q ): void {
		if ( ! is_admin() || ! $q->is_main_query() ) {
			return;
		}
		$type = $q->get( 'post_type' );
		if ( in_array( $type, array( 'fck_platform', 'fck_sector', 'fck_client', 'fck_partner', 'fck_team', 'fck_testimonial', 'fck_metric', 'fck_case' ), true ) && ! $q->get( 'orderby' ) ) {
			$q->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
		}
	}
);

// Coluna "Modelo" na lista de páginas.
add_filter(
	'manage_page_posts_columns',
	static fn( array $c ) => array_slice( $c, 0, 2, true ) + array( 'fck_template' => 'Modelo' ) + array_slice( $c, 2, null, true )
);
add_action(
	'manage_page_posts_custom_column',
	static function ( string $col, int $id ): void {
		if ( 'fck_template' === $col ) {
			echo esc_html( fck_page_templates()[ fck_template_of( $id ) ] ?? '—' );
		}
	},
	10,
	2
);

// Rodapé do painel.
add_filter( 'admin_footer_text', static fn() => 'Painel do site Full Connect Key. Dúvidas de edição: veja o Guia do Editor (docs/EDITOR_GUIDE.md).' );
