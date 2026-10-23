<?php
/** Lista simples de itens (produto sem interação específica). */
defined( 'FCK_ROOT' ) || exit;
?>
<ol class="mix-blocks">
	<?php foreach ( $args['items'] as $item ) : ?>
		<li class="mix-block"><p class="eyebrow"><?php echo e( $item['label'] ); ?></p><?php text_el( $item['headline'] ?? '', 'h3', 'display-xs' ); ?><?php text_el( $item['text'] ?? '', 'p', 'muted' ); ?></li>
	<?php endforeach; ?>
</ol>
