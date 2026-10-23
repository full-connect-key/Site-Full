<?php
/**
 * Estrutura da página pública.
 * Variáveis: $content (HTML da página), $seo (contexto SEO), $body_class.
 */
defined( 'FCK_ROOT' ) || exit;
view( '_header', array( 'seo' => $seo, 'body_class' => $body_class ) );
echo $content; // phpcs:ignore -- HTML montado pelas views.
view( '_footer' );
