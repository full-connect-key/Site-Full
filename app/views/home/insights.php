<?php
/**
 * Home 10 — Insights: matéria grande + manchetes laterais selecionáveis
 * (abas acessíveis) que trocam imagem e texto. Somente artigos publicados.
 */
defined( 'FCK_ROOT' ) || exit;
$d        = $args['data'];
$articles = articles_sorted();
if ( ! $articles ) {
	return;
}
// Destaques primeiro, depois os mais recentes.
usort( $articles, fn( $a, $b ) => (int) ! empty( g( $b, 'details' )['featured'] ) <=> (int) ! empty( g( $a, 'details' )['featured'] ) );
$articles = array_slice( $articles, 0, 4 );
?>
<section class="section home-insights" data-theme="light" aria-labelledby="home-insights-title">
	<div class="container">
		<div class="home-insights__head">
			<?php section_head( array( 'title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'id' => 'home-insights-title', 'size' => 'display-l' ) ); ?>
			<?php button( $d['cta'] ?? null, 'secondary' ); ?>
		</div>
		<?php view( 'parts/insights-feature', array( 'args' => array( 'articles' => $articles, 'id' => 'home-insights' ) ) ); ?>
	</div>
</section>
