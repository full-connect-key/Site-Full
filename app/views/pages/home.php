<?php
/**
 * Home — 11 seções em ordem fixa. Cada seção pode ser ocultada no painel;
 * seções que dependem de conteúdo real somem quando ele não existe.
 */
defined( 'FCK_ROOT' ) || exit;
$sections = array( 'hero', 'logos', 'essence', 'ecosystem', 'sectors', 'cases', 'partners', 'testimonials', 'team', 'insights', 'footer_cta' );
foreach ( $sections as $section ) {
	$data = g( $entry, $section );
	if ( 'hero' !== $section && ! section_enabled( $data ) ) {
		continue;
	}
	view( 'home/' . str_replace( '_', '-', $section ), array( 'args' => array( 'data' => $data, 'entry' => $entry ) ) );
}
