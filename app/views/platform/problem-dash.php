<?php
/**
 * FullDash · Problema — as palavras soltas (impressões, cliques, alcance)
 * se alinham com a rolagem e convergem para a linha "objetivo do negócio".
 */
defined( 'FCK_ROOT' ) || exit;
$in    = $args['inter'];
$words = array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) ( $in['words'] ?? '' ) ) ?: array() ), 'strlen' ) );
if ( ! $words ) {
	return;
}
$n = count( $words );
?>
<div class="pv pv-dash" aria-hidden="true">
	<?php foreach ( $words as $i => $w ) : ?>
		<span class="pv-dash__word" style="--i:<?php echo (int) $i; ?>;--dx:<?php echo (int) ( ( ( $i * 53 ) % 120 ) - 60 ); ?>;--dy:<?php echo (int) ( ( ( $i * 71 ) % 140 ) - 110 ); ?>;--r:<?php echo (int) ( ( ( $i * 37 ) % 30 ) - 15 ); ?>;--x:<?php echo e( (string) round( ( $i + 0.5 ) * 100 / $n, 1 ) ); ?>"><?php echo e( $w ); ?></span>
	<?php endforeach; ?>
	<span class="pv-dash__goal"><span class="pv-dash__goal-line"></span><?php if ( has_text( $in['label_goal'] ?? '' ) ) : ?><span class="pv-dash__goal-label"><?php echo e( $in['label_goal'] ); ?></span><?php endif; ?></span>
</div>
