<?php
/**
 * Ecossistema Full — hero, navegação interativa entre as soluções (o negócio
 * do cliente no centro do diagrama) e CTA. S&M aparece só no texto, como
 * método transversal — nunca como nó do diagrama.
 */
defined( 'FCK_ROOT' ) || exit;
$sol = g( $entry, 'solutions' );

// Plataformas: ordem escolhida no painel ou todas, na ordem do menu Plataformas.
$platforms = array();
foreach ( (array) ( $sol['platforms'] ?? array() ) as $pid ) {
	$p = entry_find( (int) $pid );
	if ( $p && 'platform' === $p['type'] ) {
		$platforms[] = $p;
	}
}
if ( ! $platforms ) {
	$platforms = entries_of( 'platform' );
}

view( 'parts/page-hero', array( 'args' => array( 'data' => g( $entry, 'hero' ) ) ) );

if ( $platforms ) :
	$total  = count( $platforms );
	$center = has_text( $sol['center_label'] ?? '' ) ? $sol['center_label'] : '';
	?>
	<section class="section eco" data-theme="dark" aria-labelledby="eco-title">
		<div class="container">
			<?php section_head( array( 'title' => $sol['title'] ?? '', 'text' => $sol['intro'] ?? '', 'id' => 'eco-title', 'size' => 'display-l' ) ); ?>
			<div class="eco__nav" data-module="tabs eco" data-tabs-hover="false">
				<div class="eco__list" role="tablist" aria-orientation="vertical" aria-labelledby="eco-title">
					<?php foreach ( $platforms as $i => $p ) : ?>
						<?php $id = g( $p, 'identity' ); ?>
						<button class="eco__tab" type="button" role="tab" id="eco-tab-<?php echo (int) $i; ?>" aria-controls="eco-panel-<?php echo (int) $i; ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>" data-product="<?php echo e( $id['product'] ?? '' ); ?>" data-eco-index="<?php echo (int) $i; ?>">
							<span class="eco__tab-dot" aria-hidden="true"></span>
							<span class="eco__tab-name"><?php echo e( $p['title'] ); ?></span>
							<?php text_el( $id['signature'] ?? '', 'span', 'eco__tab-sig small' ); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<div class="eco__stage">
					<svg class="eco__diagram" viewBox="0 0 400 400" aria-hidden="true" focusable="false">
						<circle cx="200" cy="200" r="150" class="eco__orbit" />
						<?php foreach ( $platforms as $i => $p ) : ?>
							<?php
							$a = -M_PI / 2 + $i * 2 * M_PI / $total;
							$x = round( 200 + 150 * cos( $a ), 2 );
							$y = round( 200 + 150 * sin( $a ), 2 );
							?>
							<g class="eco__node<?php echo 0 === $i ? ' is-active' : ''; ?>" data-product="<?php echo e( g( $p, 'identity' )['product'] ?? '' ); ?>" data-eco-node="<?php echo (int) $i; ?>">
								<line x1="200" y1="200" x2="<?php echo e( (string) $x ); ?>" y2="<?php echo e( (string) $y ); ?>" class="eco__link" />
								<circle cx="<?php echo e( (string) $x ); ?>" cy="<?php echo e( (string) $y ); ?>" r="10" class="eco__dot" />
							</g>
						<?php endforeach; ?>
						<circle cx="200" cy="200" r="58" class="eco__core" />
					</svg>
					<?php if ( $center ) : ?>
						<p class="eco__center"><?php echo e( $center ); ?></p>
					<?php endif; ?>
				</div>

				<div class="eco__panels">
					<?php foreach ( $platforms as $i => $p ) : ?>
						<?php
						$id    = g( $p, 'identity' );
						$label = has_text( $sol['link_label'] ?? '' ) ? $sol['link_label'] : $p['title'];
						?>
						<article class="eco__panel" id="eco-panel-<?php echo (int) $i; ?>" role="tabpanel" aria-labelledby="eco-tab-<?php echo (int) $i; ?>" tabindex="0" data-product="<?php echo e( $id['product'] ?? '' ); ?>" <?php echo 0 === $i ? '' : 'hidden'; ?>>
							<div class="eco__logo"><?php echo wordmark( $p['title'], (int) ( $id['logo'] ?? 0 ) ); // phpcs:ignore ?></div>
							<?php if ( media_ok( $id['eco_image'] ?? 0 ) ) : ?>
								<div class="media-frame eco__media"><?php echo media_html( $id['eco_image'], array( 'size' => 'card', 'sizes' => '(min-width: 64rem) 30vw, 100vw' ) ); // phpcs:ignore ?></div>
							<?php endif; ?>
							<?php text_el( $id['eco_headline'] ?? '', 'h3', 'display-s eco__headline' ); ?>
							<?php text_el( $id['eco_text'] ?? '', 'p', 'muted' ); ?>
							<a class="link-arrow eco__more" href="<?php echo e( url_for( $p ) ); ?>"><?php echo e( $label ); ?> <?php echo icon( 'arrow', 'btn__arrow' ); // phpcs:ignore ?></a>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
endif;

cta_band( g( $entry, 'cta' ), 'eco-cta' );
