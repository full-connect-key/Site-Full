<?php
/**
 * Página do case: KV full-bleed com nome, categoria, headline e resumo;
 * O desafio / A ideia / Nossa atuação; galeria mista (imagens e vídeos com
 * poster, sem autoplay); resultados só quando reais; próximo case (manual
 * ou o seguinte da listagem, voltando ao primeiro). Rótulos vêm da página
 * Cases (grupo "Página do case").
 */
defined( 'FCK_ROOT' ) || exit;
$d       = g( $entry, 'details' );
$n       = g( $entry, 'narrative' );
$page    = page_by_template( 'cases' );
$labels  = $page ? g( $page, 'case' ) : array();
$sid     = (int) ( $d['sector'] ?? 0 );
$s_entry = $sid ? entry_find( $sid ) : null;
$label   = has_text( $d['category'] ?? '' ) ? $d['category'] : ( $s_entry['title'] ?? '' );
$client  = ! empty( $d['client'] ) ? entry_find( (int) $d['client'] ) : null;
$has_kv  = media_ok( $d['kv'] ?? 0 );

$gallery = array_values( array_filter( (array) ( g( $entry, 'gallery' )['items'] ?? array() ), fn( $i ) => media_ok( $i['media'] ?? 0 ) ) );
$results = array_values( array_filter( (array) ( g( $entry, 'results' )['items'] ?? array() ), fn( $r ) => has_text( $r['value'] ?? '' ) && has_text( $r['label'] ?? '' ) ) );

