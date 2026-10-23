<?php
/**
 * Home 9 — O Time: galeria horizontal arrastável, rolável por teclado e por
 * botões. Hover/foco revela "Conecta X a Y."; no toque a frase fica visível.
 */
defined( 'FCK_ROOT' ) || exit;
$d    = $args['data'];
$team = array_values( array_filter( entries_of( 'team' ), fn( $p ) => media_ok( g( $p, 'details' )['photo'] ?? 0 ) ) );
if ( ! $team ) {
	return;
}
?>
<section class="section team" data-theme="dark" aria-labelledby="team-title">
	<div class="container">
		<div class="team__head">
			<?php section_head( array( 'title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'id' => 'team-title', 'size' => 'display-l' ) ); ?>
			<div class="team__buttons">
				<button class="icon-btn" type="button" data-scroll-prev aria-controls="team-track" aria-label="<?php echo e( ui( 'a11y', 'previous' ) ); ?>"><?php echo icon( 'arrow-left' ); // phpcs:ignore ?></button>
				<button class="icon-btn" type="button" data-scroll-next aria-controls="team-track" aria-label="<?php echo e( ui( 'a11y', 'next' ) ); ?>"><?php echo icon( 'arrow' ); // phpcs:ignore ?></button>
			</div>
		</div>
	</div>
	<div class="team__scroller no-scrollbar" id="team-track" data-module="drag-scroll" tabindex="0" role="region" aria-label="<?php echo e( ui( 'a11y', 'scroll_hint' ) ); ?>">
		<ul class="team__list">
			<?php foreach ( $team as $p ) : ?>
				<?php $pd = g( $p, 'details' ); ?>
				<li class="team-card" tabindex="-1">
					<div class="media-frame team-card__media"><?php echo media_html( $pd['photo'], array( 'size' => 'card', 'sizes' => '(min-width: 64rem) 22vw, 70vw' ) ); // phpcs:ignore ?></div>
					<p class="team-card__name"><?php echo e( $p['title'] ); ?></p>
					<?php text_el( $pd['role'] ?? '', 'p', 'team-card__role small subtle' ); ?>
					<?php text_el( $pd['connects'] ?? '', 'p', 'team-card__connects' ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
