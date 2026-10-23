<?php
/**
 * Plugin Name:       Full Connect Key — Núcleo
 * Description:       Modelo de conteúdo, campos editáveis, papéis, formulário de contato, SEO e conteúdo inicial do site da Full Connect Key. Mantenha ativo: o tema depende dele.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Full Connect Key
 * Text Domain:       fck-core
 * License:           Proprietário
 */

defined( 'ABSPATH' ) || exit;

define( 'FCK_CORE_VERSION', '1.0.0' );
define( 'FCK_CORE_FILE', __FILE__ );
define( 'FCK_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'FCK_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once FCK_CORE_DIR . 'includes/helpers.php';
require_once FCK_CORE_DIR . 'includes/class-fck-fields.php';
require_once FCK_CORE_DIR . 'includes/content-model.php';
require_once FCK_CORE_DIR . 'includes/meta-boxes.php';
require_once FCK_CORE_DIR . 'includes/media.php';
require_once FCK_CORE_DIR . 'includes/roles.php';
require_once FCK_CORE_DIR . 'includes/redirects.php';
require_once FCK_CORE_DIR . 'includes/contact.php';
require_once FCK_CORE_DIR . 'includes/seo.php';
require_once FCK_CORE_DIR . 'includes/security.php';
require_once FCK_CORE_DIR . 'includes/admin.php';
require_once FCK_CORE_DIR . 'includes/seed.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once FCK_CORE_DIR . 'includes/cli.php';
}

register_activation_hook( __FILE__, 'fck_core_activate' );
register_deactivation_hook( __FILE__, 'fck_core_deactivate' );

function fck_core_activate(): void {
	fck_register_content_model();
	fck_sync_roles();
	fck_schedule_retention();
	flush_rewrite_rules();
}

function fck_core_deactivate(): void {
	wp_clear_scheduled_hook( 'fck_contact_retention' );
	flush_rewrite_rules();
}
