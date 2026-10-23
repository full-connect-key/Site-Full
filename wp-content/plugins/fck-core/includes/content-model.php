<?php
/**
 * Tipos de conteúdo, taxonomias e páginas com função.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Modelos de página (arquivos em themes/fullconnectkey/templates/). Uma
 * página que usa o modelo assume a "função" correspondente; os endereços de
 * plataformas, cases e artigos derivam do slug da página com a função.
 */
function fck_page_templates(): array {
	return array(
		'home'      => 'Home',
		'sm'        => 'Método S&M',
		'ecosystem' => 'Ecossistema Full',
		'cases'     => 'Cases (listagem)',
		'insights'  => 'Insights (listagem)',
		'press'     => 'Full na Mídia',
		'contact'   => 'Contato',
		'legal'     => 'Texto jurídico (privacidade/cookies)',
	);
}

/** Slugs técnicos usados somente enquanto a página com a função não existe. */
function fck_route_fallbacks(): array {
	return array(
		'ecosystem' => 'ecossistema',
		'cases'     => 'cases',
		'insights'  => 'insights',
	);
}

function fck_route_bases(): array {
	$saved = get_option( 'fck_route_bases' );
	return wp_parse_args( is_array( $saved ) ? $saved : array(), fck_route_fallbacks() );
}

add_action( 'init', 'fck_register_content_model', 5 );

function fck_register_content_model(): void {
	$bases = fck_route_bases();

	$labels = static function ( string $singular, string $plural, bool $fem = false ): array {
		$novo = $fem ? 'Nova' : 'Novo';
		return array(
			'name'               => $plural,
			'singular_name'      => $singular,
			'add_new'            => "Adicionar {$singular}",
			'add_new_item'       => "Adicionar {$singular}",
			'new_item'           => "{$novo} {$singular}",
			'edit_item'          => "Editar {$singular}",
			'view_item'          => "Ver {$singular}",
			'all_items'          => $plural,
			'search_items'       => "Buscar {$plural}",
			'not_found'          => 'Nada encontrado.',
			'not_found_in_trash' => 'Nada na lixeira.',
			'menu_name'          => $plural,
		);
	};

	// Conteúdos com página própria.
	register_post_type(
		'fck_platform',
		array(
			'labels'        => $labels( 'Plataforma', 'Plataformas', true ),
			'description'   => 'Full Hub, Full Dash, Full Leads, FullMix e FullMatch. S&M é método, não plataforma.',
			'public'        => true,
			'show_in_rest'  => false,
			'menu_position' => 21,
			'menu_icon'     => 'dashicons-networking',
			'supports'      => array( 'title', 'revisions', 'page-attributes' ),
			'rewrite'       => array( 'slug' => $bases['ecosystem'], 'with_front' => false ),
			'has_archive'   => false,
		)
	);

	register_post_type(
		'fck_case',
		array(
			'labels'        => $labels( 'Case', 'Cases' ),
			'description'   => 'Publicar um case cria a página, coloca na listagem, nos filtros e no sitemap.',
			'public'        => true,
			'show_in_rest'  => false,
			'menu_position' => 22,
			'menu_icon'     => 'dashicons-portfolio',
			'supports'      => array( 'title', 'revisions', 'page-attributes' ),
			'rewrite'       => array( 'slug' => $bases['cases'], 'with_front' => false ),
			'has_archive'   => false,
		)
	);

	register_post_type(
		'fck_article',
		array(
			'labels'        => $labels( 'Artigo', 'Insights' ),
			'description'   => 'Artigos do Insights Full.',
			'public'        => true,
			'show_in_rest'  => true, // editor de blocos para o corpo do artigo.
			'menu_position' => 23,
			'menu_icon'     => 'dashicons-welcome-write-blog',
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
			'rewrite'       => array( 'slug' => $bases['insights'], 'with_front' => false ),
			'has_archive'   => false,
			'template'      => array( array( 'core/paragraph' ) ),
		)
	);

	register_taxonomy(
		'fck_article_cat',
		array( 'fck_article' ),
		array(
			'labels'            => array(
				'name'          => 'Categorias',
				'singular_name' => 'Categoria',
				'add_new_item'  => 'Adicionar categoria',
				'edit_item'     => 'Editar categoria',
				'menu_name'     => 'Categorias',
			),
			'public'            => false,
			'publicly_queryable' => false,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'rewrite'           => false,
		)
	);

	// Conteúdos de apoio (sem página própria).
	$support = array(
		'fck_press'       => array( 'Matéria', 'Full na Mídia', 'dashicons-megaphone', 24, true ),
		'fck_sector'      => array( 'Setor', 'Setores', 'dashicons-store', 25, false ),
		'fck_client'      => array( 'Cliente', 'Clientes', 'dashicons-groups', 26, false ),
		'fck_metric'      => array( 'Métrica', 'Métricas institucionais', 'dashicons-chart-bar', 27, true ),
		'fck_partner'     => array( 'Sócio', 'Sócios', 'dashicons-businessperson', 28, false ),
		'fck_team'        => array( 'Pessoa do time', 'Time', 'dashicons-admin-users', 29, true ),
		'fck_testimonial' => array( 'Depoimento', 'Depoimentos', 'dashicons-format-quote', 30, false ),
		'fck_author'      => array( 'Autor', 'Autores', 'dashicons-id', 31, false ),
	);
	foreach ( $support as $type => [ $singular, $plural, $icon, $pos, $fem ] ) {
		register_post_type(
			$type,
			array(
				'labels'              => $labels( $singular, $plural, $fem ),
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_rest'        => false,
				'show_in_nav_menus'   => false,
				'menu_position'       => $pos,
				'menu_icon'           => $icon,
				'supports'            => array( 'title', 'revisions', 'page-attributes' ),
				'rewrite'             => false,
			)
		);
	}

	// Mensagens do formulário de contato: somente administradores.
	register_post_type(
		'fck_submission',
		array(
			'labels'              => $labels( 'Mensagem de contato', 'Mensagens de contato', true ),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_rest'        => false,
			'show_in_nav_menus'   => false,
			'menu_position'       => 80,
			'menu_icon'           => 'dashicons-email-alt',
			'supports'            => array( 'title' ),
			'capability_type'     => 'fck_submission',
			'map_meta_cap'        => false,
			'capabilities'        => array(
				'edit_post'          => 'manage_options',
				'read_post'          => 'manage_options',
				'delete_post'        => 'manage_options',
				'edit_posts'         => 'manage_options',
				'edit_others_posts'  => 'manage_options',
				'publish_posts'      => 'do_not_allow',
				'read_private_posts' => 'manage_options',
				'delete_posts'       => 'manage_options',
				'create_posts'       => 'do_not_allow',
			),
			'rewrite'             => false,
		)
	);

	// Configurações globais com histórico de versões (registros únicos).
	register_post_type(
		'fck_global',
		array(
			'labels'             => array(
				'name'          => 'Configurações do site',
				'singular_name' => 'Configuração',
				'edit_item'     => 'Editar configuração',
				'menu_name'     => 'Configurações do site',
				'all_items'     => 'Configurações do site',
			),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_rest'       => false,
			'show_in_nav_menus'  => false,
			'menu_position'      => 81,
			'menu_icon'          => 'dashicons-admin-settings',
			'supports'           => array( 'title', 'revisions' ),
			'capability_type'    => 'page',
			'map_meta_cap'       => true,
			'capabilities'       => array( 'create_posts' => 'do_not_allow' ),
			'rewrite'            => false,
		)
	);
}

