<?php
/**
 * Home 6 — Cases em destaque: 1 grande + 2 empilhados (adapta-se a 1 ou 2).
 * Indicadores só com métricas reais (resultados do case ou métricas
 * institucionais validadas); sem métricas, a faixa não existe.
 */
defined( 'ABSPATH' ) || exit;
$d     = $args['data'];
$cases = array();
foreach ( fck_posts( 'fck_case' ) as $c ) {
	$det = fck_meta( $c->ID, 'details' );
	if ( ! empty( $det['featured'] ) && fck_attachment_ok( $det['kv'] ?? 0 ) ) {
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
		fck_posts( 'fck_metric' ),
		fn( $m ) => ! empty( fck_meta( $m->ID, 'details' )['validated'] )
	)
);
$metrics_for = static function ( WP_Post $c ) use ( $institutional ): array {
	$items = array_values( array_filter( (array) ( fck_meta( $c->ID, 'results' )['items'] ?? array() ), fn( $r ) => fck_has_text( $r['value'] ?? '' ) && fck_has_text( $r['label'] ?? '' ) ) );
	if ( $items ) {
		return $items;
	}
	return array_map( fn( $m ) => fck_meta( $m->ID, 'details' ), $institutional );
};
$has_metrics = (bool) array_filter( array_map( $metrics_for, $cases ) );
?>
<section class="section home-cases" data-theme="dark" aria-labelledby="home-cases-title" <?php echo $has_metrics ? 'data-module="cases-home"' : ''; ?>>
	<div class="container">
		<div class="home-cases__head">
			<?php fck_section_head( array( 'title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'id' => 'home-cases-title', 'size' => 'display-l' ) ); ?>
			<?php fck_button( $d['cta'] ?? null, 'secondary' ); ?>
		</div>
		<ul class="home-cases__grid home-cases__grid--<?php echo count( $cases ); ?>">
			<?php foreach ( $cases as $i => $c ) : ?>
				<?php
				$det    = fck_meta( $c->ID, 'details' );
				$sector = ! empty( $det['sector'] ) && fck_is_visible( (int) $det['sector'] ) ? get_the_title( (int) $det['sector'] ) : '';
				$label  = fck_has_text( $det['category'] ?? '' ) ? $det['category'] : $sector;
				?>
				<li class="home-cases__item<?php echo 0 === $i ? ' is-large' : ''; ?>">
					<a class="case-card" href="<?php echo esc_url( get_permalink( $c ) ); ?>" data-case-index="<?php echo (int) $i; ?>">
						<span class="media-frame case-card__media">
							<?php echo fck_media( $det['kv'], array( 'size' => 0 === $i ? 'fck-wide' : 'fck-card', 'sizes' => 0 === $i ? '(min-width: 64rem) 60vw, 100vw' : '(min-width: 64rem) 40vw, 100vw', 'decorative' => true ) ); // phpcs:ignore ?>
						</span>
						<span class="case-card__info">
							<span class="case-card__title"><?php echo esc_html( $c->post_title ); ?></span>
							<?php if ( $label ) : ?><span class="case-card__meta"><?php echo esc_html( $label ); ?></span><?php endif; ?>
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
								<dt class="metrics__label"><?php echo esc_html( $m['label'] ); ?></dt>
								<dd class="metrics__value"><?php echo esc_html( $m['value'] ); ?></dd>
								<?php fck_text_el( $m['source'] ?? '', 'dd', 'metrics__source small subtle' ); ?>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
