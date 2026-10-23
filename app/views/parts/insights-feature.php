<?php
/**
 * Destaque editorial: artigo grande + manchetes como abas (tablist).
 * Usado na Home e na página Insights.
 */
defined( 'FCK_ROOT' ) || exit;
$articles = $args['articles'];
$id       = $args['id'];
$read     = $args['read_label'] ?? '';
$label    = $args['label'] ?? '';
?>
<div class="feature" data-module="tabs">
	<div class="feature__panels">
		<?php foreach ( $articles as $i => $a ) : ?>
			<?php
			$ad  = g( $a, 'details' );
			$ce  = ! empty( $ad['category'] ) ? entry_find( (int) $ad['category'] ) : null;
			$cat = $ce ? $ce['title'] : '';
			?>
			<article class="feature__panel" id="<?php echo e( "$id-panel-$i" ); ?>" role="tabpanel" aria-labelledby="<?php echo e( "$id-tab-$i" ); ?>" tabindex="0" <?php echo 0 === $i ? '' : 'hidden'; ?>>
				<a class="feature__link" href="<?php echo e( url_for( $a ) ); ?>">
					<?php if ( media_ok( $ad['cover'] ?? 0 ) ) : ?>
						<span class="media-frame feature__media"><?php echo media_html( $ad['cover'], array( 'size' => 'wide', 'sizes' => '(min-width: 64rem) 60vw, 100vw', 'decorative' => true ) ); // phpcs:ignore ?></span>
					<?php endif; ?>
					<span class="feature__body">
						<?php if ( $cat ) : ?><span class="eyebrow"><?php echo e( $cat ); ?></span><?php endif; ?>
						<span class="feature__title display-s"><?php echo e( $a['title'] ); ?></span>
						<?php if ( has_text( $ad['excerpt'] ?? '' ) ) : ?><span class="feature__excerpt muted"><?php echo e( $ad['excerpt'] ); ?></span><?php endif; ?>
						<?php if ( $read ) : ?><span class="link-arrow"><?php echo e( $read ); ?> <?php echo icon( 'arrow', 'btn__arrow' ); // phpcs:ignore ?></span><?php endif; ?>
					</span>
				</a>
			</article>
		<?php endforeach; ?>
	</div>
	<?php if ( count( $articles ) > 1 ) : ?>
		<div class="feature__aside">
			<?php if ( $label ) : ?><p class="eyebrow" id="<?php echo e( "$id-tabs-label" ); ?>"><?php echo e( $label ); ?></p><?php endif; ?>
			<div class="feature__tabs" role="tablist" aria-orientation="vertical" <?php echo $label ? 'aria-labelledby="' . e( "$id-tabs-label" ) . '"' : ''; ?>>
				<?php foreach ( $articles as $i => $a ) : ?>
					<button class="feature__tab" type="button" role="tab" id="<?php echo e( "$id-tab-$i" ); ?>" aria-controls="<?php echo e( "$id-panel-$i" ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>">
						<span class="feature__tab-date small subtle"><?php echo e( format_date( article_date( $a ) ) ); ?></span>
						<span class="feature__tab-title"><?php echo e( $a['title'] ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
