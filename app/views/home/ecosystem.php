<?php
/** Home 4 — Ecossistema: cinco plataformas na ordem da jornada — nome, verbo e uma linha — com cor oficial e link. */
defined( 'FCK_ROOT' ) || exit;
$d         = $args['data'];
$platforms = entries_of( 'platform' );
if ( ! $platforms ) {
	return;
}
?>
<section class="section eco-home" data-theme="dark" aria-labelledby="eco-home-title">
	<div class="container">
		<div class="eco-home__head">
			<?php section_head( array( 'title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'id' => 'eco-home-title', 'size' => 'display-l' ) ); ?>
		</div>
		<ul class="eco-home__list">
			<?php foreach ( $platforms as $i => $p ) : ?>
				<?php
				$id      = g( $p, 'identity' );
				$product = (string) ( $id['product'] ?? '' );
				?>
				<li class="eco-home__item reveal" data-product="<?php echo e( $product ); ?>" style="--delay:<?php echo e( (string) ( 0.06 * $i ) ); ?>s">
					<a class="eco-home__link" href="<?php echo e( url_for( $p ) ); ?>">
						<span class="eco-home__index" aria-hidden="true"><?php echo e( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span class="eco-home__name">
							<?php echo wordmark( $p['title'], (int) ( $id['logo'] ?? 0 ) ); // phpcs:ignore ?>
						</span>
						<span class="eco-home__summary">
							<?php text_el( $id['verb'] ?? '', 'span', 'eco-home__verb' ); ?>
							<?php text_el( $id['home_line'] ?? '', 'span', 'eco-home__line' ); ?>
						</span>
						<span class="eco-home__arrow" aria-hidden="true"><?php echo icon( 'arrow' ); // phpcs:ignore ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<div class="eco-home__cta"><?php button( $d['cta'] ?? null, 'secondary' ); ?></div>
	</div>
</section>
