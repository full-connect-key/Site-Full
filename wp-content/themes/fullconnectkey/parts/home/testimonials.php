<?php
/**
 * Home 8 — Depoimentos: carrossel com troca a cada ~6 s, setas, arraste e
 * indicadores; pausa no hover/foco, botão de pausa e sem autoplay com
 * movimento reduzido. Só depoimentos com autorização registrada.
 */
defined( 'ABSPATH' ) || exit;
$d     = $args['data'];
$items = array_values(
	array_filter(
		fck_posts( 'fck_testimonial' ),
		static function ( WP_Post $t ): bool {
			$td = fck_meta( $t->ID, 'details' );
			return ! empty( $td['authorized'] ) && fck_has_text( $td['quote'] ?? '' );
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
		<?php fck_section_head( array( 'title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'id' => 'testimonials-title', 'size' => 'display-l' ) ); ?>
		<div class="slider" data-module="slider" data-interval="6000" aria-labelledby="testimonials-title">
			<div class="slider__viewport">
				<ul class="slider__track">
					<?php foreach ( $items as $i => $t ) : ?>
						<?php $td = fck_meta( $t->ID, 'details' ); ?>
						<li class="slider__slide testimonial" role="group" aria-label="<?php echo esc_attr( fck_fill( fck_ui( 'a11y', 'progress' ), array( 'n' => $i + 1, 'total' => $total ) ) ); ?>" data-slide>
							<blockquote class="testimonial__quote">
								<p class="display-s"><?php echo esc_html( $td['quote'] ); ?></p>
							</blockquote>
							<div class="testimonial__author">
								<?php if ( fck_attachment_ok( $td['photo'] ?? 0 ) ) : ?>
									<span class="testimonial__photo"><?php echo fck_media( $td['photo'], array( 'size' => 'thumbnail', 'decorative' => true ) ); // phpcs:ignore ?></span>
								<?php endif; ?>
								<p>
									<strong><?php echo esc_html( $t->post_title ); ?></strong>
									<?php $meta = implode( ' · ', array_filter( array( $td['role'] ?? '', $td['company'] ?? '' ), 'fck_has_text' ) ); ?>
									<?php if ( $meta ) : ?><br><span class="small subtle"><?php echo esc_html( $meta ); ?></span><?php endif; ?>
								</p>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php if ( $total > 1 ) : ?>
				<?php get_template_part( 'parts/slider-controls', null, array( 'total' => $total ) ); ?>
			<?php endif; ?>
		</div>
	</div>
</section>
