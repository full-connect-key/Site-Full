<?php
/**
 * Conteúdo (tabela entries): leitura pública, preview, URLs, links,
 * globais e gravação com rascunho / publicação / histórico.
 */

declare(strict_types=1);

/** Tipos de conteúdo do painel. */
function content_types(): array
{
	return [
		'page'        => ['Página', 'Páginas', 'public' => true, 'group' => 'Páginas', 'icon' => '▤'],
		'platform'    => ['Plataforma', 'Plataformas', 'public' => true, 'group' => 'Ecossistema', 'icon' => '◎'],
		'case'        => ['Case', 'Cases', 'public' => true, 'group' => 'Cases', 'icon' => '▣'],
		'client'      => ['Cliente', 'Clientes', 'public' => false, 'group' => 'Cases', 'icon' => '◇'],
		'metric'      => ['Métrica', 'Métricas institucionais', 'public' => false, 'group' => 'Cases', 'icon' => '▥'],
		'article'     => ['Artigo', 'Artigos', 'public' => true, 'group' => 'Insights', 'icon' => '✎'],
		'category'    => ['Categoria', 'Categorias', 'public' => false, 'group' => 'Insights', 'icon' => '#'],
		'author'      => ['Autor', 'Autores', 'public' => false, 'group' => 'Insights', 'icon' => '◔'],
		'press'       => ['Matéria', 'Full na Mídia', 'public' => false, 'group' => 'Imprensa', 'icon' => '❝'],
		'sector'      => ['Setor', 'Setores', 'public' => false, 'group' => 'Home', 'icon' => '◫'],
		'partner'     => ['Sócio', 'Sócios', 'public' => false, 'group' => 'Pessoas', 'icon' => '◉'],
		'team'        => ['Pessoa do time', 'Time', 'public' => false, 'group' => 'Pessoas', 'icon' => '◌'],
		'testimonial' => ['Depoimento', 'Depoimentos', 'public' => false, 'group' => 'Pessoas', 'icon' => '“'],
		'global'      => ['Configuração', 'Configurações do site', 'public' => false, 'group' => 'Configurações', 'icon' => '⚙'],
	];
}

/** Modelos de página: cada modelo define seções/campos e a "função" da página. */
function page_templates(): array
{
	return [
		'home'      => 'Home',
		'sm'        => 'Método S&M',
		'ecosystem' => 'Ecossistema Full',
		'cases'     => 'Cases (listagem)',
		'insights'  => 'Insights (listagem)',
		'press'     => 'Full na Mídia',
		'contact'   => 'Contato',
		'legal'     => 'Texto jurídico (privacidade/cookies)',
	];
}

/** Grupos de campos de um registro. */
function groups_for(array $entry): array
{
	return match ($entry['type']) {
		'page'   => fck_template_schemas()[$entry['template']] ?? [],
		'global' => fck_global_schemas()[$entry['slug']] ?? [],
		default  => fck_type_schemas()[$entry['type']] ?? [],
	};
}

// ---------------------------------------------------------------------------
// Modo preview: usuário do painel + ?preview=1 → lê rascunhos.
// ---------------------------------------------------------------------------

function is_preview(): bool
{
	static $p = null;
	return $p ??= (isset($_GET['preview']) && is_staff());
}

/** Linha pública de um registro, com dados do modo atual (publicado ou rascunho). */
function entry_view(?array $row): ?array
{
	if (!$row) {
		return null;
	}
	if (is_preview()) {
		$row['data'] = json_dec($row['draft']);
	} elseif ($row['published'] === null) {
		return null;
	} else {
		$row['data']  = json_dec($row['published']);
		$row['slug']  = (string) $row['published_slug'];
		$row['title'] = (string) $row['published_title'];
	}
	if ($row['type'] === 'platform') {
		$row['data'] = platform_tokens($row['data'], (string) $row['title']);
	}
	return $row;
}

/** Troca {platform} pelo nome da plataforma (título do registro) em todos os textos. */
function platform_tokens(array $data, string $name): array
{
	array_walk_recursive($data, function (&$v) use ($name) {
		if (is_string($v) && str_contains($v, '{platform}')) {
			$v = str_replace('{platform}', $name, $v);
		}
	});
	return $data;
}

