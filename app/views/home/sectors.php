<?php
/** Home 5 — Setores: seleção acessível (aria-pressed). Sem seleção, mostra a introdução. */
defined( 'FCK_ROOT' ) || exit;
$d       = $args['data'];
$sectors = entries_of( 'sector' );
if ( ! $sectors ) {
	return;
}
?>
<section class="section sectors" data-theme="light" data-module="sectors" aria-labelledby="sectors-title">
	<div class="container">
		<?php text_el( $d['title'] ?? '', 'h2', 'display-m sectors__title reveal', array( 'id' => 'sectors-title' ) ); ?>
		<div class="sectors__grid">
			<div class="sectors__buttons" role="group" aria-labelledby="sectors-title">
				<?php foreach ( $sectors as $s ) : ?>
					<button class="sectors__btn" type="button" aria-pressed="false" aria-controls="sector-<?php echo e( $s['slug'] ); ?>" data-sector="<?php echo e( $s['slug'] ); ?>">
						<span><?php echo e( $s['title'] ); ?></span>
						<?php echo icon( 'plus', 'sectors__icon' ); // phpcs:ignore ?>
					</button>
				<?php endforeach; ?>
			</div>
			<div class="sectors__stage" aria-live="polite">
				<div class="sectors__intro" data-sector-intro>
					<?php text_el( $d['intro'] ?? '', 'p', 'lead' ); ?>
				</div>
				<?php foreach ( $sectors as $s ) : ?>
					<?php $sd = g( $s, 'details' ); ?>
					<div class="sectors__panel" id="sector-<?php echo e( $s['slug'] ); ?>" data-sector-panel="<?php echo e( $s['slug'] ); ?>" hidden>
						<?php if ( media_ok( $sd['image'] ?? 0 ) ) : ?>
							<div class="media-frame sectors__media"><?php echo media_html( $sd['image'], array( 'size' => 'card', 'sizes' => '(min-width: 64rem) 40vw, 100vw' ) ); // phpcs:ignore ?></div>
						<?php endif; ?>
						<?php text_el( $sd['title'] ?? '', 'h3', 'display-s' ); ?>
						<?php text_el( $sd['text'] ?? '', 'p', 'muted' ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