// Próximo: manual (se publicado) ou o seguinte na ordem da listagem.
$next   = null;
$manual = (int) ( g( $entry, 'next' )['case'] ?? 0 );
if ( $manual && $manual !== (int) $entry['id'] ) {
	$next = entry_find( $manual );
}
if ( ! $next ) {
	$all = entries_of( 'case' );
	foreach ( $all as $k => $c ) {
		if ( (int) $c['id'] === (int) $entry['id'] ) {
			$cand = $all[ ( $k + 1 ) % count( $all ) ];
			$next = (int) $cand['id'] !== (int) $entry['id'] ? $cand : null;
			break;
		}
	}
}
$sections = array(
	'challenge' => $labels['challenge_label'] ?? '',
	'idea'      => $labels['idea_label'] ?? '',
	'approach'  => $labels['approach_label'] ?? '',
);
?>
<article class="case-page">
	<header class="case-kv<?php echo $has_kv ? '' : ' is-plain'; ?>" data-theme="hero" aria-labelledby="page-title">
		<?php if ( $has_kv ) : ?>
			<div class="case-kv__media media-frame" data-module="parallax">
				<?php echo media_html( $d['kv'], array( 'size' => 'full', 'sizes' => '100vw', 'loading' => 'eager', 'decorative' => true ) ); // phpcs:ignore ?>
			</div>
		<?php endif; ?>
		<div class="container case-kv__inner">
			<p class="eyebrow case-kv__meta">
				<?php echo e( implode( ' · ', array_filter( array( $client['title'] ?? '', $label ) ) ) ); ?>
			</p>
			<h1 id="page-title" class="display-l case-kv__title"><?php echo e( $entry['title'] ); ?></h1>
			<?php text_el( $d['headline'] ?? '', 'p', 'display-s case-kv__headline' ); ?>
			<?php text_el( $d['summary'] ?? '', 'p', 'lead case-kv__summary' ); ?>
		</div>
	</header>

	<?php if ( array_filter( array_map( fn( $k ) => has_text( $n[ $k ] ?? '' ), array_keys( $sections ) ) ) ) : ?>
		<section class="section case-story" data-theme="dark">
			<div class="container case-story__grid">
				<?php foreach ( $sections as $key => $title ) : ?>
					<?php if ( has_text( $n[ $key ] ?? '' ) ) : ?>
						<div class="case-story__block reveal">
							<?php if ( has_text( $title ) ) : ?><h2 class="eyebrow case-story__label"><?php echo e( $title ); ?></h2><?php endif; ?>
							<div class="case-story__text">
								<?php foreach ( preg_split( '/\R{2,}/', trim( $n[ $key ] ) ) as $para ) : ?>
									<p><?php echo nl2br( e( $para ) ); // phpcs:ignore ?></p>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $gallery ) : ?>
		<section class="section case-gallery" data-theme="dark" aria-labelledby="case-gallery-title">
			<div class="container">
				<h2 id="case-gallery-title" class="<?php echo has_text( $labels['gallery_title'] ?? '' ) ? 'display-xs case-gallery__title' : 'screen-reader-text'; ?>"><?php echo e( has_text( $labels['gallery_title'] ?? '' ) ? $labels['gallery_title'] : $entry['title'] ); ?></h2>
				<div class="case-gallery__grid">
					<?php foreach ( $gallery as $item ) : ?>
						<?php $size = in_array( $item['size'] ?? '', array( 'half', 'full', 'third', 'two_thirds' ), true ) ? $item['size'] : 'half'; ?>
						<figure class="case-gallery__item is-<?php echo e( $size ); ?>">
							<div class="media-frame case-gallery__media">
								<?php echo media_html( $item['media'], array( 'size' => 'full' === $size ? 'full' : 'wide', 'sizes' => 'full' === $size ? '(min-width: 72rem) 1080px, 100vw' : '(min-width: 48rem) 50vw, 100vw' ) ); // phpcs:ignore ?>
							</div>
							<?php text_el( $item['caption'] ?? '', 'figcaption', 'small subtle case-gallery__caption' ); ?>
						</figure>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $results ) : ?>
		<section class="section case-results" data-theme="dark" aria-labelledby="case-results-title">
			<div class="container">
				<h2 id="case-results-title" class="<?php echo has_text( $labels['results_title'] ?? '' ) ? 'display-xs case-results__title' : 'screen-reader-text'; ?>"><?php echo e( has_text( $labels['results_title'] ?? '' ) ? $labels['results_title'] : $entry['title'] ); ?></h2>
				<dl class="metrics case-results__list">
					<?php foreach ( $results as $m ) : ?>
						<div class="metrics__item">
							<dt class="metrics__label"><?php echo e( $m['label'] ); ?></dt>
							<dd class="metrics__value"><?php echo e( $m['value'] ); ?></dd>
							<?php text_el( $m['source'] ?? '', 'dd', 'metrics__source small subtle' ); ?>
						</div>
					<?php endforeach; ?>
				</dl>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $next ) : ?>
		<?php $nd = g( $next, 'details' ); ?>
		<nav class="case-next" data-theme="dark" aria-label="<?php echo e( $labels['next_title'] ?? $next['title'] ); ?>">
			<a class="case-next__link" href="<?php echo e( url_for( $next ) ); ?>">
				<?php if ( media_ok( $nd['kv'] ?? 0 ) ) : ?>
					<span class="case-next__media media-frame"><?php echo media_html( $nd['kv'], array( 'size' => 'wide', 'sizes' => '100vw', 'decorative' => true ) ); // phpcs:ignore ?></span>
				<?php endif; ?>
				<span class="container case-next__inner">
					<?php text_el( $labels['next_title'] ?? '', 'span', 'eyebrow case-next__eyebrow' ); ?>
					<span class="display-m case-next__title"><?php echo e( $next['title'] ); ?></span>
					<?php if ( has_text( $labels['next_label'] ?? '' ) ) : ?>
						<span class="case-next__cta"><?php echo e( $labels['next_label'] ); ?> <?php echo icon( 'arrow', 'btn__arrow' ); // phpcs:ignore ?></span>
					<?php endif; ?>
				</span>
			</a>
		</nav>
	<?php endif; ?>
</article>
<?php
if ( $page ) {
	cta_band( g( $page, 'cta' ), 'case-cta' );
}