/**
 * Afirmações de funcionalidade (docs/CLAIMS_REGISTER.md): "validar" aparece
 * fora de produção (com selo no preview) e some em produção — ou sempre que
 * FCK_CLAIMS_STRICT=1 / 'claims_strict' => true em app/config.php.
 */
function claims_strict(): bool
{
	$env = getenv('FCK_CLAIMS_STRICT');
	if ($env !== false && $env !== '') {
		return in_array(strtolower($env), ['1', 'true', 'yes'], true);
	}
	return (bool) fck_config('claims_strict', fck_is_production());
}

function claim_visible(array $item, string $key = 'claim_status'): bool
{
	return ($item[$key] ?? 'confirmado') !== 'validar' || !claims_strict();
}

function claim_pending(array $item, string $key = 'claim_status'): bool
{
	return ($item[$key] ?? 'confirmado') === 'validar';
}

/** Contato com assunto e plataforma pré-preenchidos (D4). */
function platform_contact_url(array $platform): string
{
	return url('/contato') . '?' . http_build_query(['assunto' => 'projeto', 'plataforma' => (string) $platform['slug']]);
}

function entry_find(int $id): ?array
{
	static $cache = [];
	if (!$id) {
		return null;
	}
	if (!array_key_exists($id, $cache)) {
		$cache[$id] = entry_view(db_one('SELECT * FROM entries WHERE id = ?', [$id]));
	}
	return $cache[$id];
}

function entry_by_slug(string $type, string $slug): ?array
{
	$col = is_preview() ? 'slug' : 'published_slug';
	$row = db_one("SELECT * FROM entries WHERE type = ? AND $col = ? ORDER BY published IS NULL, id LIMIT 1", [$type, $slug]);
	return entry_view($row);
}

/** Registros visíveis de um tipo, na ordem do painel. */
function entries_of(string $type, array $opts = []): array
{
	$where = is_preview() ? 'type = ?' : 'type = ? AND published IS NOT NULL';
	$order = $opts['order'] ?? 'menu_order ASC, id ASC';
	$rows  = db_all("SELECT * FROM entries WHERE $where ORDER BY $order", [$type]);
	return array_values(array_filter(array_map('entry_view', $rows)));
}

/** Grupo de campos de um registro visível. */
function g(array $entry, string $group): array
{
	$v = $entry['data'][$group] ?? [];
	return is_array($v) ? $v : [];
}

function section_enabled(array $section): bool
{
	return !array_key_exists('enabled', $section) || !empty($section['enabled']);
}

/** Página com determinado modelo (função). */
function page_by_template(string $template): ?array
{
	static $cache = [];
	if (!array_key_exists($template, $cache)) {
		$where           = is_preview() ? '' : 'AND published IS NOT NULL';
		$row             = db_one("SELECT * FROM entries WHERE type = 'page' AND template = ? $where ORDER BY menu_order, id LIMIT 1", [$template]);
		$cache[$template] = entry_view($row);
	}
	return $cache[$template];
}

/** Bases de URL dos itens: slug da página com a função (ou padrão técnico). */
function route_base(string $type): string
{
	$map      = ['platform' => ['ecosystem', 'ecossistema'], 'case' => ['cases', 'cases'], 'article' => ['insights', 'insights']];
	[$tpl, $fallback] = $map[$type];
	$page = page_by_template($tpl);
	return $page ? $page['slug'] : $fallback;
}

/** Caminho público de um registro. */
function path_for(array $entry): ?string
{
	return match ($entry['type']) {
		'page'                        => $entry['template'] === 'home' ? '/' : '/' . $entry['slug'],
		'platform', 'case', 'article' => '/' . route_base($entry['type']) . '/' . $entry['slug'],
		default                       => null,
	};
}

function url_for(array $entry): string
{
	$path = path_for($entry) ?? '/';
	$url  = url($path === '/' ? '/' : $path);
	return is_preview() ? $url . '?preview=1' : $url;
}

/**
 * Resolve um link do painel. Null se não houver texto (quando exigido) ou se
 * o destino não estiver publicado — nesse caso o botão não aparece.
 *
 * @return array{href:string,label:string,external:bool}|null
 */
