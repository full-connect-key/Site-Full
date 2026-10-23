<?php
/**
 * Home 8 — Depoimentos: carrossel com troca a cada ~6 s, setas, arraste e
 * indicadores; pausa no hover/foco, botão de pausa e sem autoplay com
 * movimento reduzido. Só depoimentos com autorização registrada.
 */
defined( 'FCK_ROOT' ) || exit;
$d     = $args['data'];
$items = array_values(
	array_filter(
		entries_of( 'testimonial' ),
		static function ( array $t ): bool {
			$td = g( $t, 'details' );
			return ! empty( $td['authorized'] ) && has_text( $td['quote'] ?? '' );
		}
	)
);
if ( ! $items ) {
	return;
}
$total = count( $items );
?>
<section class="section testimonials" data-theme="dark" aria-labelledby="testimonials-title">
	<div class="container">
		<?php section_head( array( 'title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'id' => 'testimonials-title', 'size' => 'display-l' ) ); ?>
		<div class="slider" data-module="slider" data-interval="6000" aria-labelledby="testimonials-title">
			<div class="slider__viewport">
				<div class="slider__track">
					<?php foreach ( $items as $i => $t ) : ?>
						<?php $td = g( $t, 'details' ); ?>
						<div class="slider__slide testimonial" role="group" aria-label="<?php echo e( fill( ui( 'a11y', 'progress' ), array( 'n' => $i + 1, 'total' => $total ) ) ); ?>" data-slide>
							<blockquote class="testimonial__quote">
								<p class="display-s"><?php echo e( $td['quote'] ); ?></p>
							</blockquote>
							<div class="testimonial__author">
								<?php if ( media_ok( $td['photo'] ?? 0 ) ) : ?>
									<span class="testimonial__photo"><?php echo media_html( $td['photo'], array( 'size' => 'thumb', 'decorative' => true ) ); // phpcs:ignore ?></span>
								<?php endif; ?>
								<p>
									<strong><?php echo e( $t['title'] ); ?></strong>
									<?php $meta = implode( ' · ', array_filter( array( $td['role'] ?? '', $td['company'] ?? '' ), 'has_text' ) ); ?>
									<?php if ( $meta ) : ?><br><span class="small subtle"><?php echo e( $meta ); ?></span><?php endif; ?>
								</p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php if ( $total > 1 ) : ?>
				<?php view( 'parts/slider-controls', array( 'args' => array( 'total' => $total ) ) ); ?>
			<?php endif; ?>
		</div>
	</div>
</section>
