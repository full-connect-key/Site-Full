<?php
/**
 * Página de plataforma — 7 blocos em ordem fixa (docs/BRIEFING_ECOSSISTEMA.md, E2):
 * 1 Hero · 2 Problema · 3 Como funciona (cena guiada pela rolagem) ·
 * 4 Benefícios · 5 Interface (só material real) · 6 Conexões · 7 CTA final.
 * Cada bloco pode ser ocultado no painel e some sozinho quando está vazio.
 * Afirmações "a validar" só aparecem fora de produção, com selo no preview.
 */
defined( 'FCK_ROOT' ) || exit;
$identity = g( $entry, 'identity' );
$product  = (string) ( $identity['product'] ?? '' );
$name     = (string) $entry['title'];
$verb     = (string) ( $identity['verb'] ?? '' );
$contact  = platform_contact_url( $entry );
$inter    = g( $entry, 'interaction' );
$badge    = static fn( array $item, string $key = 'claim_status' ): string => is_preview() && claim_pending( $item, $key ) && has_text( ui( 'platform', 'claim_badge' ) ) ? '<span class="claim-badge">' . e( ui( 'platform', 'claim_badge' ) ) . '</span>' : '';

// 3. Como funciona
$how   = g( $entry, 'how' );
$steps = array_values( array_filter( (array) ( $how['steps'] ?? array() ), fn( $s ) => has_text( $s['title'] ?? '' ) && claim_visible( $s ) ) );
$show_how = section_enabled( $how ) && $steps;

// 1. Hero
$hero             = g( $entry, 'hero' );
$hero['headline'] = has_text( $hero['headline'] ?? '' ) ? $hero['headline'] : $name;
$hero['eyebrow']  = implode( ' · ', array_filter( array( $name, $verb ), 'has_text' ) );
$actions          = '';
if ( has_text( $hero['cta_label'] ?? '' ) || ( $show_how && has_text( ui( 'platform', 'how_link' ) ) ) ) {
	ob_start();
	echo '<div class="platform-hero__actions">';
	button( array( 'href' => $contact, 'label' => (string) ( $hero['cta_label'] ?? '' ), 'external' => false ), 'primary' );
	if ( $show_how ) {
		button( array( 'href' => '#como-funciona', 'label' => ui( 'platform', 'how_link' ), 'external' => false ), 'secondary', array( 'arrow' => false ) );
	}
	echo '</div>';
	$actions = (string) ob_get_clean();
}
if ( media_ok( $hero['media'] ?? 0 ) ) {
	$actions .= '<div class="media-frame platform-hero__media">' . media_html( $hero['media'], array( 'size' => 'wide', 'sizes' => '(min-width: 64rem) 40vw, 100vw', 'loading' => 'eager' ) ) . '</div>';
}
$logo = '<div class="platform-hero__logo">' . wordmark( $name, (int) ( $identity['logo'] ?? 0 ) ) . '</div>';

// 4. Benefícios
$benefits = g( $entry, 'benefits' );
$b_items  = array_values( array_filter( (array) ( $benefits['items'] ?? array() ), fn( $b ) => has_text( $b['title'] ?? '' ) && claim_visible( $b ) ) );
$b_note   = has_text( $benefits['note'] ?? '' ) && claim_visible( $benefits, 'note_claim_status' ) ? (string) $benefits['note'] : '';

// 5. Interface: só capturas reais confirmadas.
$interface = g( $entry, 'interface' );
$i_items   = array_values( array_filter( (array) ( $interface['items'] ?? array() ), fn( $i ) => ! empty( $i['is_real'] ) && media_ok( $i['media'] ?? 0 ) ) );

// 6. Conexões
$conn    = g( $entry, 'connections' );
$c_items = array();
foreach ( (array) ( $conn['items'] ?? array() ) as $c ) {
	$target = ! empty( $c['platform'] ) ? entry_find( (int) $c['platform'] ) : null;
	if ( $target && 'platform' === $target['type'] && (int) $target['id'] !== (int) $entry['id'] && has_text( $c['text'] ?? '' ) && claim_visible( $c ) ) {
		$c_items[] = $c + array( 'entry' => $target );
	}
}

