<?php
/**
 * FullLeads — rede de pontos que converge em oportunidades a cada passo
 * (não um funil). Visual decorativo.
 */
defined( 'FCK_ROOT' ) || exit;
$total = max( 1, count( $args['steps'] ) );
$dots  = '';
for ( $i = 0; $i < 48; $i++ ) {
	$a     = $i * 2.399963;
	$rad   = ( 30 + ( $i * 7 ) % 18 ) * 4.2;
	$group = $i % $total;
	$dots .= sprintf(
		'<circle class="lead-dot" data-at="%.3f" cx="%.1f" cy="%.1f" r="%.1f" style="--tx:%.1fpx;--ty:%.1fpx"/>',
		( $group + 0.4 ) / $total, 200 + cos( $a ) * $rad, 200 + sin( $a ) * $rad, 2 + ( $i % 3 ), -cos( $a ) * $rad * 0.82, -sin( $a ) * $rad * 0.82
	);
}
$rings = '';
for ( $k = 0; $k < $total; $k++ ) {
	$rings .= sprintf( '<circle class="lead-ring" data-at="%.3f" cx="200" cy="200" r="%d"/>', ( $k + 0.2 ) / $total, 180 - $k * ( 140 / $total ) );
}
?>
<svg class="viz leads-net" viewBox="0 0 400 400"><?php echo $rings . $dots; // phpcs:ignore ?><circle class="lead-core" cx="200" cy="200" r="22"/></svg>
