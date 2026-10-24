<?php
/**
 * Teste da migração da Sprint ECO num banco descartável (*_ecotest):
 * 1) instala do zero; 2) volta plataformas, Home, Ecossistema e menus ao
 * estado v1.1 (app/seed/legacy-eco.php), com uma edição simulada no painel;
 * 3) roda o seed e confere: textos novos, slugs novos com redirect 301,
 * edição preservada e registrada, versão anterior no histórico, menus
 * renomeados; 4) roda de novo e confere que nada muda.
 * Uso: FCK_DB_NAME=fck_site_ecotest php tests/php/eco_migration_test.php
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
	exit;
}
require __DIR__ . '/../../app/bootstrap.php';
if (!str_ends_with((string) fck_config('db')['name'], '_ecotest')) {
	fwrite(STDERR, "Use um banco terminado em _ecotest (FCK_DB_NAME).\n");
	exit(2);
}

$fails = 0;
$check = function (bool $ok, string $msg) use (&$fails): void {
	echo ($ok ? '  ok   ' : '  FALHA ') . $msg . "\n";
	$fails += $ok ? 0 : 1;
};
$id_of = fn(string $key) => (int) db_value('SELECT id FROM entries WHERE seed_key = ?', [$key]);
$resolve = function (array $v) use (&$resolve, $id_of): array {
	foreach ($v as $k => $x) {
		if (is_array($x) && isset($x['to'], $x['label'])) {
			$v[$k] = str_starts_with($x['to'], 'url:')
				? ['label' => $x['label'], 'type' => 'url', 'post' => null, 'url' => substr($x['to'], 4)]
				: ['label' => $x['label'], 'type' => 'post', 'post' => $id_of($x['to']) ?: null, 'url' => ''];
		} elseif (is_array($x)) {
			$v[$k] = $resolve($x);
		}
	}
	return $v;
};

migrate();
run_seed();
$legacy = fck_seed_legacy_eco();

// Estado v1.1
foreach ($legacy['platforms'] as $key => $p) {
	$id   = $id_of('platform:' . $key);
	$cur  = json_dec(entry_raw($id)['draft']);
	$meta = $resolve($p['meta']);
	$meta['identity']['logo'] = $cur['identity']['logo'] ?? null;
	if ($key === 'mix') {
		$meta['hero']['lead'] = 'Texto editado no painel (teste).';
	}
	$json = json_enc($meta);
	db_update('entries', ['title' => $p['title'], 'slug' => $p['slug'], 'menu_order' => $p['order'], 'draft' => $json, 'published' => $json, 'published_slug' => $p['slug'], 'published_title' => $p['title'], 'status' => 'published'], ['id' => $id]);
}
foreach ($legacy['pages'] as $key => $groups) {
	$id  = $id_of('page:' . $key);
	$cur = json_dec(entry_raw($id)['draft']);
	foreach ($resolve($groups) as $g => $values) {
		$cur[$g] = array_replace((array) ($cur[$g] ?? []), $values);
	}
	$json = json_enc($cur);
	db_update('entries', ['draft' => $json, 'published' => $json, 'status' => 'published'], ['id' => $id]);
}
$site = $id_of('global:site');
$sd   = entry_raw($site)['draft'];
$old  = ['FullMix' => 'FullMix', 'FullDash' => 'Full Dash', 'FullLeads' => 'Full Leads', 'FullMatch' => 'FullMatch', 'FullHub' => 'Full Hub'];
$sd   = strtr($sd, ['"FullDash"' => '"Full Dash"', '"FullLeads"' => '"Full Leads"', '"FullHub"' => '"Full Hub"']);
db_update('entries', ['draft' => $sd, 'published' => $sd], ['id' => $site]);
db_query('DELETE FROM redirects');
setting_set('seed_migrations', []);
setting_set('eco_migration_conflicts', []);

echo "Migrando…\n";
run_seed();

$dash = entry_raw($id_of('platform:dash'));
$check($dash['title'] === 'FullDash' && $dash['slug'] === 'fulldash' && (int) $dash['menu_order'] === 2, 'FullDash renomeado, novo slug e nova ordem');
$check($dash['status'] === 'published', 'FullDash republicado');
$pub = json_dec($dash['published']);
$check(($pub['problem']['title'] ?? '') === 'Sua mídia gera números. Mas gera negócios?', 'hero v1.1 do FullDash virou o bloco Problema');
$check(($pub['how']['steps'][0]['claim_status'] ?? '') === 'validar', 'claim_status gravado nos passos');
$redir = db_all('SELECT from_path, to_path FROM redirects ORDER BY from_path');
$paths = array_column($redir, 'to_path', 'from_path');
$check(($paths['/ecossistema/full-dash'] ?? '') === '/ecossistema/fulldash' && isset($paths['/ecossistema/full-hub'], $paths['/ecossistema/full-leads']), 'redirects 301 dos slugs antigos');
$mix = json_dec(entry_raw($id_of('platform:mix'))['published']);
$check(($mix['hero']['lead'] ?? '') === 'Texto editado no painel (teste).', 'edição do painel preservada');
$check(in_array('platform:mix.hero.lead', (array) setting('eco_migration_conflicts', []), true), 'conflito registrado');
$check((int) db_value("SELECT COUNT(*) FROM revisions WHERE action = 'antes-eco'") >= 7, 'versão anterior guardada no histórico');
$home = json_dec(entry_raw($id_of('page:home'))['published']);
$check(($home['hero']['headline_alt'] ?? 'x') === '' && str_starts_with((string) ($home['ecosystem']['text'] ?? ''), 'Cinco plataformas'), 'Home: assinatura global e novo texto do Ecossistema');
$menu = (string) entry_raw($site)['published'];
$check(!str_contains($menu, '"Full Hub"') && strpos($menu, '"FullMix"') < strpos($menu, '"FullHub"'), 'menus renomeados e na ordem da jornada');

$before = (int) db_value('SELECT COUNT(*) FROM revisions');
run_seed();
$check((int) db_value('SELECT COUNT(*) FROM revisions') === $before, 'segunda execução não altera nada');

echo $fails ? "$fails falha(s).\n" : "Migração ok.\n";
exit($fails ? 1 : 0);
