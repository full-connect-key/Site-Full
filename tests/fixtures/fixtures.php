<?php
/**
 * FIXTURES DE TESTE — somente para o banco de teste (scripts/test-db.sh).
 * Conteúdo fictício e explicitamente marcado como teste, usado para validar
 * layouts e interações que dependem de conteúdo real. NUNCA rodar no banco
 * de revisão nem em produção.
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit;
}
if ( 'fck_wp_test' !== DB_NAME && ! getenv( 'FCK_ALLOW_FIXTURES' ) ) {
	WP_CLI::error( 'Fixtures só podem rodar no banco de teste (fck_wp_test).' );
}

function fx_find( string $type, string $key ): ?int {
	$ids = get_posts( array( 'post_type' => $type, 'post_status' => 'any', 'fields' => 'ids', 'posts_per_page' => 1, 'meta_key' => '_fck_seed_key', 'meta_value' => $key ) ); // phpcs:ignore
	return $ids ? (int) $ids[0] : null;
}

/** Imagem abstrata gerada (gradiente + rótulo "TESTE"). */
function fx_image( string $key, string $label, int $w = 1600, int $h = 1000, array $rgb = array( 30, 60, 90 ) ): int {
	if ( $id = fx_find( 'attachment', $key ) ) {
		return $id;
	}
	$im = imagecreatetruecolor( $w, $h );
	for ( $y = 0; $y < $h; $y++ ) {
		$t = $y / $h;
		$c = imagecolorallocate( $im, (int) ( $rgb[0] * ( 1 - $t ) + 5 * $t ), (int) ( $rgb[1] * ( 1 - $t ) + 8 * $t ), (int) ( $rgb[2] * ( 1 - $t ) + 10 * $t ) );
		imageline( $im, 0, $y, $w, $y, $c );
	}
	$white = imagecolorallocatealpha( $im, 255, 255, 255, 60 );
	for ( $i = 0; $i < 40; $i++ ) {
		imagefilledellipse( $im, wp_rand( 0, $w ), wp_rand( 0, $h ), 6, 6, $white );
	}
	$txt = imagecolorallocate( $im, 230, 230, 230 );
	imagestring( $im, 5, 30, $h - 40, 'TESTE - ' . strtoupper( remove_accents( $label ) ), $txt );
	$upload = wp_upload_dir();
	$file   = trailingslashit( $upload['path'] ) . sanitize_file_name( $key ) . '.jpg';
	imagejpeg( $im, $file, 82 );
	imagedestroy( $im );
	$id = (int) wp_insert_attachment( array( 'post_title' => 'Teste — ' . $label, 'post_mime_type' => 'image/jpeg', 'post_status' => 'inherit' ), $file );
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
	update_post_meta( $id, '_wp_attachment_image_alt', 'Imagem de teste: ' . $label );
	update_post_meta( $id, '_fck_rights_confirmed', true );
	update_post_meta( $id, '_fck_seed_key', $key );
	return $id;
}

function fx_post( string $type, string $key, string $title, array $meta, array $extra = array() ): int {
	if ( $id = fx_find( $type, $key ) ) {
		return $id;
	}
	$id = (int) wp_insert_post( array_merge( array( 'post_type' => $type, 'post_title' => $title, 'post_status' => 'publish' ), $extra ) );
	update_post_meta( $id, '_fck_seed_key', $key );
	foreach ( $meta as $group => $values ) {
		update_post_meta( $id, '_fck_' . $group, wp_slash( $values ) );
	}
	return $id;
}

$sectors = array();
foreach ( array( 'varejo', 'imobiliario', 'feiras-e-eventos' ) as $slug ) {
	$sectors[ $slug ] = (int) get_page_by_path( $slug, OBJECT, 'fck_sector' )->ID;
	$sd               = fck_meta( $sectors[ $slug ], 'details' );
	if ( empty( $sd['image'] ) ) {
		$sd['image'] = fx_image( 'fx-sector-' . $slug, 'Setor ' . $slug, 1200, 900, array( 20, 70, 100 ) );
		update_post_meta( $sectors[ $slug ], '_fck_details', wp_slash( $sd ) );
	}
}

// Clientes autorizados (logos genéricos de teste).
for ( $i = 1; $i <= 6; $i++ ) {
	fx_post( 'fck_client', "fx-client-$i", "Cliente Teste $i", array( 'details' => array( 'logo' => fx_image( "fx-logo-$i", "Logo $i", 400, 160, array( 60, 60, 60 ) ), 'authorized' => true, 'authorization_note' => 'Fixture de teste' ) ), array( 'menu_order' => $i ) );
}

// Métricas institucionais validadas (fictícias).
fx_post( 'fck_metric', 'fx-metric-1', 'Métrica teste 1', array( 'details' => array( 'value' => '00', 'label' => 'Indicador de teste A', 'source' => 'Fixture de teste', 'validated' => true ) ) );
fx_post( 'fck_metric', 'fx-metric-2', 'Métrica teste 2', array( 'details' => array( 'value' => '00%', 'label' => 'Indicador de teste B', 'source' => 'Fixture de teste', 'validated' => true ) ) );

