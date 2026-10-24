<?php
/**
 * Importação do conteúdo inicial — idempotente.
 * Cada registro tem uma chave estável (seed_key). Rodar de novo só cria o
 * que não existe e nunca sobrescreve edições do painel. "reset" (só em
 * ambiente local) recria tudo.
 */

declare(strict_types=1);

require_once FCK_APP . '/seed/content.php';
require_once FCK_APP . '/seed/legacy-eco.php';

final class Seeder
{
	private array $ids = ['page' => [], 'platform' => []];
	public array $log = [];
	private array $fresh = [];
	/** Registros criados nesta execução (já nascem com o conteúdo atual). */
	private array $created = [];

	public function __construct(private bool $reset = false)
	{
		if ($this->reset && fck_env() !== 'local') {
			$this->reset = false;
			$this->log[] = 'Reset ignorado: permitido apenas em ambiente local.';
		}
	}

	public function run(): array
	{
		$c = fck_seed_content();
		foreach ($c['pages'] as $key => $p) {
			$this->ids['page'][$key] = $this->ensure('page', 'page:' . $key, $p);
		}
		foreach ($c['platforms'] as $key => $p) {
			$this->ids['platform'][$key] = $this->ensure('platform', 'platform:' . $key, $p + ['status' => 'publish']);
		}
		foreach ($c['sectors'] as $slug => $p) {
			$this->ensure('sector', 'sector:' . $slug, $p + ['slug' => $slug, 'status' => 'publish']);
		}
		$partners = [];
		foreach ($c['partners'] ?? [] as $slug => $p) {
			$id   = $this->ensure('partner', 'partner:' . $slug, $p + ['slug' => $slug, 'status' => 'draft']);
			$meta = $p['meta'] ?? [];
			if (!empty($p['photo']['file']) && ($mid = $this->photo('photo:' . $slug, $p['photo']))) {
				$meta['details']['photo'] = $mid;
			}
			$this->complete($id, $meta);
			$partners[] = $id;
		}
		$this->publishPartnersOnce($partners);
		$this->clients($c['clients'] ?? []);
		$order = 0;
		foreach ($c['categories'] as $slug => $name) {
			$this->ensure('category', 'category:' . $slug, ['title' => $name, 'slug' => $slug, 'status' => 'publish', 'order' => ++$order]);
		}

		// Segunda passagem: dados (os links precisam dos IDs).
		foreach (['page', 'platform'] as $kind) {
			$source = $kind === 'page' ? $c['pages'] : $c['platforms'];
			foreach ($source as $key => $p) {
				$this->fill($this->ids[$kind][$key], $p['meta'] ?? [], ($p['status'] ?? 'publish') === 'publish');
			}
		}
		foreach ($c['sectors'] as $slug => $p) {
			$id = (int) db_value('SELECT id FROM entries WHERE seed_key = ?', ['sector:' . $slug]);
			$this->fill($id, $p['meta'] ?? [], true);
		}
		foreach (array_keys($c['categories']) as $slug) {
			$id = (int) db_value('SELECT id FROM entries WHERE seed_key = ?', ['category:' . $slug]);
			$this->fill($id, [], true);
		}

		$site         = $c['site'];
		$site['menu'] = $this->menus($c['menus']) + ['cta' => $site['header']['cta'] ?? []];
		unset($site['header']);
		$this->globals($site, $c['ui']);
		$this->logos($c['logos']);
		$this->migrateEco($c);
		$this->addPartnersMenuOnce();
		$this->log[] = 'Concluído.';
		return $this->log;
	}

	private function find(string $key): ?int
	{
		$id = db_value('SELECT id FROM entries WHERE seed_key = ?', [$key]);
		return $id ? (int) $id : null;
	}

	private function ensure(string $type, string $key, array $def): int
	{
		$id = $this->find($key);
		if ($id && !$this->reset) {
			return $id;
		}
		if ($id) {
			entry_delete($id);
		}
		$id = entry_create($type, $def['title'], [
			'slug'       => $def['slug'] ?? slugify($def['title']),
			'template'   => $def['template'] ?? '',
			'menu_order' => (int) ($def['order'] ?? 0),
			'seed_key'   => $key,
		]);
		$this->fresh[$id]   = true;
		$this->created[$id] = true;
		$this->log[]        = "Criado: {$def['title']} ($type)";
		return $id;
	}

