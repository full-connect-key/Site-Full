<?php
/**
 * Full Hub — caos de mensagens que se reorganiza em fluxo (solicitações →
 * responsáveis e prazos → aprovações → visibilidade) conforme a rolagem.
 * Os cards são decorativos (sem texto); os nomes das colunas vêm do painel.
 */
defined( 'FCK_ROOT' ) || exit;
$items = $args['items'];
$cols  = count( $items );
$cards = '';
$n     = 0;
for ( $c = 0; $c < $cols; $c++ ) {
	for ( $r = 0; $r < 3; $r++ ) {
		// Posição "caos" determinística (mesma a cada carregamento).
		$cx = ( ( $n * 37 ) % 80 ) - 40;
		$cy = ( ( $n * 53 ) % 70 ) - 35;
		$cr = ( ( $n * 29 ) % 40 ) - 20;
		$cards .= sprintf(
			'<span class="hub-card" data-col="%1$d" style="--col:%1$d;--row:%2$d;--cx:%3$d%%;--cy:%4$d%%;--cr:%5$ddeg"><i></i><i></i></span>',
			$c, $r, $cx, $cy, $cr
		);
		$n++;
	}
}
$heads = '';
foreach ( $items as $c => $item ) {
	$heads .= '<span class="hub-col" style="--col:' . (int) $c . '">' . e( $item['label'] ) . '</span>';
}
$visual = '<div class="hub-board" style="--cols:' . $cols . '"><div class="hub-cols">' . $heads . '</div>' . $cards . '</div>';
view( 'platform/story', array( 'args' => array( 'items' => $items, 'visual' => $visual, 'kind' => 'hub' ) ) );
