<?php
/** Home 1 — Hero com alternância de títulos e fundo de conexões. */
defined( 'ABSPATH' ) || exit;
$d = $args['data'];
if ( ! fck_has_text( $d['headline'] ?? '' ) ) {
	return;
}
$alt = fck_has_text( $d['headline_alt'] ?? '' ) ? $d['headline_alt'] : '';
?>
<section class="hero" data-theme="hero" data-module="connections" aria-labelledby="hero-title">
	<div class="hero__glow" aria-hidden="true"></div>
	<div class="container hero__inner">
		<h1 id="hero-title" class="hero__title display-xl" data-module="headline-swap">
			<span class="hero__swap-item" data-swap-item><?php echo esc_html( $d['headline'] ); ?></span>
			<?php if ( $alt ) : ?>
				<span class="hero__swap-item" data-swap-item><?php echo esc_html( $alt ); ?></span>
			<?php endif; ?>
		</h1>
		<div class="hero__bottom">
			<?php fck_text_el( $d['lead'] ?? '', 'p', 'lead hero__lead' ); ?>
			<?php fck_button( $d['cta'] ?? null, 'primary' ); ?>
		</div>
	</div>
	<span class="hero__scroll" aria-hidden="true"></span>
</section>
