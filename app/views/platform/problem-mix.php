<?php
/**
 * FullMix · Problema — blocos de canais espalhados se empilham numa barra
 * "vendas", que se divide numa base ("o que aconteceria sem campanhas") e
 * em segmentos de "contribuição estimada". Proporções ilustrativas.
 */
defined( 'FCK_ROOT' ) || exit;
$in       = $args['inter'];
$channels = array_values( array_filter( (array) ( $in['channels'] ?? array() ), fn( $c ) => has_text( $c['name'] ?? '' ) ) );
if ( ! $channels ) {
	return;
}
$total = array_sum( array_map( fn( $c ) => max( 1, (int) ( $c['share'] ?? 1 ) ), $channels ) );
?>
<div class="pv pv-mix" aria-hidden="true">
	<?php if ( has_text( $in['label_sales'] ?? '' ) ) : ?><span class="pv-mix__sales"><?php echo e( $in['label_sales'] ); ?></span><?php endif; ?>
	<div class="pv-mix__bar">
		<span class="pv-mix__base"><?php echo e( (string) ( $in['label_base'] ?? '' ) ); ?></span>
		<span class="pv-mix__segs">
			<?php foreach ( $channels as $i => $c ) : ?>
				<span class="pv-mix__seg" data-label="<?php echo e( $c['name'] ); ?>" style="--i:<?php echo (int) $i; ?>;--w:<?php echo e( (string) round( max( 1, (int) ( $c['share'] ?? 1 ) ) / $total, 3 ) ); ?>;--dx:<?php echo (int) ( ( ( $i * 67 ) % 160 ) - 80 ); ?>;--dy:<?php echo (int) ( -60 - ( ( $i * 41 ) % 90 ) ); ?>;--r:<?php echo (int) ( ( ( $i * 29 ) % 40 ) - 20 ); ?>"></span>
			<?php endforeach; ?>
		</span>
	</div>
	<?php if ( has_text( $in['label_contribution'] ?? '' ) ) : ?><span class="pv-mix__contrib"><?php echo e( $in['label_contribution'] ); ?></span><?php endif; ?>
</div>
