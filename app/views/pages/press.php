<?php
/**
 * Full na Mídia — hero; faixa horizontal imersiva (a matéria ativa se
 * expande e revela a capa por máscara; as manchetes respondem ao cursor);
 * arquivo com filtros por tipo SOMENTE quando houver volume (limiar do
 * painel); CTA de imprensa (contato com ?assunto=imprensa). Sem matérias:
 * hero, estado vazio e CTA. Somente publicações reais.
 */
defined( 'FCK_ROOT' ) || exit;
$a     = g( $entry, 'archive' );
$items = entries_of( 'press' );
usort( $items, fn( $x, $y ) => strcmp( (string) ( g( $y, 'details' )['date'] ?? '' ), (string) ( g( $x, 'details' )['date'] ?? '' ) ) );
$items     = array_values( array_filter( $items, fn( $p ) => has_text( g( $p, 'details' )['url'] ?? '' ) ) );
$threshold = max( 1, (int) ( $a['threshold'] ?? 6 ) );
$archive   = count( $items ) >= $threshold;
$strip     = $archive ? array_slice( $items, 0, 6 ) : $items;
$kinds     = fck_press_kinds();
$ext       = ui( 'a11y', 'external_link' );
$kind_lbl  = static fn( string $k ): string => (string) ( $a[ 'filter_' . $k ] ?? '' ) ?: ( $kinds[ $k ] ?? '' );

