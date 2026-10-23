<?php
/** Card de artigo (grid de Insights e relacionados). */
defined( 'FCK_ROOT' ) || exit;
$a     = $args['article'];
$cta   = $args['cta'] ?? '';
$level = $args['level'] ?? 'h3';
$ad    = g( $a, 'details' );
$ce    = ! empty( $ad['category'] ) ? entry_find( (int) $ad['category'] ) : null;
$min   = reading_minutes( (string) ( $ad['body'] ?? '' ) );
?>
<article class="post-card">
	<a class="post-card__link" href="<?php echo e( url_for( $a ) ); ?>">
		<span class="media-frame post-card__media<?php echo media_ok( $ad['cover'] ?? 0 ) ? '' : ' is-empty'; ?>">
			<?php if ( media_ok( $ad['cover'] ?? 0 ) ) : ?>
				<?php echo media_html( $ad['cover'], array( 'size' => 'card', 'sizes' => '(min-width: 64rem) 33vw, (min-width: 48rem) 50vw, 100vw', 'decorative' => true ) ); // phpcs:ignore ?>
			<?php endif; ?>
		</span>
		<span class="post-card__body">
			<?php if ( $ce ) : ?><span class="eyebrow post-card__cat"><?php echo e( $ce['title'] ); ?></span><?php endif; ?>
			<<?php echo $level; // phpcs:ignore -- h2/h3 fixo pelo template. ?> class="post-card__title"><?php echo e( $a['title'] ); ?></<?php echo $level; // phpcs:ignore ?>>
			<?php text_el( $ad['excerpt'] ?? '', 'span', 'post-card__excerpt muted' ); ?>
			<span class="post-card__meta small subtle">
				<time datetime="<?php echo e( substr( article_date( $a ), 0, 10 ) ); ?>"><?php echo e( format_date( article_date( $a ) ) ); ?></time>
				<?php if ( has_text( ui( 'content', 'reading_time' ) ) ) : ?> · <?php echo e( str_replace( '{n}', (string) $min, ui( 'content', 'reading_time' ) ) ); ?><?php endif; ?>
			</span>
			<?php if ( has_text( $cta ) ) : ?><span class="link-arrow post-card__cta"><?php echo e( $cta ); ?> <?php echo icon( 'arrow', 'btn__arrow' ); // phpcs:ignore ?></span><?php endif; ?>
		</span>
	</a>
</article>