/** Desliga o tipo "post" padrão (os artigos usam fck_article). */
add_action(
	'admin_menu',
	static function (): void {
		remove_menu_page( 'edit.php' );
		remove_menu_page( 'edit-comments.php' );
	}
);
add_filter( 'comments_open', '__return_false' );
add_filter( 'pings_open', '__return_false' );

// -------------------------------------------------------------------------
// Páginas com função
// -------------------------------------------------------------------------

/** Página que usa o modelo indicado (publicada; em preview aceita rascunho). */
function fck_role_page( string $role ): ?WP_Post {
	static $cache = array();
	if ( array_key_exists( $role, $cache ) ) {
		return $cache[ $role ];
	}
	$statuses = array( 'publish' );
	if ( is_preview() && current_user_can( 'edit_pages' ) ) {
		$statuses[] = 'draft';
	}
	$pages          = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => $statuses,
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template', // phpcs:ignore -- consulta pequena e indexada.
			'meta_value'     => 'templates/' . $role . '.php', // phpcs:ignore
			'orderby'        => 'menu_order ID',
			'order'          => 'ASC',
		)
	);
	$cache[ $role ] = $pages[0] ?? null;
	return $cache[ $role ];
}

/** URL da página com função; null se ela não estiver publicada. */
function fck_role_url( string $role ): ?string {
	if ( 'home' === $role ) {
		return home_url( '/' );
	}
	$page = fck_role_page( $role );
	return $page ? get_permalink( $page ) : null;
}

/** Template (chave) usado por uma página. */
function fck_template_of( int $post_id ): string {
	$file = (string) get_post_meta( $post_id, '_wp_page_template', true );
	return preg_match( '#^templates/([a-z]+)\.php$#', $file, $m ) ? $m[1] : '';
}

/**
 * Mantém as bases de URL de plataformas/cases/artigos sincronizadas com os
 * slugs das páginas com função. Quando mudam, as regras de URL são refeitas e
 * um redirect de prefixo preserva os endereços antigos.
 */
add_action( 'save_post_page', 'fck_sync_route_bases', 20 );
add_action( 'trashed_post', 'fck_sync_route_bases' );
add_action( 'untrashed_post', 'fck_sync_route_bases' );

function fck_sync_route_bases(): void {
	$old   = fck_route_bases();
	$bases = fck_route_fallbacks();
	foreach ( array_keys( $bases ) as $role ) {
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'meta_key'       => '_wp_page_template', // phpcs:ignore
				'meta_value'     => 'templates/' . $role . '.php', // phpcs:ignore
				'orderby'        => 'menu_order ID',
				'order'          => 'ASC',
			)
		);
		if ( $pages ) {
			$bases[ $role ] = get_page_uri( $pages[0] );
		}
	}
	if ( $bases === $old ) {
		return;
	}
	foreach ( $bases as $role => $base ) {
		if ( ( $old[ $role ] ?? '' ) !== $base && ! empty( $old[ $role ] ) ) {
			fck_add_redirect( '/' . $old[ $role ] . '/', '/' . $base . '/', 'prefix' );
		}
	}
	update_option( 'fck_route_bases', $bases, true );
	update_option( 'fck_flush_rewrites', 1, true );
}

add_action(
	'init',
	static function (): void {
		if ( get_option( 'fck_flush_rewrites' ) ) {
			delete_option( 'fck_flush_rewrites' );
			flush_rewrite_rules( false );
		}
	},
	99
);
