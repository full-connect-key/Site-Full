<?php
/** Home 2 — Faixa de logos (somente clientes autorizados; sem título). */
defined( 'FCK_ROOT' ) || exit;
$clients = array_values(
	array_filter(
		entries_of( 'client' ),
		static function ( array $c ): bool {
			$d = g( $c, 'details' );
			return ! empty( $d['authorized'] ) && media_ok( $d['logo'] ?? 0 );
		}
	)
);
if ( ! $clients ) {
	return;
}
$render = static function ( bool $clone ) use ( $clients ): void {
	foreach ( $clients as $c ) {
		$logo = (int) g( $c, 'details' )['logo'];
		echo '<li class="marquee__item"' . ( $clone ? ' aria-hidden="true"' : '' ) . '>';
		echo media_html( $logo, array( 'size' => 'thumb', 'alt' => $clone ? '' : $c['title'], 'class' => 'marquee__logo' ) );
		echo '</li>';
	}
};
?>
<section class="marquee" data-theme="dark" aria-label="<?php echo e( ui( 'a11y', 'logos_region' ) ); ?>" data-module="marquee">
	<div class="marquee__viewport">
		<ul class="marquee__track" data-marquee-track>
			<?php $render( false ); ?>
			<?php $render( true ); ?>
		</ul>
	</div>
	<div class="container marquee__controls">
		<button class="icon-btn" type="button" data-marquee-toggle aria-pressed="false" data-label-pause="<?php echo e( ui( 'a11y', 'pause' ) ); ?>" data-label-play="<?php echo e( ui( 'a11y', 'play' ) ); ?>" aria-label="<?php echo e( ui( 'a11y', 'pause' ) ); ?>">
			<?php echo icon( 'pause' ); // phpcs:ignore ?>
		</button>
	</div>
</section>
