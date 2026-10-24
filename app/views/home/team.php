<?php
/**
 * Home 9 — O Time: grade de retratos (proposta A, ADR-018). Os cards entram em
 * sequência; hover/foco dá cor à foto e revela "Conecta X a Y."; no toque e com
 * movimento reduzido tudo fica visível.
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
		<?php section_head( array( 'title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'id' => 'team-title', 'size' => 'display-l', 'class' => 'team__head' ) ); ?>
		<ul class="team__grid">
			<?php foreach ( $team as $i => $p ) : ?>
				<?php $pd = g( $p, 'details' ); ?>
				<li class="team-card reveal" style="--delay:<?php echo e( (string) ( 0.12 * ( $i % 4 ) ) ); ?>s">
					<div class="team-card__inner" tabindex="0">
						<div class="team-card__media"><?php echo media_html( $pd['photo'], array( 'size' => 'card', 'sizes' => '(min-width: 64rem) 22vw, 45vw' ) ); // phpcs:ignore ?></div>
						<div class="team-card__caption">
							<p class="team-card__name"><?php echo e( $p['title'] ); ?></p>
							<?php text_el( $pd['role'] ?? '', 'p', 'team-card__role small' ); ?>
							<?php if ( has_text( $pd['connects'] ?? '' ) ) : ?>
								<div class="team-card__more"><div><?php text_el( $pd['connects'], 'p', 'team-card__connects' ); ?></div></div>
							<?php endif; ?>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
