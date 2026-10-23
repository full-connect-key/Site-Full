<?php
/**
 * Importação do conteúdo inicial — idempotente.
 * Cada registro tem uma chave estável (_fck_seed_key). Rodar de novo só cria
 * o que não existe: nunca sobrescreve edições feitas no painel.
 * A opção "reset" (apenas em ambiente local) recria tudo a partir do seed.
 */

defined( 'ABSPATH' ) || exit;

require_once FCK_CORE_DIR . 'seed/content.php';

final class FCK_Seeder {

	private array $ids = array( 'page' => array(), 'platform' => array() );

	/** @var string[] */
	public array $log = array();

	public function __construct( private bool $reset = false ) {
		if ( $this->reset && 'local' !== wp_get_environment_type() ) {
			$this->reset = false;
			$this->log[] = 'Reset ignorado: permitido apenas em ambiente local.';
		}
	}

	public function run(): array {
		$content = fck_seed_content();

		$this->settings();
		foreach ( $content['pages'] as $key => $page ) {
			$this->ids['page'][ $key ] = $this->ensure_post( 'page', 'page:' . $key, $page );
		}
		foreach ( $content['platforms'] as $key => $platform ) {
			$this->ids['platform'][ $key ] = $this->ensure_post( 'fck_platform', 'platform:' . $key, $platform + array( 'status' => 'publish' ) );
		}
		foreach ( $content['sectors'] as $slug => $sector ) {
			$this->ensure_post( 'fck_sector', 'sector:' . $slug, $sector + array( 'slug' => $slug, 'status' => 'publish' ) );
		}
		foreach ( $content['categories'] as $slug => $name ) {
			if ( ! term_exists( $slug, 'fck_article_cat' ) ) {
				wp_insert_term( $name, 'fck_article_cat', array( 'slug' => $slug ) );
				$this->log[] = "Categoria criada: {$name}";
			}
		}

		// Segunda passagem: metas (links precisam dos IDs já criados).
		foreach ( $content['pages'] as $key => $page ) {
			$this->apply_meta( $this->ids['page'][ $key ], $page['meta'] ?? array() );
		}
		foreach ( $content['platforms'] as $key => $platform ) {
			$this->apply_meta( $this->ids['platform'][ $key ], $platform['meta'] ?? array() );
		}

		$this->globals( $content['site'], $content['ui'] );
		$this->menus( $content['menus'] );

		// Home como página inicial.
		if ( ! empty( $this->ids['page']['home'] ) && ( $this->reset || ! (int) get_option( 'page_on_front' ) ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $this->ids['page']['home'] );
		}

		fck_sync_route_bases();
		flush_rewrite_rules( false );
		$this->log[] = 'Concluído.';
		return $this->log;
	}

	private function settings(): void {
		if ( '/%postname%/' !== get_option( 'permalink_structure' ) ) {
			update_option( 'permalink_structure', '/%postname%/' );
			$this->log[] = 'Links permanentes: /%postname%/';
		}
		update_option( 'timezone_string', get_option( 'timezone_string' ) ?: 'America/Sao_Paulo' );
		update_option( 'date_format', 'd/m/Y' );
		update_option( 'default_comment_status', 'closed' );
		update_option( 'default_ping_status', 'closed' );
		update_option( 'blogdescription', '' );
	}

	private function find( string $type, string $key ): ?int {
		$found = get_posts(
			array(
				'post_type'      => $type,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_fck_seed_key', // phpcs:ignore
				'meta_value'     => $key, // phpcs:ignore
			)
		);
		return $found ? (int) $found[0] : null;
	}

	private function ensure_post( string $type, string $key, array $def ): int {
		$existing = $this->find( $type, $key );
		if ( $existing && ! $this->reset ) {
			return $existing;
		}
		$data = array(
			'post_type'   => $type,
			'post_title'  => $def['title'],
			'post_name'   => $def['slug'] ?? sanitize_title( $def['title'] ),
			'post_status' => $def['status'] ?? 'publish',
			'menu_order'  => (int) ( $def['order'] ?? 0 ),
		);
		if ( $existing ) {
			$data['ID'] = $existing;
			wp_update_post( $data );
			$id = $existing;
			foreach ( array_keys( get_post_meta( $id ) ) as $meta_key ) {
				if ( str_starts_with( $meta_key, '_fck_' ) && '_fck_seed_key' !== $meta_key ) {
					delete_post_meta( $id, $meta_key );
				}
			}
		} else {
			$id = (int) wp_insert_post( $data );
		}
		update_post_meta( $id, '_fck_seed_key', $key );
		update_post_meta( $id, '_fck_seeded_new', 1 );
		if ( ! empty( $def['template'] ) ) {
			update_post_meta( $id, '_wp_page_template', 'templates/' . $def['template'] . '.php' );
		}
		$this->log[] = ( $existing ? 'Recriado' : 'Criado' ) . ": {$def['title']} ({$type})";
		return $id;
	}

	/** Grava metas somente em registros recém-criados pelo seed. */
	private function apply_meta( int $id, array $meta ): void {
		if ( ! get_post_meta( $id, '_fck_seeded_new', true ) ) {
			return;
		}
		foreach ( $meta as $group => $values ) {
			update_post_meta( $id, '_fck_' . $group, wp_slash( $this->resolve_links( $values ) ) );
		}
		delete_post_meta( $id, '_fck_seeded_new' );
	}

