<?php
/**
 * Rodapé: marca, assinatura, CTA, grupos de links (menu "Rodapé"), contatos,
 * links legais (menu "Links legais"), preferências de cookies e voltar ao topo.
 */

defined( 'FCK_ROOT' ) || exit;

$brand    = site( 'brand' );
$footer   = site( 'footer' );
$contacts = site( 'contacts' );
$name     = (string) ( $brand['site_name'] ?? '' );
$menu     = site( 'menu' );
$groups   = array();
foreach ( (array) ( $menu['footer_groups'] ?? array() ) as $fg ) {
	$groups[] = array( 'label' => (string) ( $fg['title'] ?? '' ), 'href' => '', 'external' => false, 'children' => menu_tree( (array) ( $fg['links'] ?? array() ) ) );
}
$legal    = menu_tree( (array) ( $menu['legal'] ?? array() ) );
$socials  = array_filter( (array) ( $contacts['socials'] ?? array() ), fn( $s ) => ! empty( $s['url'] ) && ! empty( $s['label'] ) );
$ext      = ui( 'a11y', 'external_link' );
$has_contacts = ! empty( $contacts['email'] ) || has_text( $contacts['phone'] ?? '' ) || $socials || has_text( $contacts['address'] ?? '' );
?>
</main>

<footer class="site-footer" data-theme="hero">
	<div class="container">
		<div class="grid site-footer__top">
			<div class="site-footer__brand">
				<?php echo wordmark( $name, (int) ( $brand['logo_dark'] ?? 0 ), 'display-xs' ); // phpcs:ignore ?>
				<?php text_el( $footer['tagline'] ?? '', 'p', 'site-footer__tagline font-display' ); ?>
				<?php text_el( $footer['text'] ?? '', 'p', 'site-footer__text' ); ?>
				<?php if ( link_of( $footer['cta'] ?? null ) ) : ?>
					<div style="margin-top:2rem"><?php button( $footer['cta'], 'primary' ); ?></div>
				<?php endif; ?>
			</div>

			<nav class="site-footer__nav" aria-label="<?php echo e( ui( 'a11y', 'footer_nav' ) ); ?>">
				<?php foreach ( $groups as $group ) : ?>
					<?php
					// Item de 1º nível com filhos = título do grupo; sem filhos = link solto.
					$links = $group['children'] ?: ( $group['href'] ? array( $group ) : array() );
					if ( ! $links ) {
						continue;
					}
					?>
					<div>
						<?php if ( $group['children'] ) : ?>
							<p class="eyebrow site-footer__group-title"><?php echo e( $group['label'] ); ?></p>
						<?php endif; ?>
						<ul class="site-footer__links">
							<?php foreach ( $links as $link ) : ?>
								<li><a href="<?php echo e( $link['href'] ); ?>"<?php echo $link['external'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo e( $link['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>

				<?php if ( $has_contacts ) : ?>
					<div>
						<ul class="site-footer__links">
							<?php if ( ! empty( $contacts['email'] ) ) : ?>
								<li><a href="mailto:<?php echo e( $contacts['email'] ); ?>"><?php echo e( $contacts['email'] ); ?></a></li>
							<?php endif; ?>
							<?php if ( has_text( $contacts['phone'] ?? '' ) ) : ?>
								<li><a href="tel:<?php echo e( preg_replace( '/[^\d+]/', '', $contacts['phone'] ) ); ?>"><?php echo e( $contacts['phone'] ); ?></a></li>
							<?php endif; ?>
							<?php foreach ( $socials as $s ) : ?>
								<li><a href="<?php echo e( $s['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo e( $s['label'] ); ?><?php if ( $ext ) : ?><span class="screen-reader-text"> <?php echo e( $ext ); ?></span><?php endif; ?></a></li>
							<?php endforeach; ?>
							<?php if ( has_text( $contacts['address'] ?? '' ) ) : ?>
								<li class="small subtle" style="white-space:pre-line;padding-top:.5rem"><?php echo e( $contacts['address'] ); ?></li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endif; ?>
			</nav>
		</div>

		<div class="site-footer__bottom">
			<?php if ( has_text( $footer['copyright'] ?? '' ) ) : ?>
				<p><?php echo e( fill( $footer['copyright'], array( 'ano' => date( 'Y' ) ) ) ); ?></p>
			<?php else : ?>
				<span></span>
			<?php endif; ?>
			<ul class="site-footer__legal">
				<?php foreach ( $legal as $link ) : ?>
					<?php if ( $link['href'] ) : ?>
						<li><a href="<?php echo e( $link['href'] ); ?>"><?php echo e( $link['label'] ); ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php if ( ui( 'cookies', 'open_preferences' ) ) : ?>
					<li><button type="button" data-cookie-open><?php echo e( ui( 'cookies', 'open_preferences' ) ); ?></button></li>
				<?php endif; ?>
				<?php if ( ui( 'a11y', 'back_to_top' ) ) : ?>
					<li><a href="#top"><?php echo e( ui( 'a11y', 'back_to_top' ) ); ?> <?php echo icon( 'arrow-up', 'btn__arrow' ); // phpcs:ignore ?></a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</footer>

<?php view( 'parts/cookie-banner' ); ?>
</body>
</html>
