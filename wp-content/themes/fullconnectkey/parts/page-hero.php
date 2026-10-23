<?php
/**
 * Hero de página interna: chamada (eyebrow), título (opcional: título
 * alternativo), texto de apoio e fundo de conexões.
 */
defined( 'ABSPATH' ) || exit;
$d       = $args['data'] ?? array();
$product = $args['product'] ?? '';
$extra   = $args['extra'] ?? '';
if ( ! fck_has_text( $d['headline'] ?? '' ) ) {
	return;
}
?>
<section class="page-hero" data-theme="hero" data-module="connections" <?php echo $product ? 'data-product="' . esc_attr( $product ) . '"' : ''; ?> aria-labelledby="page-title">
	<div class="container page-hero__inner">
		<?php if ( ! empty( $args['before'] ) ) : ?>
			<div class="page-hero__before"><?php echo $args['before']; // phpcs:ignore -- marcação montada pelo template. ?></div>
		<?php endif; ?>
		<?php fck_text_el( $d['eyebrow'] ?? '', 'p', 'eyebrow page-hero__eyebrow' ); ?>
		<h1 id="page-title" class="display-l page-hero__title"><?php echo esc_html( $d['headline'] ); ?></h1>
		<?php fck_text_el( $d['headline_alt'] ?? '', 'p', 'display-s page-hero__alt' ); ?>
		<?php if ( fck_has_text( $d['lead'] ?? '' ) || $extra ) : ?>
			<div class="page-hero__bottom">
				<?php fck_text_el( $d['lead'] ?? '', 'p', 'lead page-hero__lead' ); ?>
				<?php echo $extra; // phpcs:ignore -- marcação montada pelo template. ?>
			</div>
		<?php endif; ?>
	</div>
</section>
