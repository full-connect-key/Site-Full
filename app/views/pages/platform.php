<?php
/**
 * Página do item (hero com os dados do painel). O layout completo e as
 * interações entram na sprint correspondente (docs/SPRINT_STATUS.md).
 */
defined( 'FCK_ROOT' ) || exit;
$hero = g( $entry, 'hero' ) ?: array( 'headline' => $entry['title'] );
if ( empty( $hero['headline'] ) ) {
	$hero['headline'] = $entry['title'];
}
view( 'parts/page-hero', array( 'args' => array( 'data' => $hero, 'product' => (string) ( g( $entry, 'identity' )['product'] ?? '' ) ) ) );
cta_band( g( $entry, 'cta' ) + array( 'enabled' => true ), 'item-cta' );
