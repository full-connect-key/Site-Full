<?php
/**
 * Destaque editorial: artigo grande + manchetes como abas (tablist).
 * Usado na Home e na página Insights.
 */
defined( 'ABSPATH' ) || exit;
$articles = $args['articles'];
$id       = $args['id'];
$read     = $args['read_label'] ?? '';
$label    = $args['label'] ?? '';
?>
<div class="feature" data-module="tabs">
	<div class="feature__panels">
		<?php foreach ( $articles as $i => $a ) : ?>
			<?php
			$cat = get_the_terms( $a, 'fck_article_cat' );
			$cat = $cat && ! is_wp_error( $cat ) ? $cat[0]->name : '';
			?>
			<article class="feature__panel" id="<?php echo esc_attr( "$id-panel-$i" ); ?>" role="tabpanel" aria-labelledby="<?php echo esc_attr( "$id-tab-$i" ); ?>" tabindex="0" <?php echo 0 === $i ? '' : 'hidden'; ?>>
				<a class="feature__link" href="<?php echo esc_url( get_permalink( $a ) ); ?>">
					<?php if ( has_post_thumbnail( $a ) ) : ?>
						<span class="media-frame feature__media"><?php echo fck_media( get_post_thumbnail_id( $a ), array( 'size' => 'fck-wide', 'sizes' => '(min-width: 64rem) 60vw, 100vw', 'decorative' => true ) ); // phpcs:ignore ?></span>
					<?php endif; ?>
					<span class="feature__body">
						<?php if ( $cat ) : ?><span class="eyebrow"><?php echo esc_html( $cat ); ?></span><?php endif; ?>
						<span class="feature__title display-s"><?php echo esc_html( $a->post_title ); ?></span>
						<?php if ( has_excerpt( $a ) ) : ?><span class="feature__excerpt muted"><?php echo esc_html( get_the_excerpt( $a ) ); ?></span><?php endif; ?>
						<?php if ( $read ) : ?><span class="link-arrow"><?php echo esc_html( $read ); ?> <?php echo fck_icon( 'arrow', 'btn__arrow' ); // phpcs:ignore ?></span><?php endif; ?>
					</span>
				</a>
			</article>
		<?php endforeach; ?>
	</div>
	<?php if ( count( $articles ) > 1 ) : ?>
		<div class="feature__aside">
			<?php if ( $label ) : ?><p class="eyebrow" id="<?php echo esc_attr( "$id-tabs-label" ); ?>"><?php echo esc_html( $label ); ?></p><?php endif; ?>
			<div class="feature__tabs" role="tablist" aria-orientation="vertical" <?php echo $label ? 'aria-labelledby="' . esc_attr( "$id-tabs-label" ) . '"' : ''; ?>>
				<?php foreach ( $articles as $i => $a ) : ?>
					<button class="feature__tab" type="button" role="tab" id="<?php echo esc_attr( "$id-tab-$i" ); ?>" aria-controls="<?php echo esc_attr( "$id-panel-$i" ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>">
						<span class="feature__tab-date small subtle"><?php echo esc_html( get_the_date( '', $a ) ); ?></span>
						<span class="feature__tab-title"><?php echo esc_html( $a->post_title ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