function link_of(mixed $link, bool $require_label = true): ?array
{
	if (!is_array($link)) {
		return null;
	}
	$label = trim((string) ($link['label'] ?? ''));
	if ($require_label && $label === '') {
		return null;
	}
	$href = '';
	if (($link['type'] ?? 'post') === 'url') {
		$href = trim((string) ($link['url'] ?? ''));
		if (str_starts_with($href, '/')) {
			$href = url($href);
		}
	} elseif (!empty($link['post']) && ($target = entry_find((int) $link['post']))) {
		$href = url_for($target);
	}
	if ($href === '') {
		return null;
	}
	return ['href' => $href, 'label' => $label, 'external' => is_external_url($href)];
}

// ---------------------------------------------------------------------------
// Globais
// ---------------------------------------------------------------------------

function global_entry(string $key): ?array
{
	static $cache = [];
	if (!array_key_exists($key, $cache)) {
		$row = db_one("SELECT * FROM entries WHERE type = 'global' AND slug = ? LIMIT 1", [$key]);
		if ($row) {
			// Globais: em preview lê o rascunho; senão o publicado (ou rascunho se nunca publicado).
			$row['data'] = json_dec(is_preview() || $row['published'] === null ? $row['draft'] : $row['published']);
		}
		$cache[$key] = $row;
	}
	return $cache[$key];
}

function site(string $group): array
{
	$g = global_entry('site');
	return $g ? (array) ($g['data'][$group] ?? []) : [];
}

/** Microtexto de interface: ui('a11y', 'open_menu'). */
function ui(string $group, string $key): string
{
	$g      = global_entry('ui');
	$stored = $g['data']['ui_' . $group] ?? [];
	if (is_array($stored) && array_key_exists($key, $stored)) {
		return (string) $stored[$key]; // valor do painel (vazio = oculto de propósito)
	}
	// Campo criado depois da instalação: usa o texto inicial do seed até ser salvo no painel.
	static $seed = null;
	$seed ??= fck_seed_content()['ui'];
	return (string) ($seed[$group][$key] ?? '');
}

// ---------------------------------------------------------------------------
// Gravação (painel)
// ---------------------------------------------------------------------------

function entry_raw(int $id): ?array
{
	return db_one('SELECT * FROM entries WHERE id = ?', [$id]);
}

function entry_create(string $type, string $title, array $attrs = []): int
{
	$slug = $attrs['slug'] ?? slugify($title);
	return db_insert('entries', [
		'type'       => $type,
		'template'   => $attrs['template'] ?? '',
		'slug'       => entry_unique_slug($type, $slug ?: $type),
		'title'      => $title,
		'status'     => 'draft',
		'menu_order' => (int) ($attrs['menu_order'] ?? 0),
		'draft'      => json_enc($attrs['data'] ?? []),
		'seed_key'   => $attrs['seed_key'] ?? null,
		'created_at' => now(),
		'updated_at' => now(),
		'updated_by' => current_user()['id'] ?? null,
	]);
}

function entry_unique_slug(string $type, string $slug, int $ignore = 0): string
{
	$base = $slug;
	$n    = 2;
	while (db_value('SELECT id FROM entries WHERE type = ? AND (slug = ? OR published_slug = ?) AND id <> ?', [$type, $slug, $slug, $ignore])) {
		$slug = $base . '-' . $n++;
	}
	return $slug;
}

/** Salva o rascunho (o site público não muda). */
function entry_save_draft(int $id, array $changes, string $action = 'save'): void
{
	$row = entry_raw($id);
	if (!$row) {
		return;
	}
	$data = array_merge([
		'title'      => $row['title'],
		'slug'       => $row['slug'],
		'template'   => $row['template'],
		'menu_order' => $row['menu_order'],
		'draft'      => $row['draft'],
	], $changes);
	$data['status']     = $row['published'] === null ? 'draft' : ($data['draft'] === $row['published'] && $data['slug'] === $row['published_slug'] && $data['title'] === $row['published_title'] ? 'published' : 'changed');
	$data['updated_at'] = now();
	$data['updated_by'] = current_user()['id'] ?? null;
	db_update('entries', $data, ['id' => $id]);
	revision_add($id, (string) $data['title'], (string) $data['slug'], (string) $data['draft'], $action);
}

