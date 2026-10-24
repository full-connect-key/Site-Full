<?php
/**
 * FullMix — simulador ILUSTRATIVO de distribuição de orçamento entre canais.
 * Em demonstração automática enquanto a pessoa rola (os sliders se movem
 * sozinhos); ao pegar um slider, a demonstração para e ela assume o
 * controle. Aviso obrigatório sempre visível; sem projeção de vendas.
 */
defined( 'FCK_ROOT' ) || exit;
$inter    = $args['inter'];
$channels = array_values( array_filter( (array) ( $inter['channels'] ?? array() ), fn( $c ) => has_text( $c['name'] ?? '' ) ) );
$note     = (string) ( $inter['illustrative_note'] ?? '' );
?>

<?php if ( count( $channels ) >= 2 && has_text( $note ) ) : // sem o aviso obrigatório, o simulador não é exibido ?>
	<?php
	$total = array_sum( array_map( fn( $c ) => max( 0, (int) ( $c['share'] ?? 0 ) ), $channels ) ) ?: 1;
	?>
	<div class="mix-sim" data-module="mix-sim" data-demo="on" aria-labelledby="mix-sim-intro">
		<div class="mix-sim__head">
			<?php text_el( $inter['simulator_intro'] ?? '', 'p', 'lead', array( 'id' => 'mix-sim-intro' ) ); ?>
			<p class="mix-sim__note badge-illustrative" role="note"><?php echo e( $note ); ?></p>
		</div>
		<div class="mix-sim__body">
			<div class="mix-sim__controls">
				<?php foreach ( $channels as $i => $c ) : ?>
					<?php $v = (int) round( max( 0, (int) ( $c['share'] ?? 0 ) ) * 100 / $total ); ?>
					<div class="mix-sim__row" style="--i:<?php echo (int) $i; ?>">
						<label for="mix-<?php echo (int) $i; ?>"><?php echo e( $c['name'] ); ?></label>
						<input type="range" id="mix-<?php echo (int) $i; ?>" min="0" max="100" step="1" value="<?php echo (int) $v; ?>" data-initial="<?php echo (int) $v; ?>" data-kind="<?php echo e( $c['kind'] ?? 'online' ); ?>" aria-valuetext="<?php echo (int) $v; ?>%">
						<output for="mix-<?php echo (int) $i; ?>" data-out><?php echo (int) $v; ?>%</output>
					</div>
				<?php endforeach; ?>
				<?php if ( has_text( $inter['reset_label'] ?? '' ) ) : ?>
					<button class="btn btn--ghost btn--sm" type="button" data-mix-reset><?php echo e( $inter['reset_label'] ); ?></button>
				<?php endif; ?>
			</div>
			<div class="mix-sim__viz" aria-hidden="true">
				<div class="mix-sim__stack">
					<?php foreach ( $channels as $i => $c ) : ?>
						<span class="mix-sim__seg" data-seg style="--i:<?php echo (int) $i; ?>"></span>
					<?php endforeach; ?>
				</div>
				<ul class="mix-sim__legend">
					<?php foreach ( $channels as $i => $c ) : ?>
						<li style="--i:<?php echo (int) $i; ?>"><span class="mix-sim__swatch"></span><?php echo e( $c['name'] ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<?php if ( has_text( $inter['total_label'] ?? '' ) ) : ?>
			<p class="mix-sim__total"><?php echo e( $inter['total_label'] ); ?>: <strong data-mix-total>100%</strong></p>
		<?php endif; ?>
	</div>
<?php endif; ?>
