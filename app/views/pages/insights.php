<?php
/**
 * Insights — hero; destaque editorial (artigo grande + manchetes como abas,
 * destaques primeiro); grid com filtro por categoria na URL
 * (?categoria=, também sem JS), "Carregar mais" e estado vazio; CTA.
 */
defined( 'FCK_ROOT' ) || exit;
$list     = g( $entry, 'listing' );
$size     = max( 3, (int) ( $list['page_size'] ?? 9 ) );
$articles = articles_sorted();

view( 'parts/page-hero', array( 'args' => array( 'data' => g( $entry, 'hero' ) ) ) );

if ( ! $articles ) :
	?>
	<section class="section" data-theme="dark">
		<div class="container"><p class="empty-state"><?php echo e( $list['empty'] ?? '' ); ?></p></div>
	</section>
	<?php
	cta_band( g( $entry, 'cta' ), 'insights-cta' );
	return;
endif;

// Destaque: marcados como destaque primeiro, depois os mais recentes (até 4).
$feature = $articles;
usort( $feature, fn( $a, $b ) => (int) ! empty( g( $b, 'details' )['featured'] ) <=> (int) ! empty( g( $a, 'details' )['featured'] ) );
$feature  = array_slice( $feature, 0, min( 4, count( $articles ) ) );
$in_feat  = array_map( fn( $a ) => (int) $a['id'], $feature );
$rest     = array_values( array_filter( $articles, fn( $a ) => ! in_array( (int) $a['id'], $in_feat, true ) ) );

// Categorias usadas no grid.
$cat_slug = array();
foreach ( $rest as $a ) {
	$cid = (int) ( g( $a, 'details' )['category'] ?? 0 );
	if ( $cid && ! isset( $cat_slug[ $cid ] ) && ( $ce = entry_find( $cid ) ) ) {
		$cat_slug[ $cid ] = $ce;
	}
}
$current = query( 'categoria' );
if ( $current && ! in_array( $current, array_column( $cat_slug, 'slug' ), true ) ) {
	$current = '';
}
$key_of   = static fn( array $a ): string => $cat_slug[ (int) ( g( $a, 'details' )['category'] ?? 0 ) ]['slug'] ?? '';
$filtered = array_values( array_filter( $rest, fn( $a ) => ! $current || $key_of( $a ) === $current ) );
$shown    = max( $size, (int) query( 'mostrar' ) ?: $size );
$base     = url_for( $entry );
$href     = static fn( string $cat, int $mostrar = 0 ): string => $base . ( $cat || $mostrar ? '?' . http_build_query( array_filter( array( 'categoria' => $cat, 'mostrar' => $mostrar ?: null ) ) ) . '#insights-grid' : '#insights-grid' );
?>
<section class="section insights-feature" data-theme="light" aria-labelledby="insights-feature-title">
	<div class="container">
		<h2 id="insights-feature-title" class="<?php echo has_text( $list['featured_label'] ?? '' ) ? 'eyebrow insights-feature__label' : 'screen-reader-text'; ?>"><?php echo e( has_text( $list['featured_label'] ?? '' ) ? $list['featured_label'] : $entry['title'] ); ?></h2>
		<?php view( 'parts/insights-feature', array( 'args' => array( 'articles' => $feature, 'id' => 'insights-feature', 'read_label' => $list['read_label'] ?? '', 'label' => $list['others_label'] ?? '' ) ) ); ?>
	</div>
</section>

<?php if ( $rest ) : ?>
	<section class="section insights-grid" id="insights-grid" data-theme="dark" aria-labelledby="insights-grid-title">
		<div class="container" data-module="filter-grid" data-param="categoria" data-page-size="<?php echo (int) $size; ?>">
			<?php section_head( array( 'title' => has_text( $list['grid_title'] ?? '' ) ? $list['grid_title'] : $entry['title'], 'text' => $list['grid_text'] ?? '', 'id' => 'insights-grid-title', 'size' => 'display-m' ) ); ?>
			<?php if ( count( $cat_slug ) > 1 && has_text( $list['filter_all'] ?? '' ) ) : ?>
				<nav class="chips insights-grid__filters" aria-label="<?php echo e( ui( 'content', 'filter_by' ) ); ?>">
					<a class="chip" href="<?php echo e( $href( '' ) ); ?>" data-filter=""<?php echo '' === $current ? ' aria-current="true"' : ''; ?>><?php echo e( $list['filter_all'] ); ?></a>
					<?php foreach ( $cat_slug as $c ) : ?>
						<a class="chip" href="<?php echo e( $href( $c['slug'] ) ); ?>" data-filter="<?php echo e( $c['slug'] ); ?>"<?php echo $current === $c['slug'] ? ' aria-current="true"' : ''; ?>><?php echo e( $c['title'] ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
			<p class="screen-reader-text" aria-live="polite" data-results-count data-template="<?php echo e( ui( 'content', 'results_count' ) ); ?>"></p>
			<ul class="post-grid">
				<?php foreach ( $rest as $a ) : ?>
					<?php $pos = array_search( $a, $filtered, true ); ?>
					<li class="post-grid__item" data-filter-item data-key="<?php echo e( $key_of( $a ) ); ?>"<?php echo false === $pos || $pos >= $shown ? ' hidden' : ''; ?>>
						<?php view( 'insights/card', array( 'args' => array( 'article' => $a, 'cta' => $list['continue_label'] ?? '' ) ) ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="empty-state" data-empty<?php echo $filtered ? ' hidden' : ''; ?>><?php echo e( $list['empty'] ?? '' ); ?></p>
			<?php if ( has_text( $list['load_more'] ?? '' ) ) : ?>
				<div class="cases-list__more"<?php echo count( $filtered ) > $shown ? '' : ' hidden'; ?> data-more-wrap>
					<a class="btn btn--secondary" href="<?php echo e( $href( $current, $shown + $size ) ); ?>" data-load-more><span><?php echo e( $list['load_more'] ); ?></span><?php echo icon( 'plus', 'btn__arrow' ); // phpcs:ignore ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>
<?php
cta_band( g( $entry, 'cta' ), 'insights-cta' );
