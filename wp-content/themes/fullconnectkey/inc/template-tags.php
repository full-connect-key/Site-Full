<?php
/**
 * Peças de marcação reutilizáveis. Não contêm texto editorial: rótulos vêm
 * dos parâmetros (painel) ou dos textos de interface (fck_ui).
 */

defined( 'ABSPATH' ) || exit;

/** Ícones SVG decorativos. */
function fck_icon( string $name, string $class = '' ): string {
	$paths = array(
		'arrow'       => '<path d="M4 12h15M13 6l6 6-6 6"/>',
		'arrow-left'  => '<path d="M20 12H5M11 6l-6 6 6 6"/>',
		'arrow-up'    => '<path d="M12 20V5M6 11l6-6 6 6"/>',
		'external'    => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
		'chevron'     => '<path d="M6 9l6 6 6-6"/>',
		'close'       => '<path d="M5 5l14 14M19 5L5 19"/>',
		'pause'       => '<path d="M9 5v14M15 5v14"/>',
		'play'        => '<path d="M7 5l12 7-12 7z"/>',
		'plus'        => '<path d="M12 5v14M5 12h14"/>',
		'link'        => '<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/>',
		'share'       => '<path d="M12 4v11M7 9l5-5 5 5M5 14v5h14v-5"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
		esc_attr( $class ),
		$paths[ $name ]
	);
}

/**
 * Botão-link a partir de um link do painel. Sem link válido, não imprime nada.
 */
function fck_button( mixed $link, string $variant = 'primary', array $opts = array() ): void {
	$resolved = is_array( $link ) && isset( $link['href'] ) ? $link : fck_link( $link );
	if ( ! $resolved ) {
		return;
	}
	$arrow = $opts['arrow'] ?? true;
	printf(
		'<a class="btn btn--%s %s" href="%s"%s>%s%s%s</a>',
		esc_attr( $variant ),
		esc_attr( $opts['class'] ?? '' ),
		esc_url( $resolved['href'] ),
		fck_link_attrs( $resolved ), // phpcs:ignore -- atributos fixos.
		'<span>' . esc_html( $resolved['label'] ) . '</span>',
		$arrow ? fck_icon( $resolved['external'] ? 'external' : 'arrow', 'btn__arrow' ) : '', // phpcs:ignore -- SVG fixo.
		fck_external_notice( $resolved ) // phpcs:ignore -- escapado.
	);
}

/** Wordmark: logo oficial se cadastrado; senão o nome em texto (nunca logo inventado). */
function fck_wordmark( string $name, int $logo = 0, string $class = '' ): string {
	if ( $logo && fck_attachment_ok( $logo ) ) {
		$img = fck_is_svg( $logo )
			? fck_svg_img( $logo, 'wordmark__img', $name, 'eager' )
			: wp_get_attachment_image( $logo, 'medium', false, array( 'alt' => $name, 'loading' => 'eager', 'decoding' => 'async', 'class' => 'wordmark__img' ) );
		return '<span class="wordmark wordmark--logo ' . esc_attr( $class ) . '">' . $img . '</span>';
	}
	return '<span class="wordmark ' . esc_attr( $class ) . '">' . esc_html( $name ) . '</span>';
}

/** Abertura padrão de seção (eyebrow opcional + título + texto). */
function fck_section_head( array $args ): void {
	$title = $args['title'] ?? '';
	$text  = $args['text'] ?? '';
	if ( ! fck_has_text( $title ) && ! fck_has_text( $text ) ) {
		return;
	}
	$id    = $args['id'] ?? '';
	$level = (int) ( $args['level'] ?? 2 );
	$size  = $args['size'] ?? 'display-m';
	echo '<div class="section-head ' . esc_attr( $args['class'] ?? '' ) . '">';
	if ( fck_has_text( $args['eyebrow'] ?? '' ) ) {
		echo '<p class="eyebrow">' . esc_html( $args['eyebrow'] ) . '</p>';
	}
	if ( fck_has_text( $title ) ) {
		printf( '<h%1$d%2$s class="%3$s section-head__title">%4$s</h%1$d>', $level, $id ? ' id="' . esc_attr( $id ) . '"' : '', esc_attr( $size ), esc_html( $title ) );
	}
	if ( fck_has_text( $text ) ) {
		echo '<p class="lead section-head__text">' . esc_html( $text ) . '</p>';
	}
	echo '</div>';
}

/** Escapa e imprime texto do painel se existir (com tag e classe). */
function fck_text_el( mixed $value, string $tag = 'p', string $class = '', array $attrs = array() ): void {
	if ( ! fck_has_text( $value ) ) {
		return;
	}
	$extra = '';
	foreach ( $attrs as $k => $v ) {
		$extra .= ' ' . esc_attr( $k ) . '="' . esc_attr( (string) $v ) . '"';
	}
	printf( '<%1$s class="%2$s"%3$s>%4$s</%1$s>', tag_escape( $tag ), esc_attr( $class ), $extra, esc_html( $value ) ); // phpcs:ignore
}

/** Bloco "CTA final" (cinematográfico) usado por várias páginas. */
function fck_cta_band( array $cta, string $id = 'cta' ): void {
	if ( ! fck_enabled_section( $cta ) || ! fck_has_text( $cta['title'] ?? '' ) ) {
		return;
	}
	get_template_part( 'parts/cta-band', null, array( 'cta' => $cta, 'id' => $id ) );
}

/** Página-base (listagem) de um tipo de conteúdo. */
function fck_base_page( string $post_type ): ?WP_Post {
	$roles = array( 'fck_platform' => 'ecosystem', 'fck_case' => 'cases', 'fck_article' => 'insights' );
	return isset( $roles[ $post_type ] ) ? fck_role_page( $roles[ $post_type ] ) : null;
}