// Cases.
$case_ids = array();
$keys     = array_keys( $sectors );
for ( $i = 1; $i <= 5; $i++ ) {
	$sector     = $keys[ ( $i - 1 ) % 3 ];
	$case_ids[] = fx_post(
		'fck_case',
		"fx-case-$i",
		"Case Teste $i",
		array(
			'details'   => array( 'sector' => $sectors[ $sector ], 'category' => 'Categoria teste', 'headline' => "Headline conceitual do case teste $i.", 'summary' => 'Resumo fictício usado apenas em testes automatizados.', 'kv' => fx_image( "fx-kv-$i", "KV case $i", 1800, 1100, array( 10 + 20 * $i, 60, 120 - 15 * $i ) ), 'featured' => $i <= 3 ? (string) $i : '' ),
			'narrative' => array( 'challenge' => 'Desafio fictício (teste).', 'idea' => 'Ideia fictícia (teste).', 'approach' => 'Atuação fictícia (teste).' ),
			'gallery'   => array( 'items' => array( array( 'media' => fx_image( "fx-gal-$i-1", "Galeria $i.1" ), 'caption' => 'Legenda de teste', 'size' => 'full' ), array( 'media' => fx_image( "fx-gal-$i-2", "Galeria $i.2", 1200, 1200 ), 'caption' => '', 'size' => 'half' ), array( 'media' => fx_image( "fx-gal-$i-3", "Galeria $i.3", 1200, 1200 ), 'caption' => '', 'size' => 'half' ) ) ),
			'results'   => 1 === $i ? array( 'items' => array( array( 'value' => '00', 'label' => 'Resultado de teste', 'source' => 'Fixture' ) ) ) : array( 'items' => array() ),
		),
		array( 'menu_order' => $i, 'post_name' => "case-teste-$i" )
	);
}

// Sócios, time, depoimentos.
for ( $i = 1; $i <= 3; $i++ ) {
	fx_post( 'fck_partner', "fx-partner-$i", "Pessoa Sócia Teste $i", array( 'details' => array( 'role' => 'Cargo de teste', 'bio' => 'Bio fictícia de teste para validar o layout da seção de sócios.', 'photo' => fx_image( "fx-partner-$i", "Sócio $i", 900, 1300, array( 80, 80, 90 ) ) ) ), array( 'menu_order' => $i ) );
}
for ( $i = 1; $i <= 8; $i++ ) {
	fx_post( 'fck_team', "fx-team-$i", "Pessoa Time Teste $i", array( 'details' => array( 'role' => 'Função de teste', 'photo' => fx_image( "fx-team-$i", "Time $i", 900, 1200, array( 70, 75, 85 ) ), 'connects' => 'Conecta teste a validação.' ) ), array( 'menu_order' => $i ) );
}
for ( $i = 1; $i <= 3; $i++ ) {
	fx_post( 'fck_testimonial', "fx-testimonial-$i", "Pessoa Depoente Teste $i", array( 'details' => array( 'quote' => "Depoimento fictício número $i, usado apenas para testar o carrossel.", 'role' => 'Cargo teste', 'company' => 'Empresa Teste', 'authorized' => true, 'authorization_note' => 'Fixture' ) ), array( 'menu_order' => $i ) );
}

// Autor e artigos.
$author = fx_post( 'fck_author', 'fx-author', 'Autoria Teste', array( 'details' => array( 'role' => 'Cargo teste' ) ) );
$cats   = array( 'estrategia', 'branding', 'tecnologia', 'comportamento', 'negocios' );
for ( $i = 1; $i <= 7; $i++ ) {
	$id = fx_find( 'fck_article', "fx-article-$i" );
	if ( ! $id ) {
		$body = '<!-- wp:paragraph --><p>Parágrafo fictício de teste. ' . str_repeat( 'Texto de exemplo para medir a largura de leitura confortável e o tempo estimado. ', 12 ) . '</p><!-- /wp:paragraph -->'
			. '<!-- wp:heading --><h2 class="wp-block-heading">Subtítulo de teste</h2><!-- /wp:heading -->'
			. '<!-- wp:quote --><blockquote class="wp-block-quote"><p>Citação fictícia de teste.</p></blockquote><!-- /wp:quote -->'
			. '<!-- wp:paragraph --><p>' . str_repeat( 'Mais conteúdo fictício. ', 30 ) . '</p><!-- /wp:paragraph -->';
		$id   = fx_post(
			'fck_article',
			"fx-article-$i",
			"Artigo de teste $i: um título editorial longo o suficiente",
			array( 'details' => array( 'subtitle' => 'Subtítulo fictício do artigo de teste.', 'author' => $author, 'featured' => 1 === $i ) ),
			array( 'post_content' => $body, 'post_excerpt' => 'Resumo fictício do artigo de teste.', 'post_name' => "artigo-teste-$i", 'post_date' => gmdate( 'Y-m-d H:i:s', strtotime( "-$i days" ) ) )
		);
		set_post_thumbnail( $id, fx_image( "fx-cover-$i", "Capa artigo $i", 1600, 1000, array( 40, 50 + 10 * $i, 80 ) ) );
		wp_set_object_terms( $id, $cats[ ( $i - 1 ) % 5 ], 'fck_article_cat' );
	}
}

// Matérias de imprensa (URLs de exemplo).
$kinds = array( 'interview', 'report', 'article' );
for ( $i = 1; $i <= 8; $i++ ) {
	fx_post( 'fck_press', "fx-press-$i", "Manchete de teste número $i sobre a Full", array( 'details' => array( 'outlet' => "Veículo Teste $i", 'summary' => 'Resumo próprio fictício para teste.', 'date' => gmdate( 'Y-m-d', strtotime( "-{$i} weeks" ) ), 'kind' => $kinds[ $i % 3 ], 'url' => "https://example.com/materia-teste-$i", 'cover' => fx_image( "fx-press-$i", "Matéria $i", 1200, 800, array( 50, 50, 70 ) ) ) ) );
}

// Destinatário de teste (Mailpit).
update_option( 'fck_contact_settings', array( 'recipients' => array( array( 'subject' => 'all', 'email' => 'contato-teste@localhost.test' ) ), 'retention_months' => 12 ), false );

WP_CLI::success( 'Fixtures de teste aplicadas.' );
