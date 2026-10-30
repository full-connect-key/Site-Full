<?php
/**
 * FIXTURES DE TESTE — somente para o banco de teste (fck_site_test).
 * Conteúdo fictício e explicitamente marcado como teste, para validar
 * layouts e interações que dependem de conteúdo real. NUNCA rodar no banco
 * de revisão nem em produção.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
	exit;
}
if (!str_ends_with((string) fck_config('db.name'), '_test')) {
	fwrite(STDERR, "Fixtures só podem rodar em um banco *_test.\n");
	exit(1);
}

function fx_image(string $key, string $label, int $w = 1600, int $h = 1000, array $rgb = [30, 60, 90]): int
{
	if ($id = db_value('SELECT id FROM media WHERE seed_key = ?', [$key])) {
		return (int) $id;
	}
	$im = imagecreatetruecolor($w, $h);
	for ($y = 0; $y < $h; $y++) {
		$t = $y / $h;
		imageline($im, 0, $y, $w, $y, imagecolorallocate($im, (int) ($rgb[0] * (1 - $t) + 5 * $t), (int) ($rgb[1] * (1 - $t) + 8 * $t), (int) ($rgb[2] * (1 - $t) + 10 * $t)));
	}
	imagestring($im, 5, 30, $h - 40, 'TESTE - ' . strtoupper($label), imagecolorallocate($im, 230, 230, 230));
	$tmp = sys_get_temp_dir() . '/' . $key . '.jpg';
	imagejpeg($im, $tmp, 82);
	imagedestroy($im);
	[$id] = media_import($tmp, $key . '.jpg', ['alt' => 'Imagem de teste: ' . $label, 'rights_confirmed' => true, 'seed_key' => $key]);
	@unlink($tmp);
	return (int) $id;
}

function fx_entry(string $type, string $key, string $title, array $data, array $attrs = []): int
{
	if ($id = db_value('SELECT id FROM entries WHERE seed_key = ?', [$key])) {
		return (int) $id;
	}
	$id = entry_create($type, $title, $attrs + ['seed_key' => $key]);
	entry_save_draft($id, ['draft' => json_enc($data)], 'seed');
	entry_publish($id);
	return $id;
}

$sectors = [];
foreach (['varejo', 'imobiliario', 'feiras-e-eventos'] as $slug) {
	$row             = db_one("SELECT * FROM entries WHERE type = 'sector' AND slug = ?", [$slug]);
	$sectors[$slug] = (int) $row['id'];
	$data            = json_dec($row['draft']);
	if (empty($data['details']['image'])) {
		$data['details']['image'] = fx_image('fx-sector-' . $slug, 'Setor ' . $slug, 1200, 900, [20, 70, 100]);
		entry_save_draft((int) $row['id'], ['draft' => json_enc($data)], 'seed');
		entry_publish((int) $row['id']);
	}
}

for ($i = 1; $i <= 6; $i++) {
	fx_entry('client', "fx-client-$i", "Cliente Teste $i", ['details' => ['logo' => fx_image("fx-logo-$i", "Logo $i", 400, 160, [60, 60, 60]), 'authorized' => true, 'authorization_note' => 'Fixture']], ['menu_order' => $i]);
}
fx_entry('metric', 'fx-metric-1', 'Métrica teste 1', ['details' => ['value' => '00', 'label' => 'Indicador de teste A', 'source' => 'Fixture', 'validated' => true]]);
fx_entry('metric', 'fx-metric-2', 'Métrica teste 2', ['details' => ['value' => '00%', 'label' => 'Indicador de teste B', 'source' => 'Fixture', 'validated' => true]]);

$keys = array_keys($sectors);
for ($i = 1; $i <= 5; $i++) {
	fx_entry('case', "fx-case-$i", "Case Teste $i", [
		'details'   => ['sector' => $sectors[$keys[($i - 1) % 3]], 'category' => 'Categoria teste', 'headline' => "Headline conceitual do case teste $i.", 'summary' => 'Resumo fictício usado apenas em testes.', 'kv' => fx_image("fx-kv-$i", "KV case $i", 1800, 1100, [10 + 20 * $i, 60, 120 - 15 * $i]), 'featured' => $i <= 3 ? (string) $i : ''],
		'narrative' => ['challenge' => 'Desafio fictício (teste).', 'idea' => 'Ideia fictícia (teste).', 'approach' => 'Atuação fictícia (teste).'],
		'gallery'   => ['items' => [['media' => fx_image("fx-gal-$i-1", "Galeria $i.1"), 'caption' => 'Legenda de teste', 'size' => 'full'], ['media' => fx_image("fx-gal-$i-2", "Galeria $i.2", 1200, 1200), 'caption' => '', 'size' => 'half'], ['media' => fx_image("fx-gal-$i-3", "Galeria $i.3", 1200, 1200), 'caption' => '', 'size' => 'half']]],
		'results'   => ['items' => $i === 1 ? [['value' => '00', 'label' => 'Resultado de teste', 'source' => 'Fixture']] : []],
	], ['menu_order' => $i, 'slug' => "case-teste-$i"]);
}
for ($i = 1; $i <= 3; $i++) {
	fx_entry('partner', "fx-partner-$i", "Pessoa Sócia Teste $i", ['details' => ['role' => 'Cargo de teste', 'bio' => 'Bio fictícia de teste para validar o layout.', 'photo' => fx_image("fx-partner-$i", "Sócio $i", 900, 1300, [80, 80, 90])]], ['menu_order' => $i]);
	fx_entry('testimonial', "fx-testimonial-$i", "Pessoa Depoente Teste $i", ['details' => ['quote' => "Depoimento fictício número $i, usado apenas para testar o carrossel.", 'role' => 'Cargo teste', 'company' => 'Empresa Teste', 'authorized' => true]], ['menu_order' => $i]);
}
for ($i = 1; $i <= 8; $i++) {
	fx_entry('team', "fx-team-$i", "Pessoa Time Teste $i", ['details' => ['role' => 'Função de teste', 'photo' => fx_image("fx-team-$i", "Time $i", 900, 1200, [70, 75, 85]), 'connects' => 'Conecta teste a validação.']], ['menu_order' => $i]);
}
$author = fx_entry('author', 'fx-author', 'Autoria Teste', ['details' => ['role' => 'Cargo teste']]);
$cats   = db_all("SELECT id FROM entries WHERE type = 'category' ORDER BY menu_order");
for ($i = 1; $i <= 7; $i++) {
	$body = '<p>Parágrafo fictício de teste. ' . str_repeat('Texto de exemplo para medir a largura de leitura. ', 12) . '</p><h2>Subtítulo de teste</h2><blockquote>Citação fictícia de teste.</blockquote><p>' . str_repeat('Mais conteúdo fictício. ', 30) . '</p>';
	fx_entry('article', "fx-article-$i", "Artigo de teste $i: um título editorial longo o suficiente", ['details' => [
		'subtitle' => 'Subtítulo fictício do artigo de teste.', 'excerpt' => 'Resumo fictício do artigo de teste.',
		'cover' => fx_image("fx-cover-$i", "Capa artigo $i", 1600, 1000, [40, 50 + 10 * $i, 80]), 'category' => (int) $cats[($i - 1) % count($cats)]['id'],
		'date' => date('Y-m-d', strtotime("-$i days")), 'body' => $body, 'author' => $author, 'featured' => $i === 1,
	]], ['slug' => "artigo-teste-$i"]);
}
$kinds = ['interview', 'report', 'article'];
for ($i = 1; $i <= 8; $i++) {
	fx_entry('press', "fx-press-$i", "Manchete de teste número $i sobre a Full", ['details' => ['outlet' => "Veículo Teste $i", 'summary' => 'Resumo próprio fictício.', 'date' => date('Y-m-d', strtotime("-$i weeks")), 'kind' => $kinds[$i % 3], 'url' => "https://example.com/materia-teste-$i", 'cover' => fx_image("fx-press-$i", "Matéria $i", 1200, 800, [50, 50, 70])]]);
}
setting_set('contact', ['recipients' => [['subject' => 'all', 'email' => 'contato-teste@localhost.test']], 'retention_months' => 12]);
echo "Fixtures de teste aplicadas.\n";

// Cases, Insights e Full na Mídia estão ocultas no site (decisão do cliente,
// 28/10/2026), mas os modelos continuam no código: no banco de teste elas são
// republicadas para manter a cobertura dos templates.
foreach (['page:cases', 'page:insights', 'page:press'] as $key) {
	if ($id = (int) db_value('SELECT id FROM entries WHERE seed_key = ? AND published IS NULL', [$key])) {
		entry_publish($id);
	}
}

// Ecossistema: no site, as plataformas abrem vídeos e as páginas estão ocultas
// (ADR-022). No banco de teste, os vídeos são retirados para manter a
// cobertura das páginas do Ecossistema.
foreach (db_all("SELECT id, draft FROM entries WHERE type = 'platform' OR seed_key = 'page:ecosystem'") as $row) {
	$data = json_dec($row['draft']);
	if (!empty($data['identity']['video']) || !empty($data['presentation']['video']) || !empty($data['presentation']['intro_video'])) {
		unset($data['identity']['video'], $data['presentation']['video'], $data['presentation']['intro_video']);
		entry_save_draft((int) $row['id'], ['draft' => json_enc($data)], 'seed');
		entry_publish((int) $row['id']);
	}
}

// Usuário editor para testes de permissão.
if (!db_value('SELECT id FROM users WHERE email = ?', ['e2e-editor@localhost.test'])) {
	create_user('e2e-editor@localhost.test', 'e2e-editor-pass-123', 'editor', 'Editor Teste');
}
