<?php
/**
 * Suportes do tema, locais de menu e carregamento de assets.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function (): void {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		remove_theme_support( 'core-block-patterns' );
		register_nav_menus(
			array(
				'primary' => 'Menu principal (com submenu)',
				'footer'  => 'Rodapé (itens de 1º nível = títulos dos grupos)',
				'legal'   => 'Links legais (rodapé)',
			)
		);
	}
);

/** Assets: um CSS e um JS (módulo que carrega cada interação sob demanda). */
add_action(
	'wp_enqueue_scripts',
	static function (): void {
		$css = FCK_THEME_DIR . '/assets/css/main.css';
		$js  = FCK_THEME_DIR . '/assets/js/main.js';
		wp_enqueue_style( 'fck-main', FCK_THEME_URL . '/assets/css/main.css', array(), (string) filemtime( $css ) );
		wp_enqueue_script_module( 'fck-main', FCK_THEME_URL . '/assets/js/main.js', array(), (string) filemtime( $js ) );

		// Sem estilos de blocos/globais do core no site (o tema tem os próprios).
		if ( ! is_singular( 'fck_article' ) ) {
			wp_dequeue_style( 'wp-block-library' );
		}
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	},
	20
);

// Pré-carrega a fonte de display (usada acima da dobra).
add_action(
	'wp_head',
	static function (): void {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( FCK_THEME_URL . '/assets/fonts/BricolageGrotesque-latin-variable.woff2' )
		);
	},
	1
);

// Tamanho de conteúdo para embeds.
add_action( 'after_setup_theme', static function (): void { $GLOBALS['content_width'] = 760; } );

// Remove o prefixo "Privado:"/"Protegido:" dos títulos.
add_filter( 'private_title_format', static fn() => '%s' );
add_filter( 'protected_title_format', static fn() => '%s' );

// Classe no <body> com o modelo/página para estilos específicos.
add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'no-js';
		if ( is_page() ) {
			$tpl = fck_template_of( (int) get_queried_object_id() );
			if ( $tpl ) {
				$classes[] = 'tpl-' . $tpl;
			}
		}
		return $classes;
	}
);
