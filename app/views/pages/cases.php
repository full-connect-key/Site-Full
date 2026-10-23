<?php
/**
 * Cases — hero, grid editorial com filtros por setor (na URL: ?setor=...),
 * "Carregar mais" e estado vazio; CTA. Funciona sem JS (filtros e
 * "carregar mais" viram links); com JS, o filtro reorganiza sem recarregar.
 */
defined( 'FCK_ROOT' ) || exit;
$list    = g( $entry, 'listing' );
$size    = max( 3, (int) ( $list['page_size'] ?? 9 ) );
$cases   = entries_of( 'case' );
$sectors = entries_of( 'sector' );
$current = query( 'setor' );
$shown   = max( $size, (int) query( 'mostrar' ) ?: $size );

// Setores com pelo menos um case publicado.
$used = array();
foreach ( $cases as $c ) {
	$used[ (int) ( g( $c, 'details' )['sector'] ?? 0 ) ] = true;
}
$sectors = array_values( array_filter( $sectors, fn( $s ) => isset( $used[ (int) $s['id'] ] ) ) );
$slug_of = array();
foreach ( $sectors as $s ) {
	$slug_of[ (int) $s['id'] ] = $s['slug'];
}
if ( $current && ! in_array( $current, $slug_of, true ) ) {
	$current = '';
}
$filtered = array_values( array_filter( $cases, fn( $c ) => ! $current || ( $slug_of[ (int) ( g( $c, 'details' )['sector'] ?? 0 ) ] ?? '' ) === $current ) );
$base     = url_for( $entry );
$href     = static fn( string $setor, int $mostrar = 0 ): string => $base . ( $setor || $mostrar ? '?' . http_build_query( array_filter( array( 'setor' => $setor, 'mostrar' => $mostrar ?: null ) ) ) : '' );

/**
 * Ritmo do grid (blocos de 7): 7/5 · 4/4/4 · 5/7. Blocos incompletos no fim
 * se esticam para não deixar buracos. Espelhado em cases-filter.js.
 */
if ( ! function_exists( 'fck_case_span' ) ) {
	function fck_case_span( int $k, int $n ): string {
		$r     = $k % 7;
		$start = $k - $r;
		if ( $r < 2 || $r > 4 ) {
			$first = $r < 2 ? $start : $start + 5;
			if ( min( 2, $n - $first ) === 1 ) {
				return ' is-full';
			}
			return ( 0 === $r || 6 === $r ) ? ' is-a' : ' is-b';
		}
		$count = min( 3, $n - ( $start + 2 ) );
		return 1 === $count ? ' is-full' : ( 2 === $count ? ' is-half' : '' );
	}
}

view( 'parts/page-hero', array( 'args' => array( 'data' => g( $entry, 'hero' ) ) ) );
?>
<section class="section cases-list" data-theme="dark" aria-labelledby="cases-list-title">
	<div class="container" data-module="cases-filter" data-page-size="<?php echo (int) $size; ?>">
		<h2 id="cases-list-title" class="screen-reader-text"><?php echo e( $entry['title'] ); ?></h2>
		<?php if ( $cases && $sectors && has_text( $list['filter_all'] ?? '' ) ) : ?>
			<nav class="chips cases-list__filters" aria-label="<?php echo e( ui( 'content', 'filter_by_sector' ) ); ?>">
				<a class="chip" href="<?php echo e( $href( '' ) ); ?>" data-filter=""<?php echo '' === $current ? ' aria-current="true"' : ''; ?>><?php echo e( $list['filter_all'] ); ?></a>
				<?php foreach ( $sectors as $s ) : ?>
					<a class="chip" href="<?php echo e( $href( $s['slug'] ) ); ?>" data-filter="<?php echo e( $s['slug'] ); ?>"<?php echo $current === $s['slug'] ? ' aria-current="true"' : ''; ?>><?php echo e( $s['title'] ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<p class="screen-reader-text" aria-live="polite" data-results-count data-template="<?php echo e( ui( 'content', 'results_count' ) ); ?>"></p>

		<ul class="case-grid" data-case-grid>
			<?php foreach ( $cases as $c ) : ?>
				<?php
				$d       = g( $c, 'details' );
				$sid     = (int) ( $d['sector'] ?? 0 );
				$sector  = $slug_of[ $sid ] ?? '';
				$s_entry = $sid ? entry_find( $sid ) : null;
				$label   = has_text( $d['category'] ?? '' ) ? $d['category'] : ( $s_entry['title'] ?? '' );
				$in      = in_array( $c, $filtered, true );
				$pos     = array_search( $c, $filtered, true );
				$hidden  = ! $in || $pos >= $shown;
				?>
				<li class="case-grid__item<?php echo false === $pos || $hidden ? '' : e( fck_case_span( $pos, min( $shown, count( $filtered ) ) ) ); ?>" data-sector="<?php echo e( $sector ); ?>"<?php echo $hidden ? ' hidden' : ''; ?>>
					<a class="case-card<?php echo media_ok( $d['kv'] ?? 0 ) ? '' : ' is-plain'; ?>" href="<?php echo e( url_for( $c ) ); ?>">
						<span class="media-frame case-card__media">
							<?php if ( media_ok( $d['kv'] ?? 0 ) ) : ?>
								<?php echo media_html( $d['kv'], array( 'size' => 'card', 'sizes' => '(min-width: 64rem) 50vw, 100vw', 'decorative' => true ) ); // phpcs:ignore ?>
							<?php endif; ?>
						</span>
						<span class="case-card__info">
							<span class="case-card__title"><?php echo e( $c['title'] ); ?></span>
							<?php if ( $label ) : ?><span class="case-card__meta"><?php echo e( $label ); ?></span><?php endif; ?>
							<?php if ( has_text( $list['view_label'] ?? '' ) ) : ?><span class="case-card__cta"><?php echo e( $list['view_label'] ); ?> <?php echo icon( 'arrow', 'btn__arrow' ); // phpcs:ignore ?></span><?php endif; ?>
						</span>
					</a>
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
<?php
cta_band( g( $entry, 'cta' ), 'cases-cta' );
