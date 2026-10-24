<?php
/**
 * FullDash — linhas de ritmo: "planejado" (tracejada) desenha-se no passo 1
 * com os blocos de orçamento; "realizado" avança e se afasta no passo 2
 * (área de desvio + pulso de atenção); no passo 3 setas de realocação e a
 * linha volta a se alinhar ao plano. Sem números nem eixos com valores.
 */
defined( 'FCK_ROOT' ) || exit;
$in = $args['inter'];
$l  = static fn( string $k ): string => (string) ( $in[ $k ] ?? '' );
?>
<svg class="viz viz-dash" viewBox="0 0 420 270" data-crosshair>
	<line class="viz-axis" x1="20" y1="214" x2="404" y2="214"/>
	<g class="dash-planned"><path class="dash-planned__line" d="M20 178 C 130 160, 240 128, 400 72"/></g>
	<path class="dash-dev-area" d="M150 156 C 200 150, 240 134, 300 112 L 300 164 C 240 176, 200 170, 150 156 Z"/>
	<g class="dash-actual"><path class="dash-actual__line" d="M20 178 C 110 168, 200 178, 300 164"/></g>
	<g class="dash-fix"><path class="dash-fix__line" d="M300 164 C 330 140, 360 100, 400 76"/></g>
	<circle class="dash-pulse" cx="300" cy="164" r="7"/>
	<circle class="dash-pulse dash-pulse--ring" cx="300" cy="164" r="7"/>
	<?php foreach ( array( 60, 170, 280 ) as $i => $x ) : ?>
		<rect class="dash-block" style="--i:<?php echo (int) $i; ?>" x="<?php echo (int) $x; ?>" y="228" width="80" height="22" rx="11"/>
	<?php endforeach; ?>
	<path class="dash-arrow" pathLength="1" d="M142 239 C 152 224, 162 224, 168 236"/>
	<path class="dash-arrow" pathLength="1" d="M252 239 C 262 224, 272 224, 278 236"/>
	<?php if ( has_text( $l( 'label_planned' ) ) ) : ?><text class="viz-label dash-label-planned" x="400" y="62" text-anchor="end"><?php echo e( $l( 'label_planned' ) ); ?></text><?php endif; ?>
	<?php if ( has_text( $l( 'label_actual' ) ) ) : ?><text class="viz-label dash-label-actual" x="300" y="190" text-anchor="middle"><?php echo e( $l( 'label_actual' ) ); ?></text><?php endif; ?>
	<?php if ( has_text( $l( 'label_adjust' ) ) ) : ?><text class="viz-label dash-label-adjust" x="210" y="222" text-anchor="middle"><?php echo e( $l( 'label_adjust' ) ); ?></text><?php endif; ?>
	<g class="viz-cross"><line x1="0" y1="20" x2="0" y2="214"/><text class="viz-label" x="6" y="30" data-cross-label data-labels="<?php echo e( implode( '|', array( $l( 'label_planned' ), $l( 'label_actual' ), $l( 'label_adjust' ) ) ) ); ?>"></text></g>
</svg>
