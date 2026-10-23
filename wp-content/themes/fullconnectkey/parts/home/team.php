<?php
/**
 * Home 9 — O Time: galeria horizontal arrastável, rolável por teclado e por
 * botões. Hover/foco revela "Conecta X a Y."; no toque a frase fica visível.
 */
defined( 'ABSPATH' ) || exit;
$d    = $args['data'];
$team = array_values( array_filter( fck_posts( 'fck_team' ), fn( $p ) => fck_attachment_ok( fck_meta( $p->ID, 'details' )['photo'] ?? 0 ) ) );
if ( ! $team ) {
	return;
}
?>
<section class="section team" data-theme="dark" aria-labelledby="team-title">
	<div class="container">
		<div class="team__head">
			<?php fck_section_head( array( 'title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'id' => 'team-title', 'size' => 'display-l' ) ); ?>
			<div class="team__buttons">
				<button class="icon-btn" type="button" data-scroll-prev aria-controls="team-track" aria-label="<?php echo esc_attr( fck_ui( 'a11y', 'previous' ) ); ?>"><?php echo fck_icon( 'arrow-left' ); // phpcs:ignore ?></button>
				<button class="icon-btn" type="button" data-scroll-next aria-controls="team-track" aria-label="<?php echo esc_attr( fck_ui( 'a11y', 'next' ) ); ?>"><?php echo fck_icon( 'arrow' ); // phpcs:ignore ?></button>
			</div>
		</div>
	</div>
	<div class="team__scroller no-scrollbar" id="team-track" data-module="drag-scroll" tabindex="0" role="region" aria-label="<?php echo esc_attr( fck_ui( 'a11y', 'scroll_hint' ) ); ?>">
		<ul class="team__list">
			<?php foreach ( $team as $p ) : ?>
				<?php $pd = fck_meta( $p->ID, 'details' ); ?>
				<li class="team-card" tabindex="-1">
					<div class="media-frame team-card__media"><?php echo fck_media( $pd['photo'], array( 'size' => 'fck-card', 'sizes' => '(min-width: 64rem) 22vw, 70vw' ) ); // phpcs:ignore ?></div>
					<p class="team-card__name"><?php echo esc_html( $p->post_title ); ?></p>
					<?php fck_text_el( $pd['role'] ?? '', 'p', 'team-card__role small subtle' ); ?>
					<?php fck_text_el( $pd['connects'] ?? '', 'p', 'team-card__connects' ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
