<?php
/**
 * Artigo: categoria, título, subtítulo, autor · data · leitura estimada,
 * capa, corpo (texto rico sanitizado), compartilhar (LinkedIn, WhatsApp,
 * e-mail, copiar link), relacionados (manuais ou da mesma categoria) e CTA
 * da página Insights. JSON-LD Article sai do SEO (app/lib/seo.php).
 */
defined( 'FCK_ROOT' ) || exit;
$d      = g( $entry, 'details' );
$page   = $base ?? page_by_template( 'insights' );
$labels = $page ? g( $page, 'article' ) : array();
$list   = $page ? g( $page, 'listing' ) : array();
$cat    = ! empty( $d['category'] ) ? entry_find( (int) $d['category'] ) : null;
$author = ! empty( $d['author'] ) ? entry_find( (int) $d['author'] ) : null;
$body   = (string) ( $d['body'] ?? '' );
$min    = reading_minutes( $body );
$date   = article_date( $entry );
$link   = url_for( $entry );

// Relacionados: manuais (até 3); completa com a mesma categoria e depois os mais recentes.
$related = array();
foreach ( (array) ( $d['related'] ?? array() ) as $rid ) {
	$r = entry_find( (int) $rid );
	if ( $r && 'article' === $r['type'] && (int) $r['id'] !== (int) $entry['id'] ) {
		$related[ (int) $r['id'] ] = $r;
	}
}
if ( count( $related ) < 3 ) {
	$pool = articles_sorted();
	usort( $pool, fn( $a, $b ) => (int) ( (int) ( g( $b, 'details' )['category'] ?? 0 ) === (int) ( $d['category'] ?? -1 ) ) <=> (int) ( (int) ( g( $a, 'details' )['category'] ?? 0 ) === (int) ( $d['category'] ?? -1 ) ) );
	foreach ( $pool as $r ) {
		if ( count( $related ) >= 3 ) {
			break;
		}
		if ( (int) $r['id'] !== (int) $entry['id'] ) {
			$related[ (int) $r['id'] ] ??= $r;
		}
	}
}
$related = array_slice( array_values( $related ), 0, 3 );

$share = array_filter(
	array(
		'linkedin' => array( ui( 'content', 'share_linkedin' ), 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $link ) ),
		'whatsapp' => array( ui( 'content', 'share_whatsapp' ), 'https://wa.me/?text=' . rawurlencode( $entry['title'] . ' ' . $link ) ),
		'email'    => array( ui( 'content', 'share_email' ), 'mailto:?subject=' . rawurlencode( $entry['title'] ) . '&body=' . rawurlencode( $link ) ),
	),
	fn( $s ) => has_text( $s[0] )
);
?>
<article class="article">
	<header class="article-head" data-theme="hero" aria-labelledby="page-title">
		<div class="container article-head__inner">
			<?php if ( $cat ) : ?>
				<p class="eyebrow article-head__cat">
					<?php if ( $page ) : ?><a href="<?php echo e( url_for( $page ) . '?categoria=' . rawurlencode( $cat['slug'] ) . '#insights-grid' ); ?>"><?php echo e( $cat['title'] ); ?></a><?php else : ?><?php echo e( $cat['title'] ); ?><?php endif; ?>
				</p>
			<?php endif; ?>
			<h1 id="page-title" class="display-m article-head__title"><?php echo e( $entry['title'] ); ?></h1>
			<?php text_el( $d['subtitle'] ?? '', 'p', 'lead article-head__subtitle' ); ?>
			<p class="article-head__meta small">
				<?php if ( $author ) : ?>
					<span><?php echo e( trim( ui( 'content', 'by' ) . ' ' . $author['title'] ) ); ?><?php echo has_text( g( $author, 'details' )['role'] ?? '' ) ? ', ' . e( g( $author, 'details' )['role'] ) : ''; ?></span>
				<?php endif; ?>
				<time datetime="<?php echo e( substr( $date, 0, 10 ) ); ?>"><?php echo e( format_date( $date ) ); ?></time>
				<?php if ( has_text( ui( 'content', 'reading_time' ) ) ) : ?><span><?php echo e( str_replace( '{n}', (string) $min, ui( 'content', 'reading_time' ) ) ); ?></span><?php endif; ?>
			</p>
		</div>
	</header>

	<?php if ( media_ok( $d['cover'] ?? 0 ) ) : ?>
		<div class="article-cover" data-theme="dark">
			<div class="container">
				<div class="media-frame article-cover__media"><?php echo media_html( $d['cover'], array( 'size' => 'full', 'sizes' => '(min-width: 72rem) 1080px, 100vw', 'loading' => 'eager' ) ); // phpcs:ignore ?></div>
			</div>
		</div>
	<?php endif; ?>

	<div class="section article-body" data-theme="light">
		<div class="container article-body__grid">
			<div class="prose article-body__text">
				<?php echo $body; // phpcs:ignore -- texto rico sanitizado por allowlist ao salvar (html_sanitize). ?>
			</div>
			<?php if ( $share || has_text( ui( 'content', 'copy_link' ) ) ) : ?>
				<aside class="share" aria-labelledby="share-title" data-module="share">
					<h2 id="share-title" class="eyebrow share__title"><?php echo e( ui( 'content', 'share_article' ) ); ?></h2>
					<ul class="share__list">
						<?php foreach ( $share as $key => $s ) : ?>
							<li><a class="share__btn" href="<?php echo e( $s[1] ); ?>" data-share="<?php echo e( $key ); ?>"<?php echo 'email' === $key ? '' : ' target="_blank" rel="noopener"'; ?>><?php echo e( $s[0] ); ?><?php if ( 'email' !== $key ) : ?><span class="screen-reader-text"> <?php echo e( ui( 'a11y', 'external_link' ) ); ?></span><?php endif; ?></a></li>
						<?php endforeach; ?>
						<?php if ( has_text( ui( 'content', 'copy_link' ) ) ) : ?>
							<li><button class="share__btn" type="button" data-copy="<?php echo e( $link ); ?>" hidden><?php echo icon( 'link' ); // phpcs:ignore ?><?php echo e( ui( 'content', 'copy_link' ) ); ?></button></li>
						<?php endif; ?>
					</ul>
					<p class="share__status small" role="status" data-copied="<?php echo e( ui( 'content', 'link_copied' ) ); ?>"></p>
				</aside>
			<?php endif; ?>
		</div>
	</div>
</article>

<?php if ( $related ) : ?>
	<section class="section article-related" data-theme="dark" aria-labelledby="related-title">
		<div class="container">
			<div class="article-related__head">
				<?php section_head( array( 'title' => has_text( $labels['related_title'] ?? '' ) ? $labels['related_title'] : ( $page['title'] ?? '' ), 'text' => $labels['related_text'] ?? '', 'id' => 'related-title', 'size' => 'display-s' ) ); ?>
				<?php button( $labels['related_cta'] ?? null, 'secondary' ); ?>
			</div>
			<ul class="post-grid">
				<?php foreach ( $related as $r ) : ?>
					<li class="post-grid__item"><?php view( 'insights/card', array( 'args' => array( 'article' => $r, 'cta' => $list['continue_label'] ?? '' ) ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>
<?php
if ( $page ) {
	cta_band( g( $page, 'cta' ), 'article-cta' );
}
