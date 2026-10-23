<?php
/**
 * Full Leads — atrair → capturar → nutrir → vendas como uma rede de pontos
 * que converge em oportunidades (não um funil). Visual decorativo.
 */
defined( 'FCK_ROOT' ) || exit;
$items = $args['items'];
$total = count( $items );
$dots  = '';
for ( $i = 0; $i < 48; $i++ ) {
	$a     = $i * 2.399963; // ângulo áureo: distribuição orgânica
	$rad   = 30 + ( $i * 7 ) % 18;
	$group = $i % $total;
	$dots .= sprintf(
		'<circle class="lead-dot" data-group="%d" cx="%.1f" cy="%.1f" r="%.1f" style="--tx:%.1fpx;--ty:%.1fpx"/>',
		$group,
		200 + cos( $a ) * $rad * 4.2,
		200 + sin( $a ) * $rad * 4.2,
		2 + ( $i % 3 ),
		-cos( $a ) * $rad * 4.2 * 0.82,
		-sin( $a ) * $rad * 4.2 * 0.82
	);
}
$rings = '';
for ( $k = 0; $k < $total; $k++ ) {
	$rings .= sprintf( '<circle class="lead-ring" data-ring="%d" cx="200" cy="200" r="%d"/>', $k, 180 - $k * ( 140 / max( 1, $total ) ) );
}
$visual = '<svg class="leads-net" viewBox="0 0 400 400">' . $rings . $dots . '<circle class="lead-core" cx="200" cy="200" r="22"/></svg>';
view( 'platform/story', array( 'args' => array( 'items' => $items, 'visual' => $visual, 'kind' => 'leads' ) ) );
