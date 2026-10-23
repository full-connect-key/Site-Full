<?php
/**
 * Home — 11 seções em ordem fixa. Cada seção pode ser ocultada no painel;
 * seções que dependem de conteúdo real somem quando ele não existe.
 */

defined( 'ABSPATH' ) || exit;

get_header();
$home_id  = (int) get_queried_object_id() ?: (int) get_option( 'page_on_front' );
$sections = array( 'hero', 'logos', 'essence', 'ecosystem', 'sectors', 'cases', 'partners', 'testimonials', 'team', 'insights', 'footer_cta' );
foreach ( $sections as $section ) {
	$data = fck_meta( $home_id, $section );
	if ( 'hero' !== $section && ! fck_enabled_section( $data ) ) {
		continue;
	}
	get_template_part( 'parts/home/' . str_replace( '_', '-', $section ), null, array( 'data' => $data, 'page_id' => $home_id ) );
}
get_footer();
