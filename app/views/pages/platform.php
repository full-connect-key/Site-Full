<?php
/**
 * Página de plataforma: hero, solução com interação exclusiva por produto
 * (Hub: caos → fluxo; Dash: seletor com gráficos ilustrativos; Leads: rede
 * que converge; FullMix: blocos + simulador ilustrativo; FullMatch: perfis)
 * e CTA. Cor oficial e logo vêm dos tokens e da mídia oficial.
 */
defined( 'FCK_ROOT' ) || exit;
$identity = g( $entry, 'identity' );
$hero     = g( $entry, 'hero' );
$solution = g( $entry, 'solution' );
$inter    = g( $entry, 'interaction' );
$product  = (string) ( $identity['product'] ?? '' );
$items    = array_values( array_filter( (array) ( $solution['items'] ?? array() ), fn( $i ) => has_text( $i['label'] ?? '' ) ) );
$hero['headline'] = has_text( $hero['headline'] ?? '' ) ? $hero['headline'] : $entry['title'];
$hero['eyebrow']  = $identity['signature'] ?? '';
$logo             = '<div class="platform-hero__logo">' . wordmark( $entry['title'], (int) ( $identity['logo'] ?? 0 ) ) . '</div>';
if ( media_ok( $hero['media'] ?? 0 ) ) {
	$extra = '<div class="media-frame platform-hero__media">' . media_html( $hero['media'], array( 'size' => 'wide', 'sizes' => '(min-width: 64rem) 40vw, 100vw', 'loading' => 'eager' ) ) . '</div>';
}
?>
<div class="platform" data-product="<?php echo e( $product ); ?>">
	<?php view( 'parts/page-hero', array( 'args' => array( 'data' => $hero, 'product' => $product, 'before' => $logo, 'extra' => $extra ?? '' ) ) ); ?>

	<?php if ( $items || has_text( $solution['title'] ?? '' ) ) : ?>
		<section class="section platform-solution" data-theme="dark" data-product="<?php echo e( $product ); ?>" aria-labelledby="solution-title">
			<div class="container">
				<?php section_head( array( 'title' => $solution['title'] ?? '', 'text' => $solution['intro'] ?? '', 'id' => 'solution-title', 'size' => 'display-l' ) ); ?>
				<?php
				$view = array( 'hub' => 'hub', 'leads' => 'leads', 'dash' => 'dash', 'mix' => 'mix', 'match' => 'match' )[ $product ] ?? 'list';
				if ( $items ) {
					view( 'platform/' . $view, array( 'args' => array( 'items' => $items, 'inter' => $inter, 'entry' => $entry ) ) );
				}
				?>
				<?php text_el( $solution['complement'] ?? '', 'p', 'lead platform-solution__complement' ); ?>
			</div>
		</section>
	<?php endif; ?>

	<?php cta_band( g( $entry, 'cta' ) + array( 'enabled' => true ), 'platform-cta' ); ?>
</div>