// 7. CTA final
$closing = g( $entry, 'closing' );
?>
<div class="platform" data-product="<?php echo e( $product ); ?>">
	<?php view( 'parts/page-hero', array( 'args' => array( 'data' => $hero, 'product' => $product, 'before' => $logo, 'extra' => $actions ) ) ); ?>

	<?php $problem = g( $entry, 'problem' ); ?>
	<?php if ( section_enabled( $problem ) && has_text( $problem['title'] ?? '' ) ) : ?>
		<section class="section platform-problem" data-theme="dark" data-product="<?php echo e( $product ); ?>" aria-labelledby="problem-title" data-module="progress">
			<div class="container platform-problem__grid">
				<div class="platform-problem__text">
					<h2 id="problem-title" class="display-m"><?php echo e( $problem['title'] ); ?></h2>
					<?php text_el( $problem['text'] ?? '', 'p', 'lead muted' ); ?>
				</div>
				<?php if ( in_array( $product, array( 'dash', 'mix', 'match' ), true ) ) : ?>
					<?php view( 'platform/problem-' . $product, array( 'args' => array( 'inter' => $inter ) ) ); ?>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $show_how ) : ?>
		<section class="section platform-how" id="como-funciona" data-theme="dark" data-product="<?php echo e( $product ); ?>" aria-labelledby="how-title">
			<div class="container platform-how__head">
				<h2 id="how-title" class="eyebrow platform-how__title"><?php echo e( has_text( ui( 'platform', 'how_title' ) ) ? ui( 'platform', 'how_title' ) : $name ); ?></h2>
				<?php text_el( $how['intro'] ?? '', 'p', 'display-xs platform-how__intro' ); ?>
			</div>
			<?php view( 'platform/scene', array( 'args' => array( 'entry' => $entry, 'product' => $product, 'steps' => $steps, 'how' => $how, 'inter' => $inter, 'badge' => $badge ) ) ); ?>
			<?php if ( 'mix' === $product ) : ?>
				<div class="container"><?php view( 'platform/mix-sim', array( 'args' => array( 'inter' => $inter ) ) ); ?></div>
			<?php endif; ?>
		</section>
	<?php endif; ?>

	<?php if ( section_enabled( $benefits ) && ( $b_items || has_text( $benefits['manifesto'] ?? '' ) ) ) : ?>
		<section class="section platform-benefits" data-theme="dark" data-product="<?php echo e( $product ); ?>" aria-labelledby="benefits-title">
			<div class="container">
				<h2 id="benefits-title" class="<?php echo has_text( ui( 'platform', 'benefits_title' ) ) ? 'eyebrow platform-benefits__title' : 'screen-reader-text'; ?>"><?php echo e( has_text( ui( 'platform', 'benefits_title' ) ) ? ui( 'platform', 'benefits_title' ) : $name ); ?></h2>
				<?php text_el( $benefits['manifesto'] ?? '', 'p', 'display-m platform-benefits__manifesto reveal' ); ?>
				<?php if ( $b_items ) : ?>
					<ul class="benefits benefits--<?php echo (int) count( $b_items ); ?>">
						<?php foreach ( $b_items as $i => $b ) : ?>
							<li class="benefit reveal" style="--delay:<?php echo e( (string) ( 0.07 * $i ) ); ?>s">
								<span class="benefit__viz benefit__viz--<?php echo (int) ( $i % 3 ); ?>" aria-hidden="true"><i></i><i></i><i></i></span>
								<h3 class="benefit__title"><?php echo e( $b['title'] ); ?> <?php echo $badge( $b ); // phpcs:ignore -- selo montado acima. ?></h3>
								<?php text_el( $b['text'] ?? '', 'p', 'muted benefit__text' ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( '' !== $b_note ) : ?>
					<p class="small subtle platform-benefits__note"><?php echo e( $b_note ); ?> <?php echo $badge( $benefits, 'note_claim_status' ); // phpcs:ignore ?></p>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( section_enabled( $interface ) && $i_items ) : ?>
		<section class="section platform-interface" data-theme="dark" data-product="<?php echo e( $product ); ?>" aria-labelledby="interface-title">
			<div class="container">
				<h2 id="interface-title" class="eyebrow"><?php echo e( has_text( ui( 'platform', 'interface_title' ) ) ? ui( 'platform', 'interface_title' ) : $name ); ?></h2>
				<div class="platform-interface__grid">
					<?php foreach ( $i_items as $it ) : ?>
						<figure class="platform-interface__item reveal">
							<div class="media-frame platform-interface__media"><?php echo media_html( $it['media'], array( 'size' => 'wide', 'sizes' => '(min-width: 64rem) 1080px, 100vw' ) ); // phpcs:ignore ?></div>
							<?php text_el( $it['caption'] ?? '', 'figcaption', 'small subtle' ); ?>
						</figure>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( section_enabled( $conn ) && ( $c_items || has_text( $conn['intro'] ?? '' ) ) ) : ?>
		<section class="section platform-connections" data-theme="dark" data-product="<?php echo e( $product ); ?>" aria-labelledby="connections-title">
			<div class="container platform-connections__grid">
				<div class="platform-connections__head">
					<h2 id="connections-title" class="eyebrow"><?php echo e( has_text( ui( 'platform', 'connections_title' ) ) ? ui( 'platform', 'connections_title' ) : $name ); ?></h2>
					<?php text_el( $conn['intro'] ?? '', 'p', 'display-xs' ); ?>
					<?php text_el( ui( 'platform', 'independence' ), 'p', 'small subtle platform-connections__independence' ); ?>
				</div>
				<div class="platform-connections__body">
					<svg class="conn-map reveal draw-line" viewBox="0 0 320 200" aria-hidden="true" focusable="false">
						<?php $n = max( 1, count( $c_items ) ); ?>
						<?php foreach ( $c_items as $k => $c ) : ?>
							<?php $y = round( 200 / ( $n + 1 ) * ( $k + 1 ), 1 ); ?>
							<g class="conn-map__rel" data-k="<?php echo (int) $k; ?>" data-product="<?php echo e( g( $c['entry'], 'identity' )['product'] ?? '' ); ?>">
								<path class="conn-map__line" pathLength="1" d="M70 100 C 160 100, 160 <?php echo e( (string) $y ); ?>, 250 <?php echo e( (string) $y ); ?>"/>
								<circle class="conn-map__node" cx="250" cy="<?php echo e( (string) $y ); ?>" r="12"/>
							</g>
						<?php endforeach; ?>
						<circle class="conn-map__self" cx="70" cy="100" r="22"/>
					</svg>
					<?php if ( $c_items ) : ?>
						<ul class="conn-list">
							<?php foreach ( $c_items as $k => $c ) : ?>
								<?php $cp = (string) ( g( $c['entry'], 'identity' )['product'] ?? '' ); ?>
								<li class="conn-item reveal" data-k="<?php echo (int) $k; ?>" data-product="<?php echo e( $cp ); ?>" style="--delay:<?php echo e( (string) ( 0.1 * $k ) ); ?>s">
									<p class="conn-item__name"><?php echo e( $c['entry']['title'] ); ?> <?php echo $badge( $c ); // phpcs:ignore ?></p>
									<p class="muted conn-item__text"><?php echo e( $c['text'] ); ?></p>
									<?php if ( has_text( ui( 'platform', 'connection_link' ) ) ) : ?>
										<a class="link-arrow conn-item__link" href="<?php echo e( url_for( $c['entry'] ) ); ?>"><?php echo e( str_replace( '{platform}', $c['entry']['title'], ui( 'platform', 'connection_link' ) ) ); ?> <?php echo icon( 'arrow', 'btn__arrow' ); // phpcs:ignore ?></a>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	cta_band(
		array(
			'enabled' => true,
			'title'   => (string) ( $closing['title'] ?? '' ),
			'text'    => (string) ( $closing['text'] ?? '' ),
			'cta'     => has_text( $closing['cta_label'] ?? '' ) ? array( 'href' => $contact, 'label' => (string) $closing['cta_label'], 'external' => false ) : null,
		),
		'platform-cta'
	);
	?>
</div>
