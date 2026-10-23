<?php
/**
 * Método S&M — exatamente três seções: hero, narrativa Entender/Conectar/
 * Amplificar (rolagem com progresso visível) e CTA. Sem cases.
 */
defined( 'FCK_ROOT' ) || exit;
$method = g( $entry, 'method' );
$steps  = array_values( array_filter( (array) ( $method['steps'] ?? array() ), fn( $s ) => has_text( $s['name'] ?? '' ) ) );

view( 'parts/page-hero', array( 'args' => array( 'data' => g( $entry, 'hero' ) ) ) );

if ( $steps || has_text( $method['title'] ?? '' ) ) :
	$total = count( $steps );
	?>
	<section class="method" data-theme="dark" aria-labelledby="method-title" data-module="method" style="--steps:<?php echo (int) max( 1, $total ); ?>">
		<div class="container method__intro">
			<?php section_head( array( 'title' => $method['title'] ?? '', 'text' => $method['intro'] ?? '', 'id' => 'method-title', 'size' => 'display-l' ) ); ?>
		</div>
		<?php if ( $steps ) : ?>
			<div class="method__track">
				<div class="method__sticky">
					<div class="container method__stage">
						<div class="method__nav">
							<ol class="method__names" aria-label="<?php echo e( $method['title'] ?? '' ); ?>">
								<?php foreach ( $steps as $i => $s ) : ?>
									<li class="method__name<?php echo 0 === $i ? ' is-active' : ''; ?>" data-step-name="<?php echo (int) $i; ?>">
										<a href="#method-step-<?php echo (int) $i; ?>"><?php echo e( $s['name'] ); ?></a>
									</li>
								<?php endforeach; ?>
							</ol>
							<div class="method__progress" role="progressbar" aria-valuemin="1" aria-valuemax="<?php echo (int) $total; ?>" aria-valuenow="1" aria-valuetext="<?php echo e( fill( ui( 'a11y', 'progress' ), array( 'n' => 1, 'total' => $total ) ) ); ?>" data-progress-template="<?php echo e( ui( 'a11y', 'progress' ) ); ?>" aria-label="<?php echo e( $method['title'] ?? '' ); ?>">
								<span class="method__progress-bar" data-method-bar></span>
							</div>
						</div>
						<div class="method__visual" aria-hidden="true">
							<svg viewBox="0 0 400 400" class="method__svg">
								<circle cx="200" cy="200" r="150" class="method__ring" />
								<?php foreach ( $steps as $i => $s ) : ?>
									<?php $a = -M_PI / 2 + $i * 2 * M_PI / max( 1, $total ); ?>
									<line x1="200" y1="200" x2="<?php echo e( round( 200 + 150 * cos( $a ), 2 ) ); ?>" y2="<?php echo e( round( 200 + 150 * sin( $a ), 2 ) ); ?>" class="method__spoke" data-step-line="<?php echo (int) $i; ?>" />
									<circle cx="<?php echo e( round( 200 + 150 * cos( $a ), 2 ) ); ?>" cy="<?php echo e( round( 200 + 150 * sin( $a ), 2 ) ); ?>" r="9" class="method__node" data-step-node="<?php echo (int) $i; ?>" />
								<?php endforeach; ?>
								<circle cx="200" cy="200" r="16" class="method__core" />
							</svg>
						</div>
						<div class="method__panels">
							<?php foreach ( $steps as $i => $s ) : ?>
								<article class="method__panel<?php echo 0 === $i ? ' is-active' : ''; ?>" id="method-step-<?php echo (int) $i; ?>" data-step-panel="<?php echo (int) $i; ?>" tabindex="-1">
									<h3 class="method__step-name eyebrow"><?php echo e( $s['name'] ); ?></h3>
									<?php text_el( $s['headline'] ?? '', 'p', 'display-s method__headline' ); ?>
									<?php text_el( $s['text'] ?? '', 'p', 'muted method__text' ); ?>
								</article>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</section>
	<?php
endif;

cta_band( g( $entry, 'cta' ), 'sm-cta' );
