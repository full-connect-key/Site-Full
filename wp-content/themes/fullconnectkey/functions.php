<?php
/**
 * Tema Full Connect Key.
 * Layout e interações ficam no tema; todo texto vem do painel (plugin fck-core).
 */

defined( 'ABSPATH' ) || exit;

define( 'FCK_THEME_VERSION', '1.0.0' );
define( 'FCK_THEME_DIR', get_template_directory() );
define( 'FCK_THEME_URL', get_template_directory_uri() );

// O tema depende do plugin "Full Connect Key — Núcleo".
if ( ! function_exists( 'fck_meta' ) ) {
	add_action(
		'admin_notices',
		static fn() => print '<div class="notice notice-error"><p>Ative o plugin <strong>Full Connect Key — Núcleo</strong>: o tema depende dele.</p></div>'
	);
	if ( ! is_admin() ) {
		add_filter( 'template_include', static fn() => FCK_THEME_DIR . '/plugin-missing.php' );
	}
	return;
}

require_once FCK_THEME_DIR . '/inc/setup.php';
require_once FCK_THEME_DIR . '/inc/template-tags.php';
require_once FCK_THEME_DIR . '/inc/navigation.php';
