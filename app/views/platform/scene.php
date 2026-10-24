<?php
/**
 * Bloco 3 "Como funciona": cena guiada pela rolagem (E2.1). O trilho tem
 * ~1 altura de tela por passo; o palco fica fixo enquanto o progresso da
 * rolagem constrói o visual (data-step 1..N, --p e --sp). O seletor acompanha
 * os passos e, clicado, leva a rolagem até o passo. "Pular animação" vai
 * direto ao fim. Com movimento reduzido ou animação desligada no painel:
 * passos empilhados, visual completo e seletor como controle manual.
 */
defined( 'FCK_ROOT' ) || exit;
$product = $args['product'];
$steps   = $args['steps'];
$how     = $args['how'];
$inter   = $args['inter'];
$badge   = $args['badge'];
$total   = count( $steps );
$lines   = static fn( string $s ): array => array_values( array_filter( array_map( 'trim', preg_split( '/\R/', $s ) ?: array() ), 'strlen' ) );
$labels  = 'dash' === $product ? $lines( (string) ( $inter['selector'] ?? '' ) ) : array();
$motion  = ! array_key_exists( 'motion', $how ) || ! empty( $how['motion'] );
$note    = has_text( $inter['illustrative_note'] ?? '' ) && 'mix' !== $product ? (string) $inter['illustrative_note'] : ui( 'a11y', 'illustrative' );
$skip    = ui( 'platform', 'skip_animation' );
?>
<?php if ( $motion && has_text( $skip ) ) : ?>
	<a class="skip-scene" href="#depois-como-funciona"><?php echo e( $skip ); ?></a>
<?php endif; ?>
<div class="scene scene--<?php echo e( $product ); ?><?php echo $motion ? ' is-motion' : ''; ?><?php echo 'soft' === ( $how['intensity'] ?? '' ) ? ' is-soft' : ''; ?>" data-module="scene" data-steps="<?php echo (int) $total; ?>" data-step="1" style="--steps:<?php echo (int) $total; ?>">
	<div class="scene__track">
		<div class="scene__sticky">
			<div class="container scene__stage">
				<figure class="scene__visual">
					<div class="scene__art" aria-hidden="true">
						<?php view( 'platform/visual-' . $product, array( 'args' => array( 'steps' => $steps, 'inter' => $inter, 'lines' => $lines ) ) ); ?>
					</div>
					<?php if ( '' !== $note ) : ?><figcaption class="badge-illustrative scene__badge"><?php echo e( $note ); ?></figcaption><?php endif; ?>
				</figure>
				<div class="scene__content">
					<div class="scene__nav">
						<?php foreach ( $steps as $i => $s ) : ?>
							<button class="selector__tab scene__tab" type="button" data-scene-go="<?php echo (int) ( $i + 1 ); ?>" aria-current="<?php echo 0 === $i ? 'step' : 'false'; ?>"><?php echo e( $labels[ $i ] ?? $s['title'] ); ?></button>
						<?php endforeach; ?>
					</div>
					<div class="scene__progress" aria-hidden="true"><span></span></div>
					<ol class="scene__steps">
						<?php foreach ( $steps as $i => $s ) : ?>
							<li class="scene__step<?php echo 0 === $i ? ' is-active' : ''; ?>" data-scene-step="<?php echo (int) ( $i + 1 ); ?>">
								<p class="eyebrow scene__num"><?php echo e( fill( ui( 'a11y', 'progress' ), array( 'n' => $i + 1, 'total' => $total ) ) ); ?></p>
								<h3 class="display-s scene__title"><?php echo e( $s['title'] ); ?> <?php echo $badge( $s ); // phpcs:ignore ?></h3>
								<?php text_el( $s['text'] ?? '', 'p', 'muted scene__text' ); ?>
								<?php if ( 'match' === $product && 1 === $i ) : ?>
									<?php $clusters = array_values( array_filter( (array) ( $inter['clusters'] ?? array() ), fn( $c ) => has_text( $c['name'] ?? '' ) ) ); ?>
									<?php if ( $clusters ) : ?>
										<ul class="match-profiles">
											<?php foreach ( $clusters as $k => $c ) : ?>
												<li><button class="match-profile" type="button" data-cluster="<?php echo (int) $k; ?>" aria-pressed="false"><strong><?php echo e( $c['name'] ); ?></strong><?php text_el( $c['text'] ?? '', 'span', 'small' ); ?></button></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			</div>
		</div>
	</div>
</div>
<span id="depois-como-funciona" class="scene-end" tabindex="-1"></span>
