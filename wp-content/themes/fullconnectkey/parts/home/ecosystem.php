<?php
/** Home 4 — Ecossistema: cinco plataformas com cor oficial, resumo e link. */
defined( 'ABSPATH' ) || exit;
$d         = $args['data'];
$platforms = fck_posts( 'fck_platform' );
if ( ! $platforms ) {
	return;
}
?>
<section class="section eco-home" data-theme="dark" aria-labelledby="eco-home-title">
	<div class="container">
		<div class="eco-home__head">
			<?php fck_section_head( array( 'title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'id' => 'eco-home-title', 'size' => 'display-l' ) ); ?>
		</div>
		<ul class="eco-home__list">
			<?php foreach ( $platforms as $i => $p ) : ?>
				<?php
				$id      = fck_meta( $p->ID, 'identity' );
				$product = (string) ( $id['product'] ?? '' );
				?>
				<li class="eco-home__item reveal" data-product="<?php echo esc_attr( $product ); ?>" style="--delay:<?php echo esc_attr( (string) ( 0.06 * $i ) ); ?>s">
					<a class="eco-home__link" href="<?php echo esc_url( get_permalink( $p ) ); ?>">
						<span class="eco-home__index" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span class="eco-home__name">
							<?php echo fck_wordmark( $p->post_title, (int) ( $id['logo'] ?? 0 ) ); // phpcs:ignore ?>
						</span>
						<?php fck_text_el( $id['summary'] ?? '', 'span', 'eco-home__summary' ); ?>
						<span class="eco-home__arrow" aria-hidden="true"><?php echo fck_icon( 'arrow' ); // phpcs:ignore ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<div class="eco-home__cta"><?php fck_button( $d['cta'] ?? null, 'secondary' ); ?></div>
	</div>
</section>
