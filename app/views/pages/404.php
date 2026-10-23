<?php
/** Página não encontrada (textos em Textos de interface › 404). */
defined( 'FCK_ROOT' ) || exit;
?>
<section class="page-hero error-page" data-theme="hero" data-module="connections" aria-labelledby="page-title">
	<div class="container page-hero__inner">
		<h1 id="page-title" class="display-l page-hero__title"><?php echo e( ui( 'notfound', 'title' ) ); ?></h1>
		<div class="page-hero__bottom">
			<?php text_el( ui( 'notfound', 'text' ), 'p', 'lead' ); ?>
			<?php if ( ui( 'notfound', 'cta' ) ) : ?>
				<a class="btn btn--primary" href="<?php echo e( url( '/' ) ); ?>"><span><?php echo e( ui( 'notfound', 'cta' ) ); ?></span><?php echo icon( 'arrow', 'btn__arrow' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
