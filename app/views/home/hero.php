<?php
/** Home 1 — Hero com alternância de títulos e o símbolo Full animado (assets/js/modules/symbol.js). */
defined( 'FCK_ROOT' ) || exit;
$d = $args['data'];
if ( ! has_text( $d['headline'] ?? '' ) ) {
	return;
}
// Título alternativo da Home; vazio = assinatura institucional global (D2).
$alt = has_text( $d['headline_alt'] ?? '' ) ? $d['headline_alt'] : (string) ( site( 'brand' )['signature'] ?? '' );
// Com duas ou mais frases, a última vira destaque (menor, em azul, na linha de baixo).
$alt_main   = $alt;
$alt_accent = '';
if ( preg_match( '/^(.+?[.!?])\s+([^.!?]+[.!?]?)$/u', trim( $alt ), $m ) ) {
	$alt_main   = $m[1];
	$alt_accent = $m[2];
}
?>
<section class="hero" data-theme="hero" data-module="symbol" aria-labelledby="hero-title">
	<div class="hero__glow" aria-hidden="true"></div>
	<div class="container hero__inner">
		<h1 id="hero-title" class="hero__title display-xl" data-module="headline-swap">
			<span class="hero__swap-item" data-swap-item><?php echo e( $d['headline'] ); ?></span>
			<?php if ( $alt ) : ?>
				<span class="hero__swap-item" data-swap-item><?php echo e( $alt_main ); ?><?php if ( '' !== $alt_accent ) : ?> <span class="hero__accent"><?php echo e( $alt_accent ); ?></span><?php endif; ?></span>
			<?php endif; ?>
		</h1>
		<div class="hero__bottom">
			<?php text_el( $d['lead'] ?? '', 'p', 'lead hero__lead' ); ?>
			<?php button( $d['cta'] ?? null, 'primary' ); ?>
		</div>
	</div>
	<span class="hero__scroll" aria-hidden="true"></span>
</section>
