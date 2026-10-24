<?php
/** Home 1 — Hero com alternância de títulos e fundo de conexões. */
defined( 'FCK_ROOT' ) || exit;
$d = $args['data'];
if ( ! has_text( $d['headline'] ?? '' ) ) {
	return;
}
// Título alternativo da Home; vazio = assinatura institucional global (D2).
$alt = has_text( $d['headline_alt'] ?? '' ) ? $d['headline_alt'] : (string) ( site( 'brand' )['signature'] ?? '' );
?>
<section class="hero" data-theme="hero" data-module="connections" aria-labelledby="hero-title">
	<div class="hero__glow" aria-hidden="true"></div>
	<div class="container hero__inner">
		<h1 id="hero-title" class="hero__title display-xl" data-module="headline-swap">
			<span class="hero__swap-item" data-swap-item><?php echo e( $d['headline'] ); ?></span>
			<?php if ( $alt ) : ?>
				<span class="hero__swap-item" data-swap-item><?php echo e( $alt ); ?></span>
			<?php endif; ?>
		</h1>
		<div class="hero__bottom">
			<?php text_el( $d['lead'] ?? '', 'p', 'lead hero__lead' ); ?>
			<?php button( $d['cta'] ?? null, 'primary' ); ?>
		</div>
	</div>
	<span class="hero__scroll" aria-hidden="true"></span>
</section>
