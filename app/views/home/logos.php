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
		$m    = media_get( $logo );
		// Mesma área visual para todos: logos largos ficam mais baixos, compactos mais altos.
		$h = $m && $m['width'] && $m['height'] ? min( 1, sqrt( $m['height'] / $m['width'] ) ) : 0.6;
		echo '<li class="marquee__item"' . ( $clone ? ' aria-hidden="true"' : '' ) . ' style="--logo-h:' . round( $h, 3 ) . '">';
		echo media_html( $logo, array( 'size' => 'thumb', 'sizes' => '16rem', 'alt' => $clone ? '' : $c['title'], 'class' => 'marquee__logo' ) );
		echo '</li>';
	}
};
?>
<section class="marquee" data-theme="dark" aria-label="<?php echo e( ui( 'a11y', 'logos_region' ) ); ?>">
	<div class="marquee__viewport">
		<ul class="marquee__track" data-marquee-track>
			<?php $render( false ); ?>
			<?php $render( true ); ?>
		</ul>
	</div>
</section>
