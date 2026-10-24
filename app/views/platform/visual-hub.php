<?php
/**
 * FullHub — mensagens soltas (caos) que se organizam em colunas conforme a
 * rolagem avança. Cards decorativos; nomes das colunas vêm do painel.
 */
defined( 'FCK_ROOT' ) || exit;
$cols  = ( $args['lines'] )( (string) ( $args['inter']['columns'] ?? '' ) ) ?: array_map( fn( $s ) => $s['title'], $args['steps'] );
$count = count( $cols );
$cards = '';
$n     = 0;
for ( $c = 0; $c < $count; $c++ ) {
	for ( $r = 0; $r < 3; $r++ ) {
		$cards .= sprintf(
			'<span class="hub-card" data-col="%1$d" data-at="%6$.3f" style="--col:%1$d;--row:%2$d;--cx:%3$d%%;--cy:%4$d%%;--cr:%5$ddeg"><i></i><i></i></span>',
			$c, $r, ( ( $n * 37 ) % 80 ) - 40, ( ( $n * 53 ) % 70 ) - 35, ( ( $n * 29 ) % 40 ) - 20, 0.08 + 0.84 * ( $c + $r / 3 ) / $count
		);
		$n++;
	}
}
?>
<div class="hub-board" style="--cols:<?php echo (int) $count; ?>">
	<div class="hub-cols"><?php foreach ( $cols as $c => $label ) : ?><span class="hub-col" style="--col:<?php echo (int) $c; ?>"><?php echo e( $label ); ?></span><?php endforeach; ?></div>
	<?php echo $cards; // phpcs:ignore -- marcação decorativa montada acima. ?>
</div>
