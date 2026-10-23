<?php
/**
 * FullMatch — seletor de perfis (Recorrente / Alto valor / Inativo) com tipo
 * de oferta e ciclo de recompra, de forma conceitual (sem números).
 */
defined( 'FCK_ROOT' ) || exit;
$items    = $args['items'];
$inter    = $args['inter'];
$profiles = array_values( (array) ( $inter['profiles'] ?? array() ) );
$offer_l  = (string) ( $inter['offer_label'] ?? '' );
$cycle_l  = (string) ( $inter['cycle_label'] ?? '' );
?>
<div class="selector selector--match" data-module="tabs" data-tabs-hover="false">
	<div class="selector__tabs" role="tablist" aria-labelledby="solution-title">
		<?php foreach ( $items as $i => $item ) : ?>
			<button class="selector__tab" type="button" role="tab" id="match-tab-<?php echo (int) $i; ?>" aria-controls="match-panel-<?php echo (int) $i; ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>"><?php echo e( $item['label'] ); ?></button>
		<?php endforeach; ?>
	</div>
	<?php foreach ( $items as $i => $item ) : ?>
		<?php $pr = $profiles[ $i ] ?? array(); ?>
		<div class="selector__panel" id="match-panel-<?php echo (int) $i; ?>" role="tabpanel" aria-labelledby="match-tab-<?php echo (int) $i; ?>" tabindex="0" <?php echo 0 === $i ? '' : 'hidden'; ?>>
			<div class="selector__text">
				<?php text_el( $item['headline'] ?? '', 'h3', 'display-s' ); ?>
				<?php text_el( $item['text'] ?? '', 'p', 'muted' ); ?>
				<?php if ( ( $offer_l && has_text( $pr['offer'] ?? '' ) ) || ( $cycle_l && has_text( $pr['cycle'] ?? '' ) ) ) : ?>
					<dl class="match-details">
						<?php if ( $offer_l && has_text( $pr['offer'] ?? '' ) ) : ?><div><dt><?php echo e( $offer_l ); ?></dt><dd><?php echo e( $pr['offer'] ); ?></dd></div><?php endif; ?>
						<?php if ( $cycle_l && has_text( $pr['cycle'] ?? '' ) ) : ?><div><dt><?php echo e( $cycle_l ); ?></dt><dd><?php echo e( $pr['cycle'] ); ?></dd></div><?php endif; ?>
					</dl>
				<?php endif; ?>
			</div>
			<div class="selector__visual" aria-hidden="true">
				<svg viewBox="0 0 320 220" class="match-cycle match-cycle--<?php echo (int) ( $i % 3 ); ?>">
					<circle cx="160" cy="110" r="80" class="match-ring"/>
					<?php for ( $k = 0; $k < 6; $k++ ) : ?>
						<?php $a = -M_PI / 2 + $k * M_PI / 3; ?>
						<circle class="match-dot" style="--k:<?php echo (int) $k; ?>" cx="<?php echo e( (string) round( 160 + 80 * cos( $a ), 1 ) ); ?>" cy="<?php echo e( (string) round( 110 + 80 * sin( $a ), 1 ) ); ?>" r="7"/>
					<?php endfor; ?>
					<circle cx="160" cy="110" r="26" class="match-core"/>
				</svg>
			</div>
		</div>
	<?php endforeach; ?>
</div>