/** Publica o rascunho atual. Cria redirect 301 se o endereço mudou. */
function entry_publish(int $id): void
{
	$row = entry_raw($id);
	if (!$row) {
		return;
	}
	$old_path = null;
	if ($row['published'] !== null && in_array($row['type'], ['page', 'platform', 'case', 'article'], true)) {
		$old_path = path_for(['type' => $row['type'], 'template' => $row['template'], 'slug' => (string) $row['published_slug']]);
	}
	$old_bases = $row['type'] === 'page' ? route_bases_snapshot() : [];

	db_update('entries', [
		'published'       => $row['draft'],
		'published_slug'  => $row['slug'],
		'published_title' => $row['title'],
		'published_at'    => $row['published_at'] ?: now(),
		'status'          => 'published',
		'updated_at'      => now(),
	], ['id' => $id]);
	revision_add($id, (string) $row['title'], (string) $row['slug'], (string) $row['draft'], 'publish');

	if ($old_path !== null) {
		$new_path = path_for(['type' => $row['type'], 'template' => $row['template'], 'slug' => (string) $row['slug']]);
		if ($new_path && $new_path !== $old_path) {
			redirect_add($old_path, $new_path, 'exact', true);
		}
	}
	// Página-base mudou de slug: redirect de prefixo para plataformas/cases/artigos.
	if ($old_bases) {
		foreach (route_bases_snapshot() as $type => $base) {
			if (($old_bases[$type] ?? $base) !== $base) {
				redirect_add('/' . $old_bases[$type] . '/', '/' . $base . '/', 'prefix', true);
			}
		}
	}
}

function route_bases_snapshot(): array
{
	$out = [];
	foreach (['platform' => 'ecosystem', 'case' => 'cases', 'article' => 'insights'] as $type => $tpl) {
		$out[$type] = (string) (db_value("SELECT published_slug FROM entries WHERE type='page' AND template = ? AND published IS NOT NULL ORDER BY menu_order, id LIMIT 1", [$tpl]) ?: route_base($type));
	}
	return $out;
}

function entry_unpublish(int $id): void
{
	db_update('entries', ['published' => null, 'published_slug' => null, 'published_title' => null, 'status' => 'draft', 'updated_at' => now()], ['id' => $id]);
	$row = entry_raw($id);
	if ($row) {
		revision_add($id, (string) $row['title'], (string) $row['slug'], (string) $row['draft'], 'unpublish');
	}
}

function entry_delete(int $id): void
{
	db_delete('revisions', ['entry_id' => $id]);
	db_delete('entries', ['id' => $id]);
}

function revision_add(int $entry_id, string $title, string $slug, string $data, string $action): void
{
	db_insert('revisions', [
		'entry_id'   => $entry_id,
		'title'      => $title,
		'slug'       => $slug,
		'data'       => $data,
		'action'     => $action,
		'user_id'    => current_user()['id'] ?? null,
		'created_at' => now(),
	]);
	// Mantém as 50 versões mais recentes por registro.
	$keep = db_value('SELECT id FROM revisions WHERE entry_id = ? ORDER BY id DESC LIMIT 1 OFFSET 49', [$entry_id]);
	if ($keep) {
		db_query('DELETE FROM revisions WHERE entry_id = ? AND id < ?', [$entry_id, $keep]);
	}
}

/** Restaura uma versão como rascunho (publicar continua sendo um passo explícito). */
function revision_restore(int $revision_id): ?int
{
	$rev = db_one('SELECT * FROM revisions WHERE id = ?', [$revision_id]);
	if (!$rev) {
		return null;
	}
	entry_save_draft((int) $rev['entry_id'], ['title' => $rev['title'], 'slug' => $rev['slug'], 'draft' => $rev['data']], 'restore');
	return (int) $rev['entry_id'];
}

// ---------------------------------------------------------------------------
// Artigos
// ---------------------------------------------------------------------------

/** Data editorial do artigo (campo "Data" ou primeira publicação). */
function article_date(array $a): string
{
	$d = (string) (g($a, 'details')['date'] ?? '');
	return preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) ? $d : (string) ($a['published_at'] ?: $a['created_at']);
}

/** Artigos visíveis, do mais recente ao mais antigo. */
function articles_sorted(): array
{
	$items = entries_of('article');
	usort($items, fn($x, $y) => strcmp(article_date($y), article_date($x)));
	return $items;
}
