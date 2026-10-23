<?php
/** Home 2 — Faixa de logos (somente clientes autorizados; sem título). */
defined( 'ABSPATH' ) || exit;
$clients = array_values(
	array_filter(
		fck_posts( 'fck_client' ),
		static function ( WP_Post $c ): bool {
			$d = fck_meta( $c->ID, 'details' );
			return ! empty( $d['authorized'] ) && fck_attachment_ok( $d['logo'] ?? 0 );
		}
	)
);
if ( ! $clients ) {
	return;
}
$render = static function ( bool $clone ) use ( $clients ): void {
	foreach ( $clients as $c ) {
		$logo = (int) fck_meta( $c->ID, 'details' )['logo'];
		echo '<li class="marquee__item"' . ( $clone ? ' aria-hidden="true"' : '' ) . '>';
		echo wp_get_attachment_image( $logo, 'medium', false, array( 'alt' => $clone ? '' : get_the_title( $c ), 'loading' => 'lazy', 'class' => 'marquee__logo' ) );
		echo '</li>';
	}
};
?>
<section class="marquee" data-theme="dark" aria-label="<?php echo esc_attr( fck_ui( 'a11y', 'logos_region' ) ); ?>" data-module="marquee">
	<div class="marquee__viewport">
		<ul class="marquee__track" data-marquee-track>
			<?php $render( false ); ?>
			<?php $render( true ); ?>
		</ul>
	</div>
	<div class="container marquee__controls">
		<button class="icon-btn" type="button" data-marquee-toggle aria-pressed="false" data-label-pause="<?php echo esc_attr( fck_ui( 'a11y', 'pause' ) ); ?>" data-label-play="<?php echo esc_attr( fck_ui( 'a11y', 'play' ) ); ?>" aria-label="<?php echo esc_attr( fck_ui( 'a11y', 'pause' ) ); ?>">
			<?php echo fck_icon( 'pause' ); // phpcs:ignore ?>
		</button>
	</div>
</section>
