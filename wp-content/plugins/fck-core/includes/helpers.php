<?php
/**
 * Funções de leitura usadas pelo tema. Nenhum texto editorial fica no código:
 * tudo vem dos campos do painel.
 */

defined( 'ABSPATH' ) || exit;

/** Valores de um grupo de campos de um post (em preview, lê o autosave). */
function fck_meta( int $post_id, string $group ): array {
	$value = get_post_meta( $post_id, '_fck_' . $group, true );
	return is_array( $value ) ? $value : array();
}

/** Seção ativa? (checkbox "Exibir esta seção"; ausente = ativa) */
function fck_enabled_section( array $section ): bool {
	return ! array_key_exists( 'enabled', $section ) || ! empty( $section['enabled'] );
}

/** Registro global (configurações) por chave. */
function fck_global_post( string $key ): ?WP_Post {
	static $cache = array();
	if ( ! array_key_exists( $key, $cache ) ) {
		$posts         = get_posts(
			array(
				'post_type'      => 'fck_global',
				'name'           => $key,
				'post_status'    => array( 'publish', 'draft', 'private' ),
				'posts_per_page' => 1,
			)
		);
		$cache[ $key ] = $posts[0] ?? null;
	}
	return $cache[ $key ];
}

function fck_site( string $group ): array {
	$post = fck_global_post( 'site' );
	return $post ? fck_meta( $post->ID, $group ) : array();
}

/** Microtexto de interface: fck_ui( 'a11y', 'open_menu' ). */
function fck_ui( string $group, string $key ): string {
	static $cache = array();
	if ( ! isset( $cache[ $group ] ) ) {
		$post             = fck_global_post( 'ui' );
		$cache[ $group ] = $post ? fck_meta( $post->ID, 'ui_' . $group ) : array();
	}
	return (string) ( $cache[ $group ][ $key ] ?? '' );
}

/** Substitui {chave} em microtextos. */
function fck_fill( string $template, array $values ): string {
	return preg_replace_callback(
		'/\{(\w+)\}/',
		static fn( $m ) => array_key_exists( $m[1], $values ) ? (string) $values[ $m[1] ] : $m[0],
		$template
	);
}

/** Texto presente e que não seja pendência "[PENDENTE...]". */
function fck_has_text( mixed $value ): bool {
	return is_string( $value ) && '' !== trim( $value ) && ! preg_match( '/^\s*\[PENDENTE/i', $value );
}

/** Pode ser exibido publicamente? (publicado, ou pré-visualização autenticada) */
function fck_is_visible( int $post_id ): bool {
	$status = get_post_status( $post_id );
	if ( 'publish' === $status ) {
		return true;
	}
	return is_preview() && current_user_can( 'edit_post', $post_id ) && in_array( $status, array( 'draft', 'pending', 'future' ), true );
}

/**
 * Resolve um link do painel. Retorna null se não houver texto ou se o destino
 * não estiver publicado — nesse caso o botão simplesmente não aparece.
 *
 * @return array{href:string,label:string,external:bool}|null
 */
function fck_link( mixed $link, bool $require_label = true ): ?array {
	if ( ! is_array( $link ) ) {
		return null;
	}
	$label = trim( (string) ( $link['label'] ?? '' ) );
	if ( $require_label && '' === $label ) {
		return null;
	}
	$href = '';
	if ( 'url' === ( $link['type'] ?? 'post' ) ) {
		$href = trim( (string) ( $link['url'] ?? '' ) );
		if ( str_starts_with( $href, '/' ) ) {
			$href = home_url( $href );
		}
	} elseif ( ! empty( $link['post'] ) && fck_is_visible( (int) $link['post'] ) ) {
		$href = (string) get_permalink( (int) $link['post'] );
	}
	if ( '' === $href ) {
		return null;
	}
	return array(
		'href'     => $href,
		'label'    => $label,
		'external' => fck_is_external( $href ),
	);
}

function fck_is_external( string $href ): bool {
	if ( ! preg_match( '#^https?://#', $href ) ) {
		return false;
	}
	return wp_parse_url( $href, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST );
}

/** Atributos de link externo: nova aba + aviso para leitor de tela. */
function fck_link_attrs( array $link ): string {
	return $link['external'] ? ' target="_blank" rel="noopener noreferrer"' : '';
}

