<?php
/**
 * Full Dash — seletor (Gestão de mídia / Otimização contínua / Resultados de
 * negócio), cada um com gráfico ILUSTRATIVO: sem números, eixos sem valores,
 * selo "Ilustrativo" visível.
 */
defined( 'FCK_ROOT' ) || exit;
$items = $args['items'];
$note  = has_text( $args['inter']['illustrative_note'] ?? '' ) ? $args['inter']['illustrative_note'] : ui( 'a11y', 'illustrative' );
$charts = array(
	// Barras: investimento distribuído.
	static fn() => '<svg viewBox="0 0 320 200" class="dash-chart"><line x1="20" y1="180" x2="310" y2="180" class="dash-axis"/>' . implode( '', array_map( fn( $i, $h ) => sprintf( '<rect class="dash-bar" x="%d" y="%d" width="28" height="%d" style="--d:%dms"/>', 34 + $i * 46, 180 - $h, $h, $i * 80 ), range( 0, 5 ), array( 70, 110, 90, 140, 120, 160 ) ) ) . '</svg>',
	// Linha: otimização contínua (ajustes ao longo do tempo).
	static fn() => '<svg viewBox="0 0 320 200" class="dash-chart"><line x1="20" y1="180" x2="310" y2="180" class="dash-axis"/><polyline class="dash-line" points="20,150 60,140 100,148 140,120 180,112 220,96 260,84 300,60"/>' . implode( '', array_map( fn( $p ) => sprintf( '<circle class="dash-point" cx="%d" cy="%d" r="4"/>', $p[0], $p[1] ), array( array( 140, 120 ), array( 220, 96 ), array( 300, 60 ) ) ) ) . '</svg>',
	// Metas: progresso em direção ao objetivo de negócio.
	static fn() => '<svg viewBox="0 0 320 200" class="dash-chart"><circle cx="160" cy="100" r="70" class="dash-track"/><circle cx="160" cy="100" r="70" class="dash-goal" pathLength="100"/><line x1="160" y1="20" x2="160" y2="38" class="dash-axis"/></svg>',
);
?>
<div class="selector selector--dash" data-module="tabs" data-tabs-hover="false">
	<div class="selector__tabs" role="tablist" aria-labelledby="solution-title">
		<?php foreach ( $items as $i => $item ) : ?>
			<button class="selector__tab" type="button" role="tab" id="dash-tab-<?php echo (int) $i; ?>" aria-controls="dash-panel-<?php echo (int) $i; ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>"><?php echo e( $item['label'] ); ?></button>
		<?php endforeach; ?>
	</div>
	<?php foreach ( $items as $i => $item ) : ?>
		<div class="selector__panel" id="dash-panel-<?php echo (int) $i; ?>" role="tabpanel" aria-labelledby="dash-tab-<?php echo (int) $i; ?>" tabindex="0" <?php echo 0 === $i ? '' : 'hidden'; ?>>
			<div class="selector__text">
				<?php text_el( $item['headline'] ?? '', 'h3', 'display-s' ); ?>
				<?php text_el( $item['text'] ?? '', 'p', 'muted' ); ?>
			</div>
			<figure class="selector__visual">
				<?php echo ( $charts[ $i % 3 ] )(); // phpcs:ignore -- SVG fixo, decorativo. ?>
				<?php if ( '' !== $note ) : ?><figcaption class="badge-illustrative"><?php echo e( $note ); ?></figcaption><?php endif; ?>
			</figure>
		</div>
	<?php endforeach; ?>
</div>