	/** Converte {'label','to'} em link do painel. */
	private function resolve_links( array $values ): array {
		foreach ( $values as $k => $v ) {
			if ( is_array( $v ) && array_key_exists( 'to', $v ) && array_key_exists( 'label', $v ) ) {
				$values[ $k ] = $this->link( $v['label'], $v['to'] );
			} elseif ( is_array( $v ) ) {
				$values[ $k ] = $this->resolve_links( $v );
			}
		}
		return $values;
	}

	private function target_id( string $to ): ?int {
		[ $kind, $key ] = array_pad( explode( ':', $to, 2 ), 2, '' );
		return $this->ids[ $kind ][ $key ] ?? null;
	}

	private function link( string $label, string $to ): array {
		if ( str_starts_with( $to, 'url:' ) ) {
			return array( 'label' => $label, 'type' => 'url', 'post' => null, 'url' => substr( $to, 4 ) );
		}
		return array( 'label' => $label, 'type' => 'post', 'post' => $this->target_id( $to ), 'url' => '' );
	}

	private function globals( array $site, array $ui ): void {
		$ui_groups = array();
		foreach ( $ui as $group => $values ) {
			$ui_groups[ 'ui_' . $group ] = $values;
		}
		foreach ( array( 'site' => $site, 'ui' => $ui_groups ) as $key => $groups ) {
			$id = $this->find( 'fck_global', 'global:' . $key );
			if ( ! $id ) {
				$id          = (int) wp_insert_post(
					array(
						'post_type'   => 'fck_global',
						'post_title'  => fck_global_keys()[ $key ],
						'post_name'   => $key,
						'post_status' => 'publish',
					)
				);
				$this->log[] = 'Criado: ' . fck_global_keys()[ $key ];
				update_post_meta( $id, '_fck_seed_key', 'global:' . $key );
			}
			// Completa apenas chaves ausentes (novos campos), sem sobrescrever edições.
			foreach ( $groups as $group => $values ) {
				$current = get_post_meta( $id, '_fck_' . $group, true );
				$current = is_array( $current ) && ! $this->reset ? $current : array();
				$merged  = $current + $this->resolve_links( $values );
				if ( $merged !== $current ) {
					update_post_meta( $id, '_fck_' . $group, wp_slash( $merged ) );
				}
			}
		}
	}

	private function menus( array $menus ): void {
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		foreach ( $menus as $location => $menu ) {
			$existing = wp_get_nav_menu_object( $menu['name'] );
			if ( $existing && ! $this->reset ) {
				if ( empty( $locations[ $location ] ) ) {
					$locations[ $location ] = $existing->term_id;
				}
				continue;
			}
			if ( $existing ) {
				wp_delete_nav_menu( $existing->term_id );
			}
			$menu_id = (int) wp_create_nav_menu( $menu['name'] );
			$this->add_menu_items( $menu_id, $menu['items'], 0 );
			$locations[ $location ] = $menu_id;
			$this->log[]            = "Menu criado: {$menu['name']}";
		}
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	private function add_menu_items( int $menu_id, array $items, int $parent ): void {
		foreach ( $items as $pos => $item ) {
			$args = array(
				'menu-item-title'     => $item['label'],
				'menu-item-status'    => 'publish',
				'menu-item-position'  => $pos + 1,
				'menu-item-parent-id' => $parent,
			);
			if ( 'group' === $item['to'] ) {
				$args['menu-item-type'] = 'custom';
				$args['menu-item-url']  = '#';
			} elseif ( str_starts_with( $item['to'], 'url:' ) ) {
				$args['menu-item-type'] = 'custom';
				$args['menu-item-url']  = substr( $item['to'], 4 );
			} else {
				$target                      = $this->target_id( $item['to'] );
				$args['menu-item-type']      = 'post_type';
				$args['menu-item-object']    = str_starts_with( $item['to'], 'platform:' ) ? 'fck_platform' : 'page';
				$args['menu-item-object-id'] = $target;
			}
			$item_id = (int) wp_update_nav_menu_item( $menu_id, 0, $args );
			if ( ! empty( $item['children'] ) ) {
				$this->add_menu_items( $menu_id, $item['children'], $item_id );
			}
		}
	}
}

function fck_run_seed( bool $reset = false ): array {
	return ( new FCK_Seeder( $reset ) )->run();
}

// Ferramentas › Conteúdo inicial
add_action(
	'admin_menu',
	static function (): void {
		add_management_page( 'Conteúdo inicial', 'Conteúdo inicial', 'manage_options', 'fck-seed', 'fck_render_seed_page' );
	}
);

function fck_render_seed_page(): void {
	$log = null;
	if ( isset( $_POST['fck_seed'] ) && check_admin_referer( 'fck_seed' ) && current_user_can( 'manage_options' ) ) {
		$log = fck_run_seed( false );
	}
	echo '<div class="wrap"><h1>Conteúdo inicial</h1>';
	echo '<p>Cria páginas, plataformas, setores, categorias, menus e textos de interface a partir dos textos aprovados do briefing. <strong>Pode ser executado mais de uma vez</strong>: só cria o que ainda não existe e nunca sobrescreve o que foi editado.</p>';
	if ( $log ) {
		echo '<div class="notice notice-success"><ul>';
		foreach ( $log as $line ) {
			echo '<li>' . esc_html( $line ) . '</li>';
		}
		echo '</ul></div>';
	}
	echo '<form method="post">';
	wp_nonce_field( 'fck_seed' );
	submit_button( 'Importar conteúdo inicial', 'primary', 'fck_seed' );
	echo '</form></div>';
}
