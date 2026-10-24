<?php
/**
 * FullMatch — constelação de consumidores: no passo 1 os pontos começam a se
 * agrupar; no passo 2 formam três perfis (o perfil da vez acompanha a
 * rolagem); no passo 3 órbita de recompra no Recorrente, ofertas orbitando o
 * Alto valor e os Inativos voltando a acender um a um.
 */
defined( 'FCK_ROOT' ) || exit;
$in       = $args['inter'];
$clusters = array_values( array_filter( (array) ( $in['clusters'] ?? array() ), fn( $c ) => has_text( $c['name'] ?? '' ) ) );
$centers  = array( array( 105, 160 ), array( 225, 92 ), array( 330, 190 ) );
?>
<svg class="viz viz-match" viewBox="0 0 420 300">
	<circle class="match-orbit" pathLength="1" cx="105" cy="160" r="58"/>
	<g class="match-offers" style="transform-origin:225px 92px">
		<?php foreach ( array( 0, 120, 240 ) as $k => $deg ) : ?>
			<rect class="match-offer" x="<?php echo e( (string) round( 225 + 56 * cos( deg2rad( $deg ) ) - 10, 1 ) ); ?>" y="<?php echo e( (string) round( 92 + 56 * sin( deg2rad( $deg ) ) - 7, 1 ) ); ?>" width="20" height="14" rx="3"/>
		<?php endforeach; ?>
	</g>
	<?php for ( $i = 0; $i < 27; $i++ ) : ?>
		<?php
		$k  = $i % 3;
		$j  = intdiv( $i, 3 );
		$x0 = 30 + ( ( $i * 97 ) % 360 );
		$y0 = 30 + ( ( $i * 61 ) % 240 );
		$a  = $j * 0.7 + $k;
		$x1 = $centers[ $k ][0] + cos( $a ) * ( 12 + ( $j % 3 ) * 11 );
		$y1 = $centers[ $k ][1] + sin( $a ) * ( 12 + ( $j % 3 ) * 11 );
		?>
		<circle class="match-dot" data-cluster="<?php echo (int) $k; ?>" style="--x0:<?php echo (int) $x0; ?>;--y0:<?php echo (int) $y0; ?>;--x1:<?php echo e( (string) round( $x1, 1 ) ); ?>;--y1:<?php echo e( (string) round( $y1, 1 ) ); ?>;--j:<?php echo (int) $j; ?>" cx="0" cy="0" r="<?php echo 0 === $j % 4 ? 6 : 4; ?>"/>
	<?php endfor; ?>
	<?php foreach ( $clusters as $k => $c ) : ?>
		<?php if ( isset( $centers[ $k ] ) ) : ?>
			<g class="match-cluster" data-cluster="<?php echo (int) $k; ?>">
				<circle class="match-cluster__halo" cx="<?php echo (int) $centers[ $k ][0]; ?>" cy="<?php echo (int) $centers[ $k ][1]; ?>" r="44"/>
				<text class="viz-label match-cluster__label" x="<?php echo (int) $centers[ $k ][0]; ?>" y="<?php echo (int) ( $centers[ $k ][1] + 64 ); ?>" text-anchor="middle"><?php echo e( $c['name'] ); ?></text>
			</g>
		<?php endif; ?>
	<?php endforeach; ?>
</svg>
