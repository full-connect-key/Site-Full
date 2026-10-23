<?php
/**
 * Rodapé: marca, assinatura, CTA, grupos de links (menu "Rodapé"), contatos,
 * links legais (menu "Links legais"), preferências de cookies e voltar ao topo.
 */

defined( 'ABSPATH' ) || exit;

$brand    = fck_site( 'brand' );
$footer   = fck_site( 'footer' );
$contacts = fck_site( 'contacts' );
$name     = (string) ( $brand['site_name'] ?? get_bloginfo( 'name' ) );
$groups   = fck_menu_tree( 'footer' );
$legal    = fck_menu_tree( 'legal' );
$socials  = array_filter( (array) ( $contacts['socials'] ?? array() ), fn( $s ) => ! empty( $s['url'] ) && ! empty( $s['label'] ) );
$ext      = fck_ui( 'a11y', 'external_link' );
$has_contacts = ! empty( $contacts['email'] ) || fck_has_text( $contacts['phone'] ?? '' ) || $socials || fck_has_text( $contacts['address'] ?? '' );
?>
</main>

<footer class="site-footer" data-theme="hero">
	<div class="container">
		<div class="grid site-footer__top">
			<div class="site-footer__brand">
				<?php echo fck_wordmark( $name, (int) ( $brand['logo_dark'] ?? 0 ), 'display-xs' ); // phpcs:ignore ?>
				<?php fck_text_el( $footer['tagline'] ?? '', 'p', 'site-footer__tagline font-display' ); ?>
				<?php fck_text_el( $footer['text'] ?? '', 'p', 'site-footer__text' ); ?>
				<?php if ( fck_link( $footer['cta'] ?? null ) ) : ?>
					<div style="margin-top:2rem"><?php fck_button( $footer['cta'], 'primary' ); ?></div>
				<?php endif; ?>
			</div>

			<nav class="site-footer__nav" aria-label="<?php echo esc_attr( fck_ui( 'a11y', 'footer_nav' ) ); ?>">
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
							<p class="eyebrow site-footer__group-title"><?php echo esc_html( $group['label'] ); ?></p>
						<?php endif; ?>
						<ul class="site-footer__links">
							<?php foreach ( $links as $link ) : ?>
								<li><a href="<?php echo esc_url( $link['href'] ); ?>"<?php echo $link['external'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $link['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>

				<?php if ( $has_contacts ) : ?>
					<div>
						<ul class="site-footer__links">
							<?php if ( ! empty( $contacts['email'] ) ) : ?>
								<li><a href="mailto:<?php echo esc_attr( antispambot( $contacts['email'] ) ); ?>"><?php echo esc_html( antispambot( $contacts['email'] ) ); ?></a></li>
							<?php endif; ?>
							<?php if ( fck_has_text( $contacts['phone'] ?? '' ) ) : ?>
								<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $contacts['phone'] ) ); ?>"><?php echo esc_html( $contacts['phone'] ); ?></a></li>
							<?php endif; ?>
							<?php foreach ( $socials as $s ) : ?>
								<li><a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $s['label'] ); ?><?php if ( $ext ) : ?><span class="screen-reader-text"> <?php echo esc_html( $ext ); ?></span><?php endif; ?></a></li>
							<?php endforeach; ?>
							<?php if ( fck_has_text( $contacts['address'] ?? '' ) ) : ?>
								<li class="small subtle" style="white-space:pre-line;padding-top:.5rem"><?php echo esc_html( $contacts['address'] ); ?></li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endif; ?>
			</nav>
		</div>

		<div class="site-footer__bottom">
			<?php if ( fck_has_text( $footer['copyright'] ?? '' ) ) : ?>
				<p><?php echo esc_html( fck_fill( $footer['copyright'], array( 'ano' => wp_date( 'Y' ) ) ) ); ?></p>
			<?php else : ?>
				<span></span>
			<?php endif; ?>
			<ul class="site-footer__legal">
				<?php foreach ( $legal as $link ) : ?>
					<?php if ( $link['href'] ) : ?>
						<li><a href="<?php echo esc_url( $link['href'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php if ( fck_ui( 'cookies', 'open_preferences' ) ) : ?>
					<li><button type="button" data-cookie-open><?php echo esc_html( fck_ui( 'cookies', 'open_preferences' ) ); ?></button></li>
				<?php endif; ?>
				<?php if ( fck_ui( 'a11y', 'back_to_top' ) ) : ?>
					<li><a href="#top"><?php echo esc_html( fck_ui( 'a11y', 'back_to_top' ) ); ?> <?php echo fck_icon( 'arrow-up', 'btn__arrow' ); // phpcs:ignore ?></a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</footer>

<?php get_template_part( 'parts/cookie-banner' ); ?>
<?php wp_footer(); ?>
</body>
</html>