	/** Importa uma foto do seed para a biblioteca (uma vez) com alt, ponto focal e direito de uso. */
	private function photo(string $key, array $def, string $license = 'Retrato enviado pela Full Connect Key'): ?int
	{
		$mid = (int) db_value('SELECT id FROM media WHERE seed_key = ?', [$key]);
		if ($mid) {
			return $mid;
		}
		[$mid, $err] = media_import(FCK_APP . '/seed/' . $def['file'], basename($def['file']), ['alt' => $def['alt'] ?? '', 'rights_confirmed' => true, 'license' => $license, 'seed_key' => $key]);
		if (!$mid) {
			$this->log[] = "Foto não importada ({$def['file']}): $err";
			return null;
		}
		if (!empty($def['focal'])) {
			db_update('media', ['focal' => $def['focal']], ['id' => $mid]);
		}
		$this->log[] = 'Mídia importada: ' . ($def['alt'] ?? $def['file']);
		return (int) $mid;
	}

	/**
	 * Clientes da faixa de logos: cria, importa o logo e registra a
	 * autorização. Publica uma única vez (clients-2026-10); quem for
	 * despublicado no painel depois disso não volta.
	 */
	private function clients(array $clients): void
	{
		$ids = [];
		foreach ($clients as $slug => $p) {
			$id  = $this->ensure('client', 'client:' . $slug, $p + ['slug' => $slug]);
			$mid = $this->photo('client-logo:' . $slug, ['file' => $p['logo'], 'alt' => $p['title']], 'Logo do cliente — exibição autorizada (confirmado pela Full em 24/10/2026)');
			if (!$mid) {
				continue;
			}
			$this->complete($id, ['details' => ['logo' => $mid, 'authorized' => true, 'authorization_note' => 'Autorização de exibição confirmada pela Full Connect Key em 24/10/2026.']]);
			$ids[] = $id;
		}
		$done = (array) setting('seed_migrations', []);
		if (!empty($done['clients-2026-10']) || !$ids) {
			return;
		}
		foreach ($ids as $id) {
			if (entry_raw($id)['published'] === null) {
				entry_publish($id);
			}
		}
		setting_set('seed_migrations', $done + ['clients-2026-10' => now()]);
		$this->log[] = 'Clientes publicados na faixa de logos (' . count($ids) . ').';
	}

	/**
	 * Publica os sócios uma única vez, quando os três estão completos (cargo,
	 * bio e retrato) e nunca foram publicados. Se forem despublicados no
	 * painel depois disso, o seed não os republica.
	 */
	private function publishPartnersOnce(array $ids): void
	{
		$done = (array) setting('seed_migrations', []);
		if (!empty($done['partners-2026-10']) || count($ids) < 3) {
			return;
		}
		foreach ($ids as $id) {
			$row = entry_raw($id);
			$d   = json_dec($row['draft'])['details'] ?? [];
			if ($row['published'] !== null || !has_text($d['role'] ?? '') || !has_text($d['bio'] ?? '') || !media_ok($d['photo'] ?? 0)) {
				return;
			}
		}
		foreach ($ids as $id) {
			entry_publish($id);
		}
		setting_set('seed_migrations', $done + ['partners-2026-10' => now()]);
		$this->log[] = 'Sócios publicados (cargo, bio e retrato completos).';
	}

	/**
	 * Completa no rascunho só os campos vazios (nunca sobrescreve o que foi
	 * editado no painel e não publica). Usado para dados que chegaram depois
	 * da instalação, como os cargos dos sócios.
	 */
	private function complete(int $id, array $meta): void
	{
		if (!$id || !$meta) {
			return;
		}
		$draft   = json_dec(entry_raw($id)['draft'] ?? null);
		$changed = false;
		foreach ($meta as $group => $fields) {
			foreach ((array) $fields as $k => $v) {
				$cur = $draft[$group][$k] ?? null;
				if ($cur === null || $cur === '' || $cur === [] || (is_string($cur) && trim($cur) === '')) {
					$draft[$group][$k] = $v;
					$changed           = true;
				}
			}
		}
		if ($changed) {
			entry_save_draft($id, ['draft' => json_enc($draft)], 'seed');
			$this->log[] = 'Completado: ' . entry_raw($id)['title'];
		}
		unset($this->fresh[$id]);
	}

	/** Grava os dados só em registros recém-criados; publica se indicado. */
	private function fill(int $id, array $meta, bool $publish): void
	{
		if (!$id || empty($this->fresh[$id])) {
			return;
		}
		$row = entry_raw($id);
		entry_save_draft($id, ['draft' => json_enc($this->links($meta))], 'seed');
		if ($publish) {
			entry_publish($id);
		}
		unset($this->fresh[$id]);
	}

