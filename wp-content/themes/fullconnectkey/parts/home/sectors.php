<?php
/** Home 5 — Setores: seleção acessível (aria-pressed). Sem seleção, mostra a introdução. */
defined( 'ABSPATH' ) || exit;
$d       = $args['data'];
$sectors = fck_posts( 'fck_sector' );
if ( ! $sectors ) {
	return;
}
?>
<section class="section sectors" data-theme="light" data-module="sectors" aria-labelledby="sectors-title">
	<div class="container">
		<?php fck_text_el( $d['title'] ?? '', 'h2', 'display-m sectors__title reveal', array( 'id' => 'sectors-title' ) ); ?>
		<div class="sectors__grid">
			<div class="sectors__buttons" role="group" aria-labelledby="sectors-title">
				<?php foreach ( $sectors as $s ) : ?>
					<button class="sectors__btn" type="button" aria-pressed="false" aria-controls="sector-<?php echo esc_attr( $s->post_name ); ?>" data-sector="<?php echo esc_attr( $s->post_name ); ?>">
						<span><?php echo esc_html( $s->post_title ); ?></span>
						<?php echo fck_icon( 'plus', 'sectors__icon' ); // phpcs:ignore ?>
					</button>
				<?php endforeach; ?>
			</div>
			<div class="sectors__stage" aria-live="polite">
				<div class="sectors__intro" data-sector-intro>
					<?php fck_text_el( $d['intro'] ?? '', 'p', 'lead' ); ?>
				</div>
				<?php foreach ( $sectors as $s ) : ?>
					<?php $sd = fck_meta( $s->ID, 'details' ); ?>
					<div class="sectors__panel" id="sector-<?php echo esc_attr( $s->post_name ); ?>" data-sector-panel="<?php echo esc_attr( $s->post_name ); ?>" hidden>
						<?php if ( fck_attachment_ok( $sd['image'] ?? 0 ) ) : ?>
							<div class="media-frame sectors__media"><?php echo fck_media( $sd['image'], array( 'size' => 'fck-card', 'sizes' => '(min-width: 64rem) 40vw, 100vw' ) ); // phpcs:ignore ?></div>
						<?php endif; ?>
						<?php fck_text_el( $sd['title'] ?? '', 'h3', 'display-s' ); ?>
						<?php fck_text_el( $sd['text'] ?? '', 'p', 'muted' ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
