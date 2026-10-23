<?php
/** Controles de carrossel: anterior/próximo, indicadores e pausa (rótulos do painel). */
defined( 'FCK_ROOT' ) || exit;
$total = (int) $args['total'];
?>
<div class="slider__controls">
	<div class="slider__dots">
		<?php for ( $i = 0; $i < $total; $i++ ) : ?>
			<button class="slider__dot" type="button" data-slide-to="<?php echo (int) $i; ?>" aria-label="<?php echo e( fill( ui( 'a11y', 'go_to_item' ), array( 'n' => $i + 1, 'total' => $total ) ) ); ?>"<?php echo 0 === $i ? ' aria-current="true"' : ''; ?>></button>
		<?php endfor; ?>
	</div>
	<div class="slider__buttons">
		<button class="icon-btn" type="button" data-slider-pause aria-pressed="false" data-label-pause="<?php echo e( ui( 'a11y', 'pause' ) ); ?>" data-label-play="<?php echo e( ui( 'a11y', 'play' ) ); ?>" aria-label="<?php echo e( ui( 'a11y', 'pause' ) ); ?>">
			<span data-icon-pause><?php echo icon( 'pause' ); // phpcs:ignore ?></span><span data-icon-play hidden><?php echo icon( 'play' ); // phpcs:ignore ?></span>
		</button>
		<button class="icon-btn" type="button" data-slider-prev aria-label="<?php echo e( ui( 'a11y', 'previous' ) ); ?>"><?php echo icon( 'arrow-left' ); // phpcs:ignore ?></button>
		<button class="icon-btn" type="button" data-slider-next aria-label="<?php echo e( ui( 'a11y', 'next' ) ); ?>"><?php echo icon( 'arrow' ); // phpcs:ignore ?></button>
	</div>
</div>