	private function links(array $values): array
	{
		foreach ($values as $k => $v) {
			if (is_string($v) && str_starts_with($v, '@platform:')) {
				$values[$k] = $this->ids['platform'][substr($v, 10)] ?? null;
			} elseif (is_array($v) && array_key_exists('to', $v) && array_key_exists('label', $v)) {
				$values[$k] = $this->link((string) $v['label'], (string) $v['to']);
			} elseif (is_array($v)) {
				$values[$k] = $this->links($v);
			}
		}
		return $values;
	}

	private function target(string $to): ?int
	{
		[$kind, $key] = array_pad(explode(':', $to, 2), 2, '');
		return $this->ids[$kind][$key] ?? null;
	}

	private function link(string $label, string $to): array
	{
		if (str_starts_with($to, 'url:')) {
			return ['label' => $label, 'type' => 'url', 'post' => null, 'url' => substr($to, 4)];
		}
		return ['label' => $label, 'type' => 'post', 'post' => $this->target($to), 'url' => ''];
	}

	/** Converte a estrutura de menus do seed nos repetidores do grupo "Menus". */
	private function menus(array $menus): array
	{
		$item = fn(array $i) => ['link' => $this->link($i['label'], $i['to'])];
		$main = [];
		foreach ($menus['primary']['items'] as $i) {
			$main[] = $item($i) + ['children' => array_map($item, $i['children'] ?? [])];
		}
		$footer = [];
		foreach ($menus['footer']['items'] as $g) {
			$footer[] = ['title' => $g['label'], 'links' => array_map($item, $g['children'] ?? [])];
		}
		return ['main' => $main, 'footer_groups' => $footer, 'legal' => array_map($item, $menus['legal']['items'])];
	}

	private function globals(array $site, array $ui): void
	{
		$ui_groups = [];
		foreach ($ui as $g => $v) {
			$ui_groups['ui_' . $g] = $v;
		}
		foreach (['site' => $site, 'ui' => $ui_groups] as $key => $groups) {
			$id = $this->find('global:' . $key);
			if (!$id) {
				$id = entry_create('global', fck_global_keys()[$key], ['slug' => $key, 'seed_key' => 'global:' . $key]);
				$this->log[] = 'Criado: ' . fck_global_keys()[$key];
			}
			$row     = entry_raw($id);
			$current = $this->reset ? [] : json_dec($row['draft']);
			$merged  = $current;
			foreach ($groups as $g => $values) {
				$values      = $this->links($values);
				$merged[$g]  = is_array($current[$g] ?? null) ? $current[$g] + $values : $values; // só completa chaves ausentes
			}
			if ($merged !== $current || $row['published'] === null) {
				entry_save_draft($id, ['draft' => json_enc($merged)], 'seed');
				entry_publish($id);
			}
		}
	}

	private function logos(array $logos): void
	{
		$site = entry_raw((int) $this->find('global:site'));
		foreach ($logos as $key => $logo) {
			$mid = (int) db_value('SELECT id FROM media WHERE seed_key = ?', ['logo:' . $key]);
			if (!$mid) {
				[$mid, $err] = media_import(FCK_APP . '/seed/logos/' . $logo['file'], $logo['file'], ['alt' => $logo['alt'], 'rights_confirmed' => true, 'license' => 'Logo oficial — Full Connect Key', 'seed_key' => 'logo:' . $key]);
				if (!$mid) {
					$this->log[] = "Logo não importado ({$logo['file']}): $err";
					continue;
				}
				$this->log[] = "Mídia importada: {$logo['alt']} ($key)";
			}
			$parts = explode(':', $logo['target']);
			if ($parts[0] === 'site' && $site) {
				$this->set_if_empty((int) $site['id'], $parts[1], $parts[2], (int) $mid);
			} elseif ($parts[0] === 'platform' && ($pid = $this->ids['platform'][$parts[1]] ?? null)) {
				$this->set_if_empty($pid, 'identity', $parts[2] ?? 'logo', (int) $mid);
			}
		}
	}

	private function set_if_empty(int $id, string $group, string $field, int $value): void
	{
		$row  = entry_raw($id);
		$data = json_dec($row['draft']);
		if (!empty($data[$group][$field])) {
			return;
		}
		$data[$group][$field] = $value;
		$was_live = $row['status'] === 'published';
		entry_save_draft($id, ['draft' => json_enc($data)], 'seed');
		if ($was_live || $row['published'] !== null) {
			entry_publish($id);
		}
		$this->log[] = "Logo aplicado: $group.$field (#$id)";
	}

