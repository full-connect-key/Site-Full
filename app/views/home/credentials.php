<?php
/**
 * Home 2a — Faixa de credenciais (ADR-020): título fixo à esquerda e os logos
 * das plataformas correndo sem parar à direita. Com movimento reduzido, os
 * logos ficam parados, em grade.
 */
defined( 'FCK_ROOT' ) || exit;
$d     = $args['data'];
$items = array_values( array_filter( entries_of( 'credential' ), fn( $c ) => media_ok( g( $c, 'details' )['logo'] ?? 0 ) ) );
if ( ! $items ) {
	return;
}
$render = static function ( bool $clone ) use ( $items ): void {
	foreach ( $items as $c ) {
		$cd = g( $c, 'details' );
		$m  = media_get( (int) $cd['logo'] );
		// Mesma área visual para todos os logos (como na faixa de clientes).
		$h = $m && $m['width'] && $m['height'] ? min( 1, sqrt( $m['height'] / $m['width'] ) ) : 0.5;
		echo '<li class="creds__item"' . ( $clone ? ' aria-hidden="true"' : '' ) . ' style="--logo-h:' . e( (string) round( $h, 3 ) ) . '">';
		echo media_html( (int) $cd['logo'], array( 'size' => 'thumb', 'sizes' => '12rem', 'alt' => $clone ? '' : $c['title'], 'class' => 'creds__logo' ) );
		if ( has_text( $cd['level'] ?? '' ) ) {
			echo '<span class="creds__level small">' . e( $cd['level'] ) . '</span>';
		}
		echo '</li>';
	}
};
?>
<section class="creds" data-theme="dark" aria-labelledby="creds-title">
	<div class="container creds__inner">
		<div class="creds__head">
			<?php text_el( $d['eyebrow'] ?? '', 'p', 'eyebrow' ); ?>
			<?php text_el( $d['title'] ?? '', 'h2', 'creds__title', array( 'id' => 'creds-title' ) ); ?>
		</div>
		<div class="creds__viewport">
			<ul class="creds__track">
				<?php $render( false ); ?>
				<?php $render( true ); ?>
			</ul>
		</div>
	</div>
</section>
