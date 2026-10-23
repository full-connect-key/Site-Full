<?php
/** Home 3 — Nossa Essência: título + introdução + três colunas com linhas que se desenham. */
defined( 'FCK_ROOT' ) || exit;
$d       = $args['data'];
$pillars = array_values( array_filter( (array) ( $d['pillars'] ?? array() ), fn( $p ) => has_text( $p['name'] ?? '' ) ) );
if ( ! has_text( $d['title'] ?? '' ) && ! $pillars ) {
	return;
}
?>
<section class="section essence" data-theme="light" aria-labelledby="essence-title">
	<div class="container">
		<?php text_el( $d['title'] ?? '', 'h2', 'display-l essence__title reveal', array( 'id' => 'essence-title' ) ); ?>
		<div class="essence__grid">
			<div class="essence__col essence__col--intro reveal">
				<span class="line draw-line" aria-hidden="true"></span>
				<?php text_el( $d['intro'] ?? '', 'p', 'lead' ); ?>
			</div>
			<?php foreach ( $pillars as $i => $p ) : ?>
				<div class="essence__col reveal" style="--delay:<?php echo e( (string) ( 0.12 * ( $i + 1 ) ) ); ?>s">
					<span class="line draw-line" style="--delay:<?php echo e( (string) ( 0.15 * ( $i + 1 ) ) ); ?>s" aria-hidden="true"></span>
					<span class="essence__node" aria-hidden="true"></span>
					<h3 class="display-s essence__name"><?php echo e( $p['name'] ); ?></h3>
					<?php text_el( $p['text'] ?? '', 'p', 'muted' ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
