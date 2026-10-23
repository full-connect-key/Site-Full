<?php
/**
 * Home 10 — Insights: matéria grande + manchetes laterais selecionáveis
 * (abas acessíveis) que trocam imagem e texto. Somente artigos publicados.
 */
defined( 'ABSPATH' ) || exit;
$d        = $args['data'];
$articles = fck_posts( 'fck_article', array( 'orderby' => 'date', 'order' => 'DESC', 'posts_per_page' => 12 ) );
if ( ! $articles ) {
	return;
}
// Destaques primeiro, depois os mais recentes.
usort( $articles, fn( $a, $b ) => (int) ! empty( fck_meta( $b->ID, 'details' )['featured'] ) <=> (int) ! empty( fck_meta( $a->ID, 'details' )['featured'] ) );
$articles = array_slice( $articles, 0, 4 );
?>
<section class="section home-insights" data-theme="light" aria-labelledby="home-insights-title">
	<div class="container">
		<div class="home-insights__head">
			<?php fck_section_head( array( 'title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'id' => 'home-insights-title', 'size' => 'display-l' ) ); ?>
			<?php fck_button( $d['cta'] ?? null, 'secondary' ); ?>
		</div>
		<?php get_template_part( 'parts/insights-feature', null, array( 'articles' => $articles, 'id' => 'home-insights' ) ); ?>
	</div>
</section>
