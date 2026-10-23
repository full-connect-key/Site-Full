<?php
/** Exibido somente se o plugin do núcleo estiver desativado. */
defined( 'ABSPATH' ) || exit;
status_header( 503 );
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="robots" content="noindex"><title><?php bloginfo( 'name' ); ?></title></head><body><p><?php bloginfo( 'name' ); ?></p></body></html>
