<?php
/** Chamada final cinematográfica (título, texto, botão) com fundo de conexões. */
defined( 'FCK_ROOT' ) || exit;
$cta = $args['cta'];
$id  = $args['id'] ?? 'cta';
?>
<section class="cta-band" data-theme="hero" data-module="connections" data-density="0.7" aria-labelledby="<?php echo e( $id ); ?>-title">
	<div class="container cta-band__inner">
		<h2 id="<?php echo e( $id ); ?>-title" class="display-l cta-band__title reveal"><?php echo e( $cta['title'] ); ?></h2>
		<div class="cta-band__bottom reveal">
			<?php text_el( $cta['text'] ?? '', 'p', 'lead' ); ?>
			<?php button( $cta['cta'] ?? null, 'primary' ); ?>
		</div>
	</div>
</section>
