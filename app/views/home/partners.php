<?php
/**
 * Home 7 — Os Sócios (proposta B aprovada): três colunas iguais com nome e
 * cargo sobre a foto; o retrato selecionado (hover, foco ou toque) ganha cor
 * e revela a bio. No celular, um abaixo do outro, com a bio sempre visível.
 * Só aparece com os três sócios cadastrados.
 */
defined( 'FCK_ROOT' ) || exit;
$d        = $args['data'];
$partners = array_values( array_filter( entries_of( 'partner' ), fn( $p ) => media_ok( g( $p, 'details' )['photo'] ?? 0 ) ) );
if ( count( $partners ) < 3 ) {
	return;
}
$partners = array_slice( $partners, 0, 3 );
?>
<section class="section partners" id="socios" data-theme="light" aria-labelledby="partners-title">
	<div class="container">
		<?php section_head( array( 'title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'id' => 'partners-title', 'size' => 'display-l' ) ); ?>
		<div class="partners__row" data-module="partners">
			<?php foreach ( $partners as $i => $p ) : ?>
				<?php $pd = g( $p, 'details' ); ?>
				<article class="partner<?php echo 0 === $i ? ' is-active' : ''; ?>" data-partner>
					<div class="media-frame partner__media"><?php echo media_html( $pd['photo'], array( 'size' => 'card', 'sizes' => '(min-width: 40rem) 34vw, 100vw' ) ); // phpcs:ignore ?></div>
					<div class="partner__body">
						<h3 class="partner__heading">
							<button class="partner__toggle" type="button" aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>" aria-controls="partner-bio-<?php echo (int) $i; ?>">
								<span class="partner__name display-xs"><?php echo e( $p['title'] ); ?></span>
								<?php text_el( $pd['role'] ?? '', 'span', 'partner__role small' ); ?>
							</button>
						</h3>
						<div class="partner__bio" id="partner-bio-<?php echo (int) $i; ?>">
							<?php text_el( $pd['bio'] ?? '', 'p', 'small' ); ?>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