	// ------------------------------------------------------------------
	// Migração da Sprint ECO (docs/BRIEFING_ECOSSISTEMA.md, E5), uma vez só.
	// Regra: campo vazio ou ainda igual ao seed v1.1 (app/seed/legacy-eco.php)
	// recebe o texto novo; campo diferente é edição do painel e fica como
	// está (conflito registrado). O estado anterior vira uma versão no
	// histórico. Registros publicados e sem alterações pendentes são
	// republicados (troca de slug gera redirect 301 automático).
	// ------------------------------------------------------------------
	private function migrateEco(array $c): void
	{
		$done = (array) setting('seed_migrations', []);
		if (!empty($done['eco-2026-10'])) {
			return;
		}
		$legacy    = fck_seed_legacy_eco();
		$conflicts = [];
		foreach ($c['platforms'] as $key => $p) {
			$id = $this->ids['platform'][$key] ?? null;
			if (!$id || isset($this->created[$id]) || !isset($legacy['platforms'][$key])) {
				continue;
			}
			$old = $legacy['platforms'][$key];
			$this->migrateEntry($id, $this->links($p['meta']), $this->links($old['meta']), "platform:$key", $conflicts, $p['title'], $old['title'], $p['slug'], $old['slug'], (int) $p['order'], (int) $old['order']);
		}
		$pages = [
			'home'      => ['hero' => ['headline_alt' => $c['pages']['home']['meta']['hero']['headline_alt']], 'ecosystem' => $c['pages']['home']['meta']['ecosystem']],
			'ecosystem' => $c['pages']['ecosystem']['meta'],
		];
		foreach ($pages as $key => $meta) {
			$id = $this->ids['page'][$key] ?? null;
			if ($id && !isset($this->created[$id])) {
				$this->migrateEntry($id, $this->links($meta), $this->links($legacy['pages'][$key]), "page:$key", $conflicts);
			}
		}
		$this->migrateMenus($c, $legacy);
		setting_set('seed_migrations', $done + ['eco-2026-10' => now()]);
		setting_set('eco_migration_conflicts', $conflicts);
		$this->log[] = 'Migração do Ecossistema aplicada' . ($conflicts ? ' (' . count($conflicts) . ' edição(ões) do painel preservada(s))' : '') . '.';
	}

	private function migrateEntry(int $id, array $new, array $old, string $key, array &$conflicts, ?string $title = null, ?string $old_title = null, ?string $slug = null, ?string $old_slug = null, ?int $order = null, ?int $old_order = null): void
	{
		$row = entry_raw($id);
		if (!$row) {
			return;
		}
		$clean   = $row['status'] === 'published';
		$current = json_dec($row['draft']);
		$merged  = $this->mergeEco($current, $new, $old, $key, $conflicts);
		$changes = [];
		if ($merged !== $current) {
			$changes['draft'] = json_enc($merged);
		}
		if ($title !== null && $row['title'] !== $title && $row['title'] === $old_title) {
			$changes['title'] = $title;
		}
		if ($slug !== null && $row['slug'] !== $slug && $row['slug'] === $old_slug) {
			$changes['slug'] = $slug;
		}
		if ($order !== null && (int) $row['menu_order'] !== $order && (int) $row['menu_order'] === $old_order) {
			db_update('entries', ['menu_order' => $order], ['id' => $id]);
		}
		if (!$changes) {
			return;
		}
		revision_add($id, (string) $row['title'], (string) $row['slug'], (string) $row['draft'], 'antes-eco');
		entry_save_draft($id, $changes, 'migracao-eco');
		if ($clean) {
			entry_publish($id);
		}
		$this->log[] = 'Migrado: ' . ($changes['title'] ?? $row['title']) . ($clean ? ' (publicado)' : ' (rascunho)');
	}

	private function mergeEco(array $current, array $new, array $old, string $path, array &$conflicts): array
	{
		$blank = static fn($v) => $v === null || $v === '' || $v === [];
		foreach ($new as $k => $v) {
			$cur = $current[$k] ?? null;
			$was = $old[$k] ?? null;
			if (is_array($v) && !array_is_list($v)) {
				$current[$k] = $this->mergeEco(is_array($cur) ? $cur : [], $v, is_array($was) ? $was : [], "$path.$k", $conflicts);
			} elseif ($blank($cur) || $cur === $was) {
				$current[$k] = $v;
			} elseif ($cur !== $v) {
				$conflicts[] = "$path.$k";
			}
		}
		return $current;
	}

