<?php
/**
 * Importação do conteúdo inicial — idempotente.
 * Cada registro tem uma chave estável (seed_key). Rodar de novo só cria o
 * que não existe e nunca sobrescreve edições do painel. "reset" (só em
 * ambiente local) recria tudo.
 */

declare(strict_types=1);

require_once FCK_APP . '/seed/content.php';

final class Seeder
{
	private array $ids = ['page' => [], 'platform' => []];
	public array $log = [];
	private array $fresh = [];

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
		foreach ($c['partners'] ?? [] as $slug => $p) {
			$this->ensure('partner', 'partner:' . $slug, $p + ['slug' => $slug, 'status' => 'draft']);
		}
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
		$this->fresh[$id] = true;
		$this->log[]      = "Criado: {$def['title']} ($type)";
		return $id;
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
			if (is_array($v) && array_key_exists('to', $v) && array_key_exists('label', $v)) {
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
}

/** Impressão digital do conteúdo inicial: muda quando app/seed/content.php muda. */
function seed_version(): string
{
	return substr(md5_file(FCK_APP . '/seed/content.php') ?: '', 0, 12);
}

function run_seed(bool $reset = false): array
{
	return (new Seeder($reset))->run();
}
