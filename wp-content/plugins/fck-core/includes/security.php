<?php
/**
 * Endurecimento: cabeçalhos de segurança (CSP com nonce), XML-RPC desligado,
 * listagem de usuários pela API bloqueada, versão do WordPress oculta.
 */

defined( 'ABSPATH' ) || exit;

function fck_csp_nonce(): string {
	static $nonce = null;
	if ( null === $nonce ) {
		$nonce = base64_encode( random_bytes( 16 ) ); // phpcs:ignore
	}
	return $nonce;
}

add_action(
	'send_headers',
	static function (): void {
		header( 'X-Content-Type-Options: nosniff' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), interest-cohort=()' );
		if ( is_ssl() && 'production' === wp_get_environment_type() ) {
			header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
		}
		if ( is_admin() || is_customize_preview() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}
		$csp = array(
			"default-src 'self'",
			"script-src 'self' 'nonce-" . fck_csp_nonce() . "'",
			"style-src 'self' 'unsafe-inline'",
			"img-src 'self' data: blob: https:",
			"media-src 'self' https:",
			"font-src 'self'",
			"connect-src 'self'",
			'frame-src https://www.youtube-nocookie.com https://www.youtube.com https://player.vimeo.com',
			"form-action 'self'",
			"base-uri 'self'",
			"object-src 'none'",
			"frame-ancestors 'self'",
		);
		// Barra do admin (usuário logado) usa scripts inline do core.
		if ( is_user_logged_in() ) {
			$csp[1] = "script-src 'self' 'unsafe-inline'";
		}
		header( 'Content-Security-Policy: ' . implode( '; ', $csp ) );
	}
);

// Nonce nos scripts inline gerados pelo WordPress (ex.: speculation rules).
add_filter(
	'wp_inline_script_attributes',
	static function ( array $attrs ): array {
		$attrs['nonce'] = fck_csp_nonce();
		return $attrs;
	}
);
add_filter(
	'wp_script_attributes',
	static function ( array $attrs ): array {
		$attrs['nonce'] = fck_csp_nonce();
		return $attrs;
	}
);

add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'the_generator', '__return_empty_string' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

// API pública: sem listagem de usuários para visitantes.
add_filter(
	'rest_endpoints',
	static function ( array $endpoints ): array {
		if ( ! is_user_logged_in() ) {
			unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}
		return $endpoints;
	}
);

// Bloqueia ?author=N (enumeração de usuários).
add_action(
	'template_redirect',
	static function (): void {
		if ( is_author() ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	}
);

// Mensagem de login genérica.
add_filter( 'login_errors', static fn() => 'Usuário ou senha incorretos.' );
