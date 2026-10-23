<?php
/**
 * Home 6 — Cases em destaque: 1 grande + 2 empilhados (adapta-se a 1 ou 2).
 * Indicadores só com métricas reais (resultados do case ou métricas
 * institucionais validadas); sem métricas, a faixa não existe.
 */
defined( 'FCK_ROOT' ) || exit;
$d     = $args['data'];
$cases = array();
foreach ( entries_of( 'case' ) as $c ) {
	$det = g( $c, 'details' );
	if ( ! empty( $det['featured'] ) && media_ok( $det['kv'] ?? 0 ) ) {
		$cases[ (int) $det['featured'] ] ??= $c;
	}
}
ksort( $cases );
$cases = array_values( array_slice( $cases, 0, 3 ) );
if ( ! $cases ) {
	return;
}

$institutional = array_values(
	array_filter(
		entries_of( 'metric' ),
		fn( $m ) => ! empty( g( $m, 'details' )['validated'] )
	)
);
$metrics_for = static function ( array $c ) use ( $institutional ): array {
	$items = array_values( array_filter( (array) ( g( $c, 'results' )['items'] ?? array() ), fn( $r ) => has_text( $r['value'] ?? '' ) && has_text( $r['label'] ?? '' ) ) );
	if ( $items ) {
		return $items;
	}
	return array_map( fn( $m ) => g( $m, 'details' ), $institutional );
};
$has_metrics = (bool) array_filter( array_map( $metrics_for, $cases ) );
?>
<section class="section home-cases" data-theme="dark" aria-labelledby="home-cases-title" <?php echo $has_metrics ? 'data-module="cases-home"' : ''; ?>>
	<div class="container">
		<div class="home-cases__head">
			<?php section_head( array( 'title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'id' => 'home-cases-title', 'size' => 'display-l' ) ); ?>
			<?php button( $d['cta'] ?? null, 'secondary' ); ?>
		</div>
		<ul class="home-cases__grid home-cases__grid--<?php echo count( $cases ); ?>">
			<?php foreach ( $cases as $i => $c ) : ?>
				<?php
				$det    = g( $c, 'details' );
				$sector_entry = ! empty( $det['sector'] ) ? entry_find( (int) $det['sector'] ) : null;
				$sector = $sector_entry ? $sector_entry['title'] : '';
				$label  = has_text( $det['category'] ?? '' ) ? $det['category'] : $sector;
				?>
				<li class="home-cases__item<?php echo 0 === $i ? ' is-large' : ''; ?>">
					<a class="case-card" href="<?php echo e( url_for( $c ) ); ?>" data-case-index="<?php echo (int) $i; ?>">
						<span class="media-frame case-card__media">
							<?php echo media_html( $det['kv'], array( 'size' => 0 === $i ? 'wide' : 'card', 'sizes' => 0 === $i ? '(min-width: 64rem) 60vw, 100vw' : '(min-width: 64rem) 40vw, 100vw', 'decorative' => true ) ); // phpcs:ignore ?>
						</span>
						<span class="case-card__info">
							<span class="case-card__title"><?php echo e( $c['title'] ); ?></span>
							<?php if ( $label ) : ?><span class="case-card__meta"><?php echo e( $label ); ?></span><?php endif; ?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ( $has_metrics ) : ?>
			<div class="home-cases__metrics" aria-live="polite">
				<?php foreach ( $cases as $i => $c ) : ?>
					<?php $items = $metrics_for( $c ); ?>
					<dl class="metrics" data-metrics-for="<?php echo (int) $i; ?>" <?php echo 0 === $i ? '' : 'hidden'; ?>>
						<?php foreach ( array_slice( $items, 0, 4 ) as $m ) : ?>
							<div class="metrics__item">
								<dt class="metrics__label"><?php echo e( $m['label'] ); ?></dt>
								<dd class="metrics__value"><?php echo e( $m['value'] ); ?></dd>
								<?php text_el( $m['source'] ?? '', 'dd', 'metrics__source small subtle' ); ?>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
