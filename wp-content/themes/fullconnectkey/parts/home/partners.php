<?php
/**
 * Home 7 — Os Sócios: três retratos altos; o selecionado expande e revela a
 * bio (hover, foco ou toque). No celular, lista com abre/fecha.
 * Só aparece com os três sócios cadastrados.
 */
defined( 'ABSPATH' ) || exit;
$d        = $args['data'];
$partners = array_values( array_filter( fck_posts( 'fck_partner' ), fn( $p ) => fck_attachment_ok( fck_meta( $p->ID, 'details' )['photo'] ?? 0 ) ) );
if ( count( $partners ) < 3 ) {
	return;
}
$partners = array_slice( $partners, 0, 3 );
?>
<section class="section partners" data-theme="light" aria-labelledby="partners-title">
	<div class="container">
		<?php fck_section_head( array( 'title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'id' => 'partners-title', 'size' => 'display-l' ) ); ?>
		<div class="partners__row" data-module="partners">
			<?php foreach ( $partners as $i => $p ) : ?>
				<?php $pd = fck_meta( $p->ID, 'details' ); ?>
				<article class="partner<?php echo 0 === $i ? ' is-active' : ''; ?>" data-partner>
					<div class="media-frame partner__media"><?php echo fck_media( $pd['photo'], array( 'size' => 'fck-card', 'sizes' => '(min-width: 64rem) 50vw, 100vw' ) ); // phpcs:ignore ?></div>
					<h3 class="partner__heading">
						<button class="partner__toggle" type="button" aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>" aria-controls="partner-bio-<?php echo (int) $i; ?>">
							<span class="partner__name display-xs"><?php echo esc_html( $p->post_title ); ?></span>
							<?php fck_text_el( $pd['role'] ?? '', 'span', 'partner__role small' ); ?>
						</button>
					</h3>
					<div class="partner__bio" id="partner-bio-<?php echo (int) $i; ?>">
						<?php fck_text_el( $pd['bio'] ?? '', 'p', 'small' ); ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
