<?php
/**
 * Janela de vídeo (ADR-022): links para os vídeos de apresentação das
 * plataformas abrem aqui, sem sair da página. Só é impressa se houver vídeo.
 */
defined( 'FCK_ROOT' ) || exit;
$has = false;
$eco = page_by_template( 'ecosystem' );
foreach ( array_merge( entries_of( 'platform' ), $eco ? array( $eco ) : array() ) as $p ) {
	if ( entry_video( $p ) ) {
		$has = true;
		break;
	}
}
if ( ! $has && ( ! $eco || ! entry_intro_video( $eco ) ) ) {
	return;
}
?>
<dialog class="video-modal" data-module="video-modal" data-theme="hero">
	<div class="video-modal__box">
		<button class="icon-btn video-modal__close" type="button" data-video-close aria-label="<?php echo e( ui( 'a11y', 'close_video' ) ); ?>"><?php echo icon( 'close' ); // phpcs:ignore ?></button>
		<video class="video-modal__video" controls playsinline preload="none"></video>
		<?php if ( ui( 'a11y', 'unmute_video' ) ) : ?>
			<button class="btn btn--primary btn--sm video-modal__unmute" type="button" data-video-unmute hidden><span><?php echo e( ui( 'a11y', 'unmute_video' ) ); ?></span></button>
		<?php endif; ?>
	</div>
</dialog>