view( 'parts/page-hero', array( 'args' => array( 'data' => g( $entry, 'hero' ) ) ) );
?>
<section class="section press" data-theme="dark" aria-labelledby="press-title">
	<div class="container">
		<?php section_head( array( 'title' => has_text( $a['title'] ?? '' ) ? $a['title'] : $entry['title'], 'text' => $a['text'] ?? '', 'id' => 'press-title', 'size' => 'display-m' ) ); ?>
		<?php if ( ! $items ) : ?>
			<p class="empty-state"><?php echo e( $a['empty'] ?? '' ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( $strip ) : ?>
		<div class="press-strip" data-module="press-strip">
			<div class="container press-strip__bar">
				<?php text_el( count( $strip ) > 1 ? ( $a['others_label'] ?? '' ) : '', 'p', 'eyebrow press-strip__label' ); ?>
				<?php if ( count( $strip ) > 1 ) : ?>
					<div class="press-strip__nav">
						<?php if ( $archive && has_text( ui( 'a11y', 'view_all_publications' ) ) ) : ?>
							<a class="link-arrow" href="#arquivo"><?php echo e( ui( 'a11y', 'view_all_publications' ) ); ?></a>
						<?php endif; ?>
						<button class="icon-btn" type="button" data-strip-prev aria-label="<?php echo e( ui( 'a11y', 'previous' ) ); ?>"><?php echo icon( 'arrow-left' ); // phpcs:ignore ?></button>
						<button class="icon-btn" type="button" data-strip-next aria-label="<?php echo e( ui( 'a11y', 'next' ) ); ?>"><?php echo icon( 'arrow' ); // phpcs:ignore ?></button>
					</div>
				<?php endif; ?>
			</div>
			<ul class="press-strip__track" data-strip-track>
				<?php foreach ( $strip as $i => $p ) : ?>
					<?php $d = g( $p, 'details' ); ?>
					<li class="press-item<?php echo 0 === $i ? ' is-active' : ''; ?>" data-strip-item>
						<article class="press-item__inner" aria-labelledby="press-h-<?php echo (int) $p['id']; ?>">
							<?php if ( media_ok( $d['cover'] ?? 0 ) ) : ?>
								<span class="press-item__cover media-frame"><?php echo media_html( $d['cover'], array( 'size' => 'card', 'sizes' => '(min-width: 64rem) 40vw, 85vw', 'decorative' => true ) ); // phpcs:ignore ?></span>
							<?php endif; ?>
							<div class="press-item__body">
								<p class="press-item__meta small">
									<?php if ( media_rights_ok( $d['outlet_logo'] ?? 0 ) ) : ?>
										<span class="press-item__logo"><?php echo media_html( $d['outlet_logo'], array( 'alt' => (string) ( $d['outlet'] ?? '' ) ) ); // phpcs:ignore ?></span>
									<?php else : ?>
										<span class="press-item__outlet"><?php echo e( $d['outlet'] ?? '' ); ?></span>
									<?php endif; ?>
									<?php if ( has_text( $d['date'] ?? '' ) ) : ?><time datetime="<?php echo e( $d['date'] ); ?>"><?php echo e( format_date( $d['date'] ) ); ?></time><?php endif; ?>
									<?php if ( ! empty( $d['kind'] ) ) : ?><span><?php echo e( $kinds[ $d['kind'] ] ?? '' ); ?></span><?php endif; ?>
								</p>
								<h3 class="press-item__title" id="press-h-<?php echo (int) $p['id']; ?>"><span data-headline><?php echo e( $p['title'] ); ?></span></h3>
								<div class="press-item__more">
									<?php text_el( $d['summary'] ?? '', 'p', 'press-item__summary muted' ); ?>
									<?php if ( has_text( $a['read_original'] ?? '' ) ) : ?>
										<a class="btn btn--secondary press-item__link" href="<?php echo e( $d['url'] ); ?>" target="_blank" rel="noopener noreferrer"><span><?php echo e( $a['read_original'] ); ?></span><span class="screen-reader-text"> — <?php echo e( $p['title'] ); ?> <?php echo e( $ext ); ?></span><?php echo icon( 'external', 'btn__arrow' ); // phpcs:ignore ?></a>
									<?php endif; ?>
								</div>
							</div>
						</article>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</section>

<?php if ( $archive ) : ?>
	<?php
	$present = array_values( array_unique( array_filter( array_map( fn( $p ) => (string) ( g( $p, 'details' )['kind'] ?? '' ), $items ) ) ) );
	$current = query( 'tipo' );
	$current = in_array( $current, $present, true ) ? $current : '';
	$filtered = array_values( array_filter( $items, fn( $p ) => ! $current || ( g( $p, 'details' )['kind'] ?? '' ) === $current ) );
	$size     = 10;
	$shown    = max( $size, (int) query( 'mostrar' ) ?: $size );
	$base     = url_for( $entry );
	$href     = static fn( string $k, int $m = 0 ): string => $base . ( $k || $m ? '?' . http_build_query( array_filter( array( 'tipo' => $k, 'mostrar' => $m ?: null ) ) ) : '' ) . '#arquivo';
	?>
	<section class="section press-archive" id="arquivo" data-theme="light" aria-labelledby="archive-title">
		<div class="container" data-module="filter-grid" data-param="tipo" data-page-size="<?php echo (int) $size; ?>">
			<h2 id="archive-title" class="display-s press-archive__title"><?php echo e( has_text( $a['archive_title'] ?? '' ) ? $a['archive_title'] : $entry['title'] ); ?></h2>
			<?php if ( count( $present ) > 1 && has_text( $a['filter_all'] ?? '' ) ) : ?>
				<nav class="chips press-archive__filters" aria-label="<?php echo e( ui( 'content', 'filter_by' ) ); ?>">
					<a class="chip" href="<?php echo e( $href( '' ) ); ?>" data-filter=""<?php echo '' === $current ? ' aria-current="true"' : ''; ?>><?php echo e( $a['filter_all'] ); ?></a>
					<?php foreach ( array_keys( $kinds ) as $k ) : ?>
						<?php if ( in_array( $k, $present, true ) && has_text( $kind_lbl( $k ) ) ) : ?>
							<a class="chip" href="<?php echo e( $href( $k ) ); ?>" data-filter="<?php echo e( $k ); ?>"<?php echo $current === $k ? ' aria-current="true"' : ''; ?>><?php echo e( $kind_lbl( $k ) ); ?></a>
						<?php endif; ?>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
			<p class="screen-reader-text" aria-live="polite" data-results-count data-template="<?php echo e( ui( 'content', 'results_count' ) ); ?>"></p>
			<ul class="press-list">
				<?php foreach ( $items as $p ) : ?>
					<?php
					$d   = g( $p, 'details' );
					$pos = array_search( $p, $filtered, true );
					?>
					<li class="press-list__item" data-filter-item data-key="<?php echo e( (string) ( $d['kind'] ?? '' ) ); ?>"<?php echo false === $pos || $pos >= $shown ? ' hidden' : ''; ?>>
						<a class="press-list__link" href="<?php echo e( $d['url'] ); ?>" target="_blank" rel="noopener noreferrer">
							<span class="press-list__meta small subtle">
								<?php if ( has_text( $d['date'] ?? '' ) ) : ?><time datetime="<?php echo e( $d['date'] ); ?>"><?php echo e( format_date( $d['date'] ) ); ?></time><?php endif; ?>
								<span><?php echo e( $kinds[ $d['kind'] ?? '' ] ?? '' ); ?></span>
							</span>
							<span class="press-list__outlet"><?php echo e( $d['outlet'] ?? '' ); ?></span>
							<span class="press-list__title"><?php echo e( $p['title'] ); ?></span>
							<span class="press-list__cta small"><?php echo e( $a['access_label'] ?? '' ); ?><span class="screen-reader-text"> <?php echo e( $ext ); ?></span> <?php echo icon( 'external', 'btn__arrow' ); // phpcs:ignore ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="empty-state" data-empty<?php echo $filtered ? ' hidden' : ''; ?>><?php echo e( $a['empty'] ?? '' ); ?></p>
			<?php if ( has_text( $a['load_more'] ?? '' ) ) : ?>
				<div class="cases-list__more"<?php echo count( $filtered ) > $shown ? '' : ' hidden'; ?> data-more-wrap>
					<a class="btn btn--secondary" href="<?php echo e( $href( $current, $shown + $size ) ); ?>" data-load-more><span><?php echo e( $a['load_more'] ); ?></span><?php echo icon( 'plus', 'btn__arrow' ); // phpcs:ignore ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>
<?php
cta_band( g( $entry, 'cta' ), 'press-cta' );