	/**
	 * Uma vez só: adiciona "Sócios" (→ /#socios) ao menu principal, depois de
	 * "Ecossistema Full", e ao grupo do rodapé, depois de "S&M", em sites já
	 * instalados. Se o item for removido no painel depois, não volta.
	 */
	private function addPartnersMenuOnce(): void
	{
		$done = (array) setting('seed_migrations', []);
		$id   = $this->find('global:site');
		if (!empty($done['menu-socios-2026-10']) || !$id) {
			return;
		}
		$row   = entry_raw($id);
		$draft = json_dec($row['draft']);
		$json  = (string) $row['draft'];
		if (!str_contains($json, '#socios')) {
			$item = ['link' => $this->link('Sócios', 'url:/#socios')];
			$eco  = $this->ids['page']['ecosystem'] ?? 0;
			$sm   = $this->ids['page']['sm'] ?? 0;
			$main = (array) ($draft['menu']['main'] ?? []);
			$pos  = count($main);
			foreach ($main as $i => $it) {
				if ((int) ($it['link']['post'] ?? 0) === (int) $eco) {
					$pos = $i + 1;
				}
			}
			array_splice($main, $pos, 0, [$item + ['children' => []]]);
			$draft['menu']['main'] = $main;
			if (isset($draft['menu']['footer_groups'][0]['links'])) {
				$links = (array) $draft['menu']['footer_groups'][0]['links'];
				$pos   = count($links);
				foreach ($links as $i => $it) {
					if ((int) ($it['link']['post'] ?? 0) === (int) $sm) {
						$pos = $i + 1;
					}
				}
				array_splice($links, $pos, 0, [$item]);
				$draft['menu']['footer_groups'][0]['links'] = $links;
			}
			entry_save_draft($id, ['draft' => json_enc($draft)], 'seed');
			entry_publish($id);
			$this->log[] = 'Menu: item "Sócios" adicionado.';
		}
		setting_set('seed_migrations', $done + ['menu-socios-2026-10' => now()]);
	}

	/** Menus: renomeia itens de plataforma ainda com o nome antigo e segue a nova ordem. */
	private function migrateMenus(array $c, array $legacy): void
	{
		$id = $this->find('global:site');
		if (!$id) {
			return;
		}
		$names = $order = [];
		foreach ($c['platforms'] as $key => $p) {
			$pid = $this->ids['platform'][$key] ?? null;
			if ($pid) {
				$names[$pid] = [$legacy['platforms'][$key]['title'] ?? null, $p['title']];
				$order[$pid] = (int) $p['order'];
			}
		}
		$row   = entry_raw($id);
		$draft = json_dec($row['draft']);
		$walk  = function (array $list) use (&$walk, $names, $order): array {
			$slots = [];
			foreach ($list as $i => $item) {
				if (!is_array($item)) {
					continue;
				}
				foreach (['children', 'links'] as $sub) {
					if (isset($item[$sub]) && is_array($item[$sub])) {
						$list[$i][$sub] = $walk($item[$sub]);
					}
				}
				$pid = (int) ($item['link']['post'] ?? 0);
				if (($item['link']['type'] ?? '') === 'post' && isset($names[$pid])) {
					if (($item['link']['label'] ?? '') === $names[$pid][0]) {
						$list[$i]['link']['label'] = $names[$pid][1];
					}
					$slots[] = $i;
				}
			}
			if (count($slots) > 1) {
				$items = array_map(fn($i) => $list[$i], $slots);
				usort($items, fn($a, $b) => $order[(int) $a['link']['post']] <=> $order[(int) $b['link']['post']]);
				foreach ($slots as $n => $i) {
					$list[$i] = $items[$n];
				}
			}
			return $list;
		};
		$menu = (array) ($draft['menu'] ?? []);
		foreach (['main', 'footer_groups'] as $k) {
			if (isset($menu[$k]) && is_array($menu[$k])) {
				$menu[$k] = $walk($menu[$k]);
			}
		}
		if ($menu !== ($draft['menu'] ?? [])) {
			$draft['menu'] = $menu;
			entry_save_draft($id, ['draft' => json_enc($draft)], 'migracao-eco');
			entry_publish($id);
			$this->log[] = 'Menus: plataformas renomeadas e reordenadas.';
		}
	}
}

/** Impressão digital do conteúdo inicial: muda quando app/seed/content.php muda. */
function seed_version(): string
{
	return substr(md5((string) md5_file(FCK_APP . '/seed/content.php') . md5_file(FCK_APP . '/seed/platforms.php')), 0, 12);
}

function run_seed(bool $reset = false): array
{
	return (new Seeder($reset))->run();
}
