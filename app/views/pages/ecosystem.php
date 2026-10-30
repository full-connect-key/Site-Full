<?php
/**
 * Ecossistema Full — hero ("Seu negócio no centro" como subtítulo), jornada,
 * navegação interativa entre as soluções (o negócio do cliente no centro do
 * diagrama; cada item com verbo, card e "Como se conecta"), nota de
 * independência, linha do método e CTA. S&M aparece só no texto, como
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
	<?php if ( $intro = entry_intro_video( $entry ) ) : ?>
	<a href="<?php echo e( url( uploads_rel() . '/' . $intro['path'] ) ); ?>" data-video-autoopen hidden aria-label="<?php echo e( (string) ( $intro['alt'] ?: $entry['title'] ) ); ?>"></a>
<?php endif; ?>
	<section class="section eco" data-theme="dark" aria-labelledby="eco-title">
		<div class="container">
			<?php text_el( $sol['intro'] ?? '', 'p', 'lead eco__journey' ); ?>
			<?php if ( has_text( $sol['title'] ?? '' ) ) : ?>
				<h2 id="eco-title" class="display-m eco__title"><?php echo e( $sol['title'] ); ?></h2>
			<?php else : ?>
				<h2 id="eco-title" class="screen-reader-text"><?php echo e( $entry['title'] ); ?></h2>
			<?php endif; ?>
			<?php
			// Com vídeo de apresentação (ADR-022), clicar no produto ou no nó abre o vídeo; o hover (desktop) troca o painel.
			$has_video = (bool) array_filter( $platforms, fn( $p ) => entry_video( $p ) );
			?>
			<div class="eco__nav" data-module="tabs eco" data-tabs-hover="<?php echo $has_video ? 'true' : 'false'; ?>">
				<div class="eco__list" role="tablist" aria-orientation="vertical" aria-labelledby="eco-title">
					<?php foreach ( $platforms as $i => $p ) : ?>
						<?php $id = g( $p, 'identity' ); ?>
						<button class="eco__tab" type="button" role="tab" id="eco-tab-<?php echo (int) $i; ?>" aria-controls="eco-panel-<?php echo (int) $i; ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>" data-product="<?php echo e( $id['product'] ?? '' ); ?>" data-eco-index="<?php echo (int) $i; ?>"<?php echo entry_video( $p ) ? ' data-eco-video' : ''; ?>>
							<span class="eco__tab-dot" aria-hidden="true"></span>
							<span class="eco__tab-name"><?php echo e( $p['title'] ); ?></span>
							<?php text_el( $id['verb'] ?? '', 'span', 'eco__tab-sig small' ); ?>
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
						$label = has_text( $sol['link_label'] ?? '' ) ? str_replace( '{platform}', $p['title'], $sol['link_label'] ) : $p['title'];
						?>
						<article class="eco__panel" id="eco-panel-<?php echo (int) $i; ?>" role="tabpanel" aria-labelledby="eco-tab-<?php echo (int) $i; ?>" tabindex="0" data-product="<?php echo e( $id['product'] ?? '' ); ?>" <?php echo 0 === $i ? '' : 'hidden'; ?>>
							<div class="eco__logo"><?php echo wordmark( $p['title'], (int) ( $id['logo'] ?? 0 ) ); // phpcs:ignore ?></div>
							<?php if ( media_ok( $id['eco_image'] ?? 0 ) ) : ?>
								<div class="media-frame eco__media"><?php echo media_html( $id['eco_image'], array( 'size' => 'card', 'sizes' => '(min-width: 64rem) 30vw, 100vw' ) ); // phpcs:ignore ?></div>
							<?php endif; ?>
							<?php text_el( $id['verb'] ?? '', 'p', 'eyebrow eco__verb' ); ?>
							<?php text_el( $id['card_title'] ?? '', 'h3', 'display-s eco__headline' ); ?>
							<?php text_el( $id['card_text'] ?? '', 'p', 'muted' ); ?>
							<?php if ( has_text( $id['connects'] ?? '' ) ) : ?>
								<div class="eco__connects">
									<?php text_el( $sol['connects_label'] ?? '', 'p', 'small eco__connects-label' ); ?>
									<p class="eco__connects-text"><?php echo e( $id['connects'] ); ?></p>
								</div>
							<?php endif; ?>
							<a class="link-arrow eco__more" href="<?php echo e( url_for( $p ) ); ?>"><?php echo e( $label ); ?> <?php echo icon( 'arrow', 'btn__arrow' ); // phpcs:ignore ?></a>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
			<?php if ( has_text( $sol['independence'] ?? '' ) || has_text( $sol['method_note'] ?? '' ) ) : ?>
				<div class="eco__notes">
					<?php text_el( $sol['independence'] ?? '', 'p', 'lead eco__independence' ); ?>
					<?php text_el( $sol['method_note'] ?? '', 'p', 'small subtle eco__method' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
endif;

cta_band( g( $entry, 'cta' ), 'eco-cta' );
