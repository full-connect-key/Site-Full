<?php
/**
 * Página com hero e CTA vindos do painel. As seções interativas desta
 * página entram na sprint correspondente (docs/SPRINT_STATUS.md).
 */
defined( 'FCK_ROOT' ) || exit;
view( 'parts/page-hero', array( 'args' => array( 'data' => g( $entry, 'hero' ) ) ) );
cta_band( g( $entry, 'cta' ), 'page-cta' );
