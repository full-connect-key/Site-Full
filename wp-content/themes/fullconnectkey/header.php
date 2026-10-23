<?php
/**
 * Cabeçalho: skip link, marca, menu principal com submenu, CTA e menu mobile.
 */

defined( 'ABSPATH' ) || exit;

$brand    = fck_site( 'brand' );
$name     = (string) ( $brand['site_name'] ?? get_bloginfo( 'name' ) );
$cta      = fck_link( fck_site( 'header' )['cta'] ?? null );
$items    = fck_menu_tree( 'primary' );
$skip     = fck_ui( 'a11y', 'skip_to_content' );
?><!doctype html>
<html <?php language_attributes(); ?> data-theme="dark">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#05080A">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> id="top">
<?php wp_body_open(); ?>
<?php if ( $skip ) : ?>
	<a class="skip-link" href="#conteudo"><?php echo esc_html( $skip ); ?></a>
<?php endif; ?>
<span class="route-line" aria-hidden="true" data-route-line></span>

<header class="site-header" data-theme="dark" data-module="header">
	<div class="site-header__bg" aria-hidden="true"></div>
	<div class="container site-header__inner">
		<a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( fck_ui( 'a11y', 'home_link' ) ?: $name ); ?>">
			<?php echo fck_wordmark( $name, (int) ( ( $brand['logo_compact_dark'] ?? 0 ) ?: ( $brand['logo_dark'] ?? 0 ) ), 'wordmark--compact' ); // phpcs:ignore -- escapado. ?>
		</a>

		<?php if ( $items ) : ?>
		<nav class="main-nav" aria-label="<?php echo esc_attr( fck_ui( 'a11y', 'main_nav' ) ); ?>">
			<ul class="main-nav__list">
				<?php foreach ( $items as $i => $item ) : ?>
					<li class="main-nav__item" <?php echo $item['children'] ? 'data-has-sub' : ''; ?>>
						<?php if ( $item['href'] ) : ?>
							<a class="main-nav__link" href="<?php echo esc_url( $item['href'] ); ?>"<?php echo $item['current'] ? ' aria-current="page"' : ''; ?><?php echo $item['external'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $item['label'] ); ?></a>
						<?php else : ?>
							<span class="main-nav__link"><?php echo esc_html( $item['label'] ); ?></span>
						<?php endif; ?>
						<?php if ( $item['children'] ) : ?>
							<button class="main-nav__toggle" type="button" aria-expanded="false" aria-controls="submenu-<?php echo (int) $i; ?>" aria-label="<?php echo esc_attr( fck_fill( fck_ui( 'a11y', 'open_submenu' ), array( 'label' => $item['label'] ) ) ); ?>">
								<?php echo fck_icon( 'chevron' ); // phpcs:ignore ?>
							</button>
							<ul class="main-nav__sub" id="submenu-<?php echo (int) $i; ?>" hidden>
								<?php foreach ( $item['children'] as $child ) : ?>
									<li>
										<a href="<?php echo esc_url( $child['href'] ); ?>"<?php echo $child['current'] ? ' aria-current="page"' : ''; ?><?php echo $child['product'] ? ' data-product="' . esc_attr( $child['product'] ) . '"' : ''; ?>>
											<?php if ( $child['product'] ) : ?><span class="dot" aria-hidden="true"></span><?php endif; ?>
											<?php echo esc_html( $child['label'] ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
		<?php endif; ?>

		<div class="site-header__actions">
			<?php fck_button( $cta, 'primary', array( 'arrow' => false, 'class' => 'btn--sm site-header__cta' ) ); ?>
			<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="<?php echo esc_attr( fck_ui( 'a11y', 'open_menu' ) ); ?>" data-menu-toggle data-label-open="<?php echo esc_attr( fck_ui( 'a11y', 'open_menu' ) ); ?>" data-label-close="<?php echo esc_attr( fck_ui( 'a11y', 'close_menu' ) ); ?>">
				<span class="menu-toggle__bars" aria-hidden="true"><span></span><span></span></span>
			</button>
		</div>
	</div>

	<dialog class="mobile-menu" id="mobile-menu" data-theme="hero" aria-label="<?php echo esc_attr( fck_ui( 'a11y', 'main_nav' ) ); ?>">
		<div class="container mobile-menu__top">
			<button class="icon-btn" type="button" data-menu-close aria-label="<?php echo esc_attr( fck_ui( 'a11y', 'close_menu' ) ); ?>"><?php echo fck_icon( 'close' ); // phpcs:ignore ?></button>
		</div>
		<nav class="container mobile-menu__nav" aria-label="<?php echo esc_attr( fck_ui( 'a11y', 'main_nav' ) ); ?>">
			<ul class="mobile-menu__list">
				<?php foreach ( $items as $item ) : ?>
					<li>
						<?php if ( $item['href'] ) : ?>
							<a href="<?php echo esc_url( $item['href'] ); ?>"<?php echo $item['current'] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $item['label'] ); ?></a>
						<?php endif; ?>
						<?php if ( $item['children'] ) : ?>
							<ul class="mobile-menu__sub">
								<?php foreach ( $item['children'] as $child ) : ?>
									<li><a href="<?php echo esc_url( $child['href'] ); ?>"<?php echo $child['current'] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $child['label'] ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php fck_button( $cta, 'primary', array( 'class' => 'btn--block' ) ); ?>
		</nav>
	</dialog>
</header>

<main id="conteudo" tabindex="-1">
