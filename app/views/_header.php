<?php
/**
 * Cabeçalho: skip link, marca, menu principal com submenu, CTA e menu mobile.
 */

defined( 'FCK_ROOT' ) || exit;

$brand    = site( 'brand' );
$name     = (string) ( $brand['site_name'] ?? '' );
$menu     = site( 'menu' );
$cta      = link_of( $menu['cta'] ?? null );
$items    = menu_tree( (array) ( $menu['main'] ?? array() ) );
$skip     = ui( 'a11y', 'skip_to_content' );
?><!doctype html>
<html lang="pt-BR" data-theme="dark" class="no-js">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#05080A">
<script nonce="<?php echo e( csp_nonce() ); ?>">if('noModule' in HTMLScriptElement.prototype)document.documentElement.classList.replace('no-js','js')</script>
<link rel="icon" href="<?php echo e( asset( 'icon.svg' ) ); ?>" type="image/svg+xml">
<link rel="icon" href="<?php echo e( url( '/favicon.ico' ) ); ?>" sizes="48x48">
<link rel="apple-touch-icon" href="<?php echo e( asset( 'apple-touch-icon.png' ) ); ?>">
<?php seo_head( $seo ); ?>
<link rel="preload" href="<?php echo e( asset( 'fonts/GoogleSans-latin-variable.woff2' ) ); ?>" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?php echo e( asset( 'css/main.css' ) ); ?>">
<script type="module" src="<?php echo e( url( 'assets/js/main.js' ) . '?v=' . rawurlencode( js_version() ) ); ?>" nonce="<?php echo e( csp_nonce() ); ?>"></script>
</head>
<body class="<?php echo e( $body_class ?? '' ); ?>" id="top">
<?php if ( $skip ) : ?>
	<a class="skip-link" href="#conteudo"><?php echo e( $skip ); ?></a>
<?php endif; ?>
<span class="route-line" aria-hidden="true" data-route-line></span>

<header class="site-header" data-theme="dark" data-module="header">
	<div class="site-header__bg" aria-hidden="true"></div>
	<div class="container site-header__inner">
		<a class="site-header__brand" href="<?php echo e( url( '/' ) ); ?>" aria-label="<?php echo e( ui( 'a11y', 'home_link' ) ?: $name ); ?>">
			<?php echo wordmark( $name, (int) ( ( $brand['logo_dark'] ?? 0 ) ?: ( $brand['logo_compact_dark'] ?? 0 ) ), 'wordmark--full' ); // phpcs:ignore -- escapado. ?>
		</a>

		<?php if ( $items ) : ?>
		<nav class="main-nav" aria-label="<?php echo e( ui( 'a11y', 'main_nav' ) ); ?>">
			<ul class="main-nav__list">
				<?php foreach ( $items as $i => $item ) : ?>
					<li class="main-nav__item" <?php echo $item['children'] ? 'data-has-sub' : ''; ?>>
						<?php if ( $item['href'] ) : ?>
							<a class="main-nav__link" href="<?php echo e( $item['href'] ); ?>"<?php echo $item['current'] ? ' aria-current="page"' : ''; ?><?php echo $item['external'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo e( $item['label'] ); ?></a>
						<?php else : ?>
							<span class="main-nav__link"><?php echo e( $item['label'] ); ?></span>
						<?php endif; ?>
						<?php if ( $item['children'] ) : ?>
							<button class="main-nav__toggle" type="button" aria-expanded="false" aria-controls="submenu-<?php echo (int) $i; ?>" aria-label="<?php echo e( fill( ui( 'a11y', 'open_submenu' ), array( 'label' => $item['label'] ) ) ); ?>">
								<?php echo icon( 'chevron' ); // phpcs:ignore ?>
							</button>
							<ul class="main-nav__sub" id="submenu-<?php echo (int) $i; ?>" hidden>
								<?php foreach ( $item['children'] as $child ) : ?>
									<li>
										<a href="<?php echo e( $child['href'] ); ?>"<?php echo $child['current'] ? ' aria-current="page"' : ''; ?><?php echo $child['product'] ? ' data-product="' . e( $child['product'] ) . '"' : ''; ?>>
											<?php if ( $child['product'] ) : ?><span class="dot" aria-hidden="true"></span><?php endif; ?>
											<?php echo e( $child['label'] ); ?>
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
			<?php button( $cta, 'primary', array( 'arrow' => false, 'class' => 'btn--sm site-header__cta' ) ); ?>
			<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="<?php echo e( ui( 'a11y', 'open_menu' ) ); ?>" data-menu-toggle data-label-open="<?php echo e( ui( 'a11y', 'open_menu' ) ); ?>" data-label-close="<?php echo e( ui( 'a11y', 'close_menu' ) ); ?>">
				<span class="menu-toggle__bars" aria-hidden="true"><span></span><span></span></span>
			</button>
		</div>
	</div>

	<dialog class="mobile-menu" id="mobile-menu" data-theme="hero" aria-label="<?php echo e( ui( 'a11y', 'main_nav' ) ); ?>">
		<div class="container mobile-menu__top">
			<button class="icon-btn" type="button" data-menu-close aria-label="<?php echo e( ui( 'a11y', 'close_menu' ) ); ?>"><?php echo icon( 'close' ); // phpcs:ignore ?></button>
		</div>
		<nav class="container mobile-menu__nav" aria-label="<?php echo e( ui( 'a11y', 'main_nav' ) ); ?>">
			<ul class="mobile-menu__list">
				<?php foreach ( $items as $item ) : ?>
					<li>
						<?php if ( $item['href'] ) : ?>
							<a href="<?php echo e( $item['href'] ); ?>"<?php echo $item['current'] ? ' aria-current="page"' : ''; ?>><?php echo e( $item['label'] ); ?></a>
						<?php else : ?>
							<span class="mobile-menu__label"><?php echo e( $item['label'] ); ?></span>
						<?php endif; ?>
						<?php if ( $item['children'] ) : ?>
							<ul class="mobile-menu__sub">
								<?php foreach ( $item['children'] as $child ) : ?>
									<li><a href="<?php echo e( $child['href'] ); ?>"<?php echo $child['current'] ? ' aria-current="page"' : ''; ?>><?php echo e( $child['label'] ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php button( $cta, 'primary', array( 'class' => 'btn--block' ) ); ?>
		</nav>
	</dialog>
</header>

<main id="conteudo" tabindex="-1">