function fck_external_notice( array $link ): string {
	if ( ! $link['external'] ) {
		return '';
	}
	$notice = fck_ui( 'a11y', 'external_link' );
	return $notice ? '<span class="screen-reader-text"> ' . esc_html( $notice ) . '</span>' : '';
}

/** Posts publicados de um tipo, na ordem do painel. */
function fck_posts( string $type, array $args = array() ): array {
	$statuses = array( 'publish' );
	if ( is_preview() && current_user_can( 'edit_posts' ) ) {
		$statuses[] = 'draft';
	}
	return get_posts(
		array_merge(
			array(
				'post_type'        => $type,
				'post_status'      => $statuses,
				'posts_per_page'   => 200,
				'orderby'          => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
				'suppress_filters' => false,
			),
			$args
		)
	);
}

// -------------------------------------------------------------------------
// Mídia
// -------------------------------------------------------------------------

function fck_attachment_ok( mixed $id ): bool {
	$id = (int) $id;
	return $id > 0 && 'attachment' === get_post_type( $id );
}

/** Logos de terceiros (clientes, imprensa) exigem direito de uso confirmado. */
function fck_rights_confirmed( mixed $id ): bool {
	return fck_attachment_ok( $id ) && (bool) get_post_meta( (int) $id, '_fck_rights_confirmed', true );
}

function fck_is_video( int $id ): bool {
	return str_starts_with( (string) get_post_mime_type( $id ), 'video/' );
}

/** Alt do anexo ('' se decorativo). */
function fck_alt( int $id ): string {
	if ( get_post_meta( $id, '_fck_decorative', true ) ) {
		return '';
	}
	return (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
}

/** Posição do foco (object-position) definida no painel. */
function fck_focal( int $id ): string {
	$focal = get_post_meta( $id, '_fck_focal', true );
	return is_string( $focal ) && preg_match( '/^\d{1,3}% \d{1,3}%$/', $focal ) ? $focal : '50% 50%';
}

/**
 * <img> ou <video> responsivo de um anexo.
 *
 * @param array $opts size, sizes, class, loading ('lazy'|'eager'), decorative, controls
 */
function fck_media( mixed $id, array $opts = array() ): string {
	$id = (int) $id;
	if ( ! fck_attachment_ok( $id ) ) {
		return '';
	}
	$class   = $opts['class'] ?? '';
	$loading = $opts['loading'] ?? 'lazy';
	$style   = 'object-position:' . fck_focal( $id );

	if ( fck_is_video( $id ) ) {
		$poster = (int) get_post_meta( $id, '_fck_poster', true );
		$src    = wp_get_attachment_url( $id );
		return sprintf(
			'<video class="%s" style="%s" src="%s"%s %s preload="none" playsinline%s></video>',
			esc_attr( $class ),
			esc_attr( $style ),
			esc_url( (string) $src ),
			$poster ? ' poster="' . esc_url( (string) wp_get_attachment_image_url( $poster, 'large' ) ) . '"' : '',
			( $opts['controls'] ?? true ) ? 'controls' : '',
			fck_alt( $id ) ? ' aria-label="' . esc_attr( fck_alt( $id ) ) . '"' : ''
		);
	}

	$attrs = array(
		'class'    => $class,
		'style'    => $style,
		'loading'  => $loading,
		'decoding' => 'async',
		'alt'      => ! empty( $opts['decorative'] ) ? '' : fck_alt( $id ),
	);
	if ( 'eager' === $loading ) {
		$attrs['fetchpriority'] = 'high';
	}
	if ( ! empty( $opts['sizes'] ) ) {
		$attrs['sizes'] = $opts['sizes'];
	}
	return wp_get_attachment_image( $id, $opts['size'] ?? 'large', false, $attrs );
}

// -------------------------------------------------------------------------
// Leitura estimada
// -------------------------------------------------------------------------

function fck_reading_minutes( WP_Post $post ): int {
	$text  = wp_strip_all_tags( strip_shortcodes( $post->post_content . ' ' . $post->post_title ) );
	$words = preg_match_all( '/[\p{L}\p{N}]+/u', $text );
	return max( 1, (int) round( $words / 200 ) );
}
