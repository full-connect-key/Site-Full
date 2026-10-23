<?php
/**
 * Papéis: Administrador (tudo) e Editor (conteúdo, mídia e menus; sem
 * usuários, configurações sensíveis ou mensagens de contato).
 */

defined( 'ABSPATH' ) || exit;

function fck_sync_roles(): void {
	$editor = get_role( 'editor' );
	if ( $editor ) {
		// Menus são conteúdo editorial.
		$editor->add_cap( 'edit_theme_options' );
		// Sem acesso a configurações sensíveis, usuários ou plugins.
		foreach ( array( 'manage_options', 'list_users', 'edit_users', 'create_users', 'delete_users', 'promote_users', 'switch_themes', 'edit_themes', 'install_plugins', 'activate_plugins' ) as $cap ) {
			$editor->remove_cap( $cap );
		}
	}
	update_option( 'fck_roles_version', FCK_CORE_VERSION );
}

add_action(
	'admin_init',
	static function (): void {
		if ( get_option( 'fck_roles_version' ) !== FCK_CORE_VERSION ) {
			fck_sync_roles();
		}
	}
);

// Editores não acessam o Personalizador nem widgets (temas/aparência ficam com admin).
add_action(
	'admin_menu',
	static function (): void {
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}
		remove_submenu_page( 'themes.php', 'themes.php' );
		remove_submenu_page( 'themes.php', 'widgets.php' );
		global $submenu;
		foreach ( $submenu['themes.php'] ?? array() as $i => $item ) {
			if ( str_starts_with( (string) ( $item[2] ?? '' ), 'customize.php' ) ) {
				unset( $submenu['themes.php'][ $i ] );
			}
		}
	},
	999
);

add_action(
	'load-customize.php',
	static function (): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Sem permissão.', 403 );
		}
	}
);
