<?php
/**
 * FullMix — passo 1: fluxos de dados entram pelas laterais e convergem;
 * passo 2: curvas de resposta com faixa de incerteza (estimativa); passo 3:
 * barras de canais se redistribuem com a rolagem (demonstração). Proporções
 * ilustrativas, sem percentuais.
 */
defined( 'FCK_ROOT' ) || exit;
$in       = $args['inter'];
$flows    = array_slice( ( $args['lines'] )( (string) ( $in['flows'] ?? '' ) ), 0, 6 );
$channels = array_slice( array_values( array_filter( (array) ( $in['channels'] ?? array() ), fn( $c ) => has_text( $c['name'] ?? '' ) ) ), 0, 6 );
$half     = (int) ceil( count( $flows ) / 2 );
?>
<svg class="viz viz-mix" viewBox="0 0 420 300">
	<g class="mix-flows">
		<?php foreach ( $flows as $i => $f ) : ?>
			<?php
			$left = $i < $half;
			$row  = $left ? $i : $i - $half;
			$y    = 50 + $row * 70;
			$x    = $left ? 18 : 402;
			?>
			<path class="mix-flow" style="--i:<?php echo (int) $i; ?>" pathLength="1" d="M<?php echo $left ? 70 : 350; ?> <?php echo (int) $y; ?> C <?php echo $left ? 140 : 280; ?> <?php echo (int) $y; ?>, <?php echo $left ? 160 : 260; ?> 135, 210 135"/>
			<text class="viz-label mix-flow__label" style="--i:<?php echo (int) $i; ?>" x="<?php echo (int) $x; ?>" y="<?php echo (int) ( $y + 4 ); ?>" text-anchor="<?php echo $left ? 'start' : 'end'; ?>"><?php echo e( $f ); ?></text>
		<?php endforeach; ?>
		<circle class="mix-core" cx="210" cy="135" r="18"/>
	</g>
	<g class="mix-curves">
		<path class="mix-band" d="M30 230 C 90 140, 170 92, 390 70 L 390 108 C 170 128, 90 178, 30 236 Z"/>
		<?php foreach ( array( 'M30 232 C 90 160, 170 108, 390 88', 'M30 232 C 110 190, 210 160, 390 146', 'M30 232 C 140 214, 260 196, 390 186' ) as $k => $d ) : ?>
			<path class="mix-curve" style="--i:<?php echo (int) $k; ?>" pathLength="1" d="<?php echo e( $d ); ?>"/>
		<?php endforeach; ?>
		<?php if ( has_text( $in['label_contribution'] ?? '' ) ) : ?><text class="viz-label mix-curves__label" x="30" y="24" text-anchor="start"><?php echo e( $in['label_contribution'] ); ?></text><?php endif; ?>
	</g>
	<g class="mix-bars">
		<?php $w = 340 / max( 1, count( $channels ) ); ?>
		<?php foreach ( $channels as $i => $c ) : ?>
			<?php
			// Duas distribuições fixas e ilustrativas; a rolagem interpola entre elas.
			$a = array( 0.55, 0.35, 0.3, 0.8, 0.75, 0.4 )[ $i % 6 ];
			$b = array( 0.35, 0.3, 0.45, 0.6, 0.9, 0.7 )[ $i % 6 ];
			?>
			<rect class="mix-bar" style="--i:<?php echo (int) $i; ?>;--a:<?php echo e( (string) $a ); ?>;--b:<?php echo e( (string) $b ); ?>" x="<?php echo e( (string) round( 40 + $i * $w + $w * 0.18, 1 ) ); ?>" y="40" width="<?php echo e( (string) round( $w * 0.64, 1 ) ); ?>" height="180" rx="6"/>
			<?php $lx = round( 40 + $i * $w + $w / 2 + 4, 1 ); // nomes inclinados: canais longos não se sobrepõem ?>
			<text class="viz-label mix-bar__label" x="<?php echo e( (string) $lx ); ?>" y="236" text-anchor="end" transform="rotate(-35 <?php echo e( (string) $lx ); ?> 236)"><?php echo e( $c['name'] ); ?></text>
		<?php endforeach; ?>
	</g>
</svg>
