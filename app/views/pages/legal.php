<?php
/** Texto jurídico (privacidade/cookies). Publicar só após aprovação. */
defined( 'FCK_ROOT' ) || exit;
$legal = g( $entry, 'legal' );
?>
<article class="section legal" data-theme="light" aria-labelledby="page-title">
	<div class="container legal__inner">
		<h1 id="page-title" class="display-m"><?php echo e( has_text( $legal['title'] ?? '' ) ? $legal['title'] : $entry['title'] ); ?></h1>
		<?php if ( has_text( $legal['updated_on'] ?? '' ) ) : ?>
			<p class="small subtle"><?php echo e( trim( ( $legal['updated_label'] ?? '' ) . ' ' . $legal['updated_on'] ) ); ?></p>
		<?php endif; ?>
		<div class="prose"><?php echo html_sanitize( (string) ( $legal['body'] ?? '' ) ); // phpcs:ignore -- sanitizado. ?></div>
	</div>
</article>
