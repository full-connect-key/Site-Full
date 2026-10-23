<?php
/**
 * Narrativa por rolagem (scrollytelling) usada por Full Hub e Full Leads:
 * visual fixo à esquerda, etapas à direita; a etapa central fica ativa.
 * $args: items, visual (HTML decorativo), kind.
 */
defined( 'FCK_ROOT' ) || exit;
$items = $args['items'];
$total = count( $items );
?>
<div class="story story--<?php echo e( $args['kind'] ); ?>" data-module="story" data-step="0" style="--steps:<?php echo (int) $total; ?>">
	<div class="story__visual" aria-hidden="true">
		<div class="story__visual-inner"><?php echo $args['visual']; // phpcs:ignore -- SVG/HTML decorativo montado pela view. ?></div>
	</div>
	<ol class="story__steps">
		<?php foreach ( $items as $i => $item ) : ?>
			<li class="story__step<?php echo 0 === $i ? ' is-active' : ''; ?>" data-story-step="<?php echo (int) $i; ?>">
				<p class="eyebrow story__label"><span class="story__num"><?php echo e( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span> <?php echo e( $item['label'] ); ?></p>
				<?php text_el( $item['headline'] ?? '', 'h3', 'display-s story__headline' ); ?>
				<?php text_el( $item['text'] ?? '', 'p', 'muted story__text' ); ?>
			</li>
		<?php endforeach; ?>
	</ol>
</div>
