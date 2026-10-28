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
		$this->team($c['team'] ?? []);
		$this->teamOrderOnce($c['team'] ?? [], 'team-order-2026-10');
		$this->gabrielaUpdateOnce($c['team']['gabriela-oliveira'] ?? []);
		$this->teamCopyOnce('gabriela-oliveira', 'Conecta estratégia a execução.', $c['team']['gabriela-oliveira']['meta']['details']['connects'] ?? '', 'team-gabriela-2026-10');
		$this->teamCopyOnce('monique-godoy', 'Conecta ideias criativas a conteúdos que geram conversas e engajamento de verdade.', $c['team']['monique-godoy']['meta']['details']['connects'] ?? '', 'team-monique-2026-10');
		$this->credentials($c['credentials'] ?? []);
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
		$this->removePartnersMenuOnce();
		$this->migrateHomeCopy($c);
		$this->migrateHubProblemOnce($c);
		$this->migrateHomeHeadlineOnce($c);
		$this->addCredentialsSectionOnce($c);
		$this->platformVideosOnce($c['platform_videos'] ?? []);
		$this->ecosystemVideoOnce($c['ecosystem_video'] ?? []);
		$this->headerCtaOnce($c['site']['header']['cta']);
		$this->signatureOnce('Vire a chave dos seus resultados. Ative o modo Full.', $c['site']['brand']['signature'], 'signature-2026-10');
		$this->hidePagesOnce(['cases', 'insights', 'press'], 'hide-pages-2026-10');
		$this->removeClientsOnce(['brentwood' => 'client-brentwood-off-2026-10', 'paraiso-saudavel' => 'client-paraiso-off-2026-10']);
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
	 * Gabriela (28/10/2026): nova foto e cargo "Gerente de Projetos e
	 * Atendimento", uma única vez. Só troca o que ainda estiver como antes.
	 */
	private function gabrielaUpdateOnce(array $p): void
	{
		$done = (array) setting('seed_migrations', []);
		$id   = $this->find('team:gabriela-oliveira');
		if (!empty($done['team-gabriela-foto-2026-10']) || !$id || !$p) {
			return;
		}
		$old_photo = (int) db_value('SELECT id FROM media WHERE seed_key = ?', ['photo:gabriela-oliveira']);
		$new_photo = $this->photo($p['photo']['key'], $p['photo']);
		$conflicts = [];
		$this->migrateEntry($id, ['details' => ['photo' => $new_photo, 'role' => $p['meta']['details']['role']]], ['details' => ['photo' => $old_photo ?: null, 'role' => 'Gerente de Projetos']], 'team:gabriela-oliveira', $conflicts, tag: 'team');
		setting_set('seed_migrations', $done + ['team-gabriela-foto-2026-10' => now()]);
	}

	/** Aplica a ordem do time definida pelo cliente (campo Ordem), uma única vez. */
	private function teamOrderOnce(array $people, string $key): void
	{
		$done = (array) setting('seed_migrations', []);
		if (!empty($done[$key])) {
			return;
		}
		foreach ($people as $slug => $p) {
			if ($id = $this->find('team:' . $slug)) {
				db_update('entries', ['menu_order' => (int) $p['order']], ['id' => $id]);
			}
		}
		setting_set('seed_migrations', $done + [$key => now()]);
		$this->log[] = 'Time: ordem atualizada.';
	}

	/** Troca a frase de uma pessoa do time uma única vez, se ainda estiver com a versão anterior. */
	private function teamCopyOnce(string $slug, string $old, string $new, string $key): void
	{
		$done = (array) setting('seed_migrations', []);
		if (!empty($done[$key]) || $new === '') {
			return;
		}
		$conflicts = [];
		if ($id = $this->find('team:' . $slug)) {
			$this->migrateEntry($id, ['details' => ['connects' => $new]], ['details' => ['connects' => $old]], "team:$slug", $conflicts, tag: 'team');
		}
		setting_set('seed_migrations', $done + [$key => now()]);
	}

	/**
	 * Credenciais da agência: cria, importa o logo e publica cada uma uma
	 * única vez (credential:<slug> em seed_migrations).
	 */
	private function credentials(array $items): void
	{
		$done = (array) setting('seed_migrations', []);
		$new  = [];
		foreach ($items as $slug => $p) {
			$id  = $this->ensure('credential', 'credential:' . $slug, $p + ['slug' => $slug]);
			$mid = $this->photo('credential-logo:' . $slug, ['file' => $p['logo'], 'alt' => $p['title']], 'Logo da plataforma — credencial informada pela Full (26/10/2026)');
			if (!$mid) {
				continue;
			}
			$this->complete($id, ['details' => ['logo' => $mid]]);
			if (empty($done['credential:' . $slug])) {
				if (entry_raw($id)['published'] === null) {
					entry_publish($id);
				}
				$new['credential:' . $slug] = now();
			}
		}
		if ($new) {
			setting_set('seed_migrations', $done + $new);
			$this->log[] = 'Credenciais publicadas (' . count($new) . ').';
		}
	}

	/** Textos da faixa de credenciais na Home de sites já instalados, uma única vez. */
	private function addCredentialsSectionOnce(array $c): void
	{
		$done = (array) setting('seed_migrations', []);
		if (!empty($done['home-credentials-2026-10'])) {
			return;
		}
		$conflicts = [];
		if (($id = $this->ids['page']['home'] ?? null) && !isset($this->created[$id])) {
			$this->migrateEntry($id, ['credentials' => $c['pages']['home']['meta']['credentials']], ['credentials' => []], 'page:home', $conflicts, tag: 'creds');
		}
		setting_set('seed_migrations', $done + ['home-credentials-2026-10' => now()]);
	}

	/**
	 * Pessoas do time: cria, importa a foto e completa cargo e frase. Cada
	 * pessoa é publicada uma única vez (team:<slug> em seed_migrations), só
	 * com foto; quem for despublicado no painel depois não volta.
	 */
	private function team(array $people): void
	{
		$done = (array) setting('seed_migrations', []);
		$new  = [];
		foreach ($people as $slug => $p) {
			$id   = $this->ensure('team', 'team:' . $slug, $p + ['slug' => $slug]);
			$meta = $p['meta'] ?? [];
			if (!empty($p['photo']['file']) && ($mid = $this->photo($p['photo']['key'] ?? 'photo:' . $slug, $p['photo']))) {
				$meta['details']['photo'] = $mid;
			}
			$this->complete($id, $meta);
			if (empty($done['team:' . $slug]) && !empty($meta['details']['photo'])) {
				if (entry_raw($id)['published'] === null) {
					entry_publish($id);
				}
				$new['team:' . $slug] = now();
			}
		}
		if ($new) {
			setting_set('seed_migrations', $done + $new);
			$this->log[] = 'Time publicado (' . count($new) . ').';
		}
	}

	/**
	 * Clientes da faixa de logos: cria, importa o logo e registra a
	 * autorização. Cada cliente é publicado uma única vez (client:<slug> em
	 * seed_migrations); quem for despublicado no painel depois não volta.
	 */
	private function clients(array $clients): void
	{
		$done = (array) setting('seed_migrations', []);
		$new  = [];
		foreach ($clients as $slug => $p) {
			$id  = $this->ensure('client', 'client:' . $slug, $p + ['slug' => $slug]);
			$mid = $this->photo('client-logo:' . $slug, ['file' => $p['logo'], 'alt' => $p['title']], 'Logo do cliente — exibição autorizada (confirmado pela Full em 24/10/2026)');
			if (!$mid) {
				continue;
			}
			$this->complete($id, ['details' => ['logo' => $mid, 'authorized' => true, 'authorization_note' => 'Autorização de exibição confirmada pela Full Connect Key em 24/10/2026.']]);
			if (empty($done['client:' . $slug])) {
				if (entry_raw($id)['published'] === null) {
					entry_publish($id);
				}
				$new['client:' . $slug] = now();
			}
		}
		if (!$new) {
			return;
		}
		setting_set('seed_migrations', $done + $new);
		$this->log[] = 'Clientes publicados na faixa de logos (' . count($new) . ').';
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
	 * editado no painel). Republica só se o registro estava publicado sem
	 * rascunho pendente. Usado para dados que chegaram depois
	 * da instalação, como os cargos dos sócios.
	 */
	private function complete(int $id, array $meta): void
	{
		if (!$id || !$meta) {
			return;
		}
		$row     = entry_raw($id);
		$clean   = ($row['status'] ?? '') === 'published'; // publicado e sem rascunho pendente
		$draft   = json_dec($row['draft'] ?? null);
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
			if ($clean) {
				entry_publish($id); // dado novo num registro já no ar (ex.: frase do time) vai junto
			}
			$this->log[] = 'Completado: ' . $row['title'] . ($clean ? ' (publicado)' : '');
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

	private function migrateEntry(int $id, array $new, array $old, string $key, array &$conflicts, ?string $title = null, ?string $old_title = null, ?string $slug = null, ?string $old_slug = null, ?int $order = null, ?int $old_order = null, string $tag = 'eco'): void
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
		revision_add($id, (string) $row['title'], (string) $row['slug'], (string) $row['draft'], substr('antes-' . $tag, 0, 20));
		entry_save_draft($id, $changes, substr('migracao-' . $tag, 0, 20)); // revisions.action: 20 caracteres
		if ($clean) {
			entry_publish($id);
		}
		$this->log[] = 'Migrado: ' . ($changes['title'] ?? $row['title']) . ($clean ? ' (publicado)' : ' (rascunho)');
	}

	/**
	 * Sprint de conteúdo da Home (24/10/2026): troca os textos aprovados pelo
	 * cliente uma única vez (home-copy-2026-10), com a mesma regra da migração
	 * do Ecossistema: campo vazio ou ainda igual ao texto anterior é trocado;
	 * edição feita no painel é preservada e registrada. O hero não é tocado.
	 */
	private function migrateHomeCopy(array $c): void
	{
		$done = (array) setting('seed_migrations', []);
		if (!empty($done['home-copy-2026-10'])) {
			return;
		}
		$talk  = ['label' => 'Vamos conversar', 'to' => 'page:contact'];
		$home  = $c['pages']['home']['meta'];
		$new   = ['essence' => ['intro' => $home['essence']['intro'], 'pillars' => $home['essence']['pillars']], 'ecosystem' => ['title' => $home['ecosystem']['title'], 'text' => $home['ecosystem']['text']], 'partners' => ['title' => $home['partners']['title'], 'text' => $home['partners']['text']], 'footer_cta' => ['text' => $home['footer_cta']['text'], 'cta' => $home['footer_cta']['cta']]];
		$old   = [
			'essence'    => [
				'intro'   => 'Acreditamos que grandes resultados não acontecem de maneira isolada. Eles surgem quando ideias, pessoas, estratégias e tecnologias trabalham em sintonia. É assim que enxergamos cada negócio: um universo de possibilidades que ganha força quando suas conexões são compreendidas, aprimoradas e potencializadas.',
				'pillars' => [
					['name' => 'Conecta', 'text' => 'Aproximamos marcas, pessoas, ideias e oportunidades. Porque entender as relações é o primeiro passo para transformá-las.'],
					['name' => 'Organiza', 'text' => 'Integramos conhecimento, processos e estratégias para que cada ação tenha propósito e cada decisão tenha direção.'],
					['name' => 'Multiplica', 'text' => 'Testamos, aprendemos e aprimoramos continuamente para transformar boas conexões em resultados cada vez mais relevantes.'],
				],
			],
			'ecosystem'  => ['title' => 'Quem se conecta, evolui.', 'text' => 'Cinco plataformas proprietárias, cada uma resolvendo um desafio específico. Juntas, conectam diferentes etapas do crescimento do seu negócio.'],
			'partners'   => ['title' => 'Visões diferentes. Um propósito conectado.', 'text' => 'À frente da Full, diferentes experiências e perspectivas se encontram para construir uma visão integrada de comunicação, marketing e negócios.'],
			'footer_cta' => ['text' => 'Seu próximo desafio pode ser o ponto de partida para algo extraordinário. Vamos descobrir juntos?', 'cta' => $talk],
		];
		$conflicts = [];
		if (($id = $this->ids['page']['home'] ?? null) && !isset($this->created[$id])) {
			$this->migrateEntry($id, $this->links($new), $this->links($old), 'page:home', $conflicts, tag: 'home-copy');
		}
		$lines = [
			'mix'   => 'Inteligência estratégica para descobrir onde e como investir.',
			'dash'  => 'Gestão e performance para fazer cada investimento em mídia render melhor.',
			'leads' => 'Atração, nutrição e qualificação de oportunidades para o time comercial.',
			'match' => 'Inteligência de relacionamento para personalizar ofertas, estimular novas compras e reativar clientes.',
			'hub'   => 'Gestão integrada para conectar agência, clientes, equipes e entregas em um só lugar.',
		];
		foreach ($lines as $key => $was) {
			$id = $this->ids['platform'][$key] ?? null;
			if ($id && !isset($this->created[$id])) {
				$this->migrateEntry($id, ['identity' => ['home_line' => $c['platforms'][$key]['meta']['identity']['home_line']]], ['identity' => ['home_line' => $was]], "platform:$key", $conflicts, tag: 'home-copy');
			}
		}
		$sectors = [
			'varejo'           => 'Do posicionamento à experiência de compra, conectamos comunicação, pontos de contato e estratégias comerciais para aproximar marcas e consumidores.',
			'imobiliario'      => 'Da construção de uma marca imobiliária à geração de oportunidades comerciais, criamos estratégias que valorizam empreendimentos e acompanham diferentes momentos da jornada de compra.',
			'feiras-e-eventos' => 'Conectamos estratégia, comunicação e experiência para atrair públicos, fortalecer marcas e ampliar as oportunidades de negócio antes, durante e depois de cada evento.',
		];
		foreach ($sectors as $slug => $was) {
			$id = $this->find('sector:' . $slug);
			if ($id && !isset($this->created[$id])) {
				$this->migrateEntry($id, ['details' => ['text' => $c['sectors'][$slug]['meta']['details']['text']]], ['details' => ['text' => $was]], "sector:$slug", $conflicts, tag: 'home-copy');
			}
		}
		setting_set('seed_migrations', $done + ['home-copy-2026-10' => now()]);
		$this->log[] = 'Textos da Home atualizados' . ($conflicts ? ' (' . count($conflicts) . ' edição(ões) do painel preservada(s): ' . implode(', ', $conflicts) . ')' : '') . '.';
	}

	/**
	 * Cadastra o vídeo de apresentação de cada plataforma uma única vez
	 * (video:<plataforma>). Se o campo já tiver outro vídeo escolhido no
	 * painel, ele é mantido.
	 */
	private function platformVideosOnce(array $videos): void
	{
		$done      = (array) setting('seed_migrations', []);
		$new       = [];
		$conflicts = [];
		foreach ($videos as $key => $def) {
			$id = $this->ids['platform'][$key] ?? null;
			if (!empty($done['video:' . $key]) || !$id) {
				continue;
			}
			$mid = $this->photo('video:' . $key, $def, 'Vídeo enviado pela Full Connect Key (28/10/2026)');
			if (!$mid) {
				continue;
			}
			$this->migrateEntry($id, ['identity' => ['video' => $mid]], ['identity' => ['video' => null]], "platform:$key", $conflicts, tag: 'video');
			$new['video:' . $key] = now();
		}
		if ($new) {
			setting_set('seed_migrations', $done + $new);
			$this->log[] = 'Vídeos das plataformas cadastrados (' . count($new) . ').';
		}
	}

	/** Troca a assinatura institucional (título alternado do hero) uma única vez, se ainda for a anterior. */
	private function signatureOnce(string $old, string $new, string $key): void
	{
		$done = (array) setting('seed_migrations', []);
		$id   = $this->find('global:site');
		if (!empty($done[$key]) || !$id) {
			return;
		}
		$row   = entry_raw($id);
		$draft = json_dec($row['draft']);
		if (($draft['brand']['signature'] ?? '') === $old || empty($draft['brand']['signature'])) {
			$clean                       = $row['status'] === 'published';
			$draft['brand']['signature'] = $new;
			entry_save_draft($id, ['draft' => json_enc($draft)], 'seed');
			if ($clean) {
				entry_publish($id);
			}
			$this->log[] = 'Assinatura atualizada.';
		}
		setting_set('seed_migrations', $done + [$key => now()]);
	}

	/**
	 * Botão do cabeçalho: "Vamos conversar" vira "Conheça o ecossistema" (abre o
	 * vídeo do Ecossistema), uma única vez e só se ainda estiver como o seed antigo.
	 */
	private function headerCtaOnce(array $cta): void
	{
		$done = (array) setting('seed_migrations', []);
		$id   = $this->find('global:site');
		if (!empty($done['header-cta-eco-2026-10']) || !$id) {
			return;
		}
		$row   = entry_raw($id);
		$draft = json_dec($row['draft']);
		$cur   = $draft['menu']['cta'] ?? [];
		if (($cur['label'] ?? '') === 'Vamos conversar' || empty($cur['label'])) {
			$clean                 = $row['status'] === 'published';
			$draft['menu']['cta'] = $this->link($cta['label'], $cta['to']);
			entry_save_draft($id, ['draft' => json_enc($draft)], 'seed');
			if ($clean) {
				entry_publish($id);
			}
			$this->log[] = 'Cabeçalho: botão "' . $cta['label'] . '".';
		}
		setting_set('seed_migrations', $done + ['header-cta-eco-2026-10' => now()]);
	}

	/** Vídeo da visão geral do Ecossistema, uma única vez (video:ecosystem). */
	private function ecosystemVideoOnce(array $def): void
	{
		$done = (array) setting('seed_migrations', []);
		$id   = $this->ids['page']['ecosystem'] ?? null;
		if (!$def || !empty($done['video:ecosystem']) || !$id) {
			return;
		}
		$mid = $this->photo('video:ecosystem', $def, 'Vídeo enviado pela Full Connect Key (28/10/2026)');
		if (!$mid) {
			return;
		}
		$conflicts = [];
		$this->migrateEntry($id, ['presentation' => ['video' => $mid]], ['presentation' => ['video' => null]], 'page:ecosystem', $conflicts, tag: 'video');
		setting_set('seed_migrations', $done + ['video:ecosystem' => now()]);
		$this->log[] = 'Vídeo do Ecossistema cadastrado.';
	}

	/**
	 * Oculta páginas a pedido do cliente (28/10/2026: Cases, Insights e Full na
	 * Mídia), uma única vez: despublica. Itens de menu que apontam para elas
	 * somem sozinhos; republicar no painel traz tudo de volta.
	 */
	private function hidePagesOnce(array $pages, string $key): void
	{
		$done = (array) setting('seed_migrations', []);
		if (!empty($done[$key])) {
			return;
		}
		foreach ($pages as $page) {
			$id = $this->ids['page'][$page] ?? null;
			if ($id && entry_raw($id)['published'] !== null) {
				entry_unpublish($id);
				$this->log[] = 'Página ocultada: ' . $page . '.';
			}
		}
		setting_set('seed_migrations', $done + [$key => now()]);
	}

	/**
	 * Tira clientes da faixa de logos a pedido do cliente: despublica uma única
	 * vez (fica como rascunho no painel e pode voltar se for republicado).
	 */
	private function removeClientsOnce(array $clients): void
	{
		$done = (array) setting('seed_migrations', []);
		$new  = [];
		foreach ($clients as $slug => $key) {
			if (!empty($done[$key])) {
				continue;
			}
			$id = $this->find('client:' . $slug);
			if ($id && entry_raw($id)['published'] !== null) {
				entry_unpublish($id);
				$this->log[] = 'Faixa de logos: cliente "' . $slug . '" despublicado.';
			}
			$new[$key] = now();
		}
		if ($new) {
			setting_set('seed_migrations', $done + $new);
		}
	}

	/** Novo título do hero da Home (pedido do cliente, 24/10/2026), uma única vez. */
	private function migrateHomeHeadlineOnce(array $c): void
	{
		$done = (array) setting('seed_migrations', []);
		if (!empty($done['home-headline-2026-10'])) {
			return;
		}
		$conflicts = [];
		if (($id = $this->ids['page']['home'] ?? null) && !isset($this->created[$id])) {
			$this->migrateEntry($id, ['hero' => ['headline' => $c['pages']['home']['meta']['hero']['headline']]], ['hero' => ['headline' => 'O que se conecta, se multiplica.']], 'page:home', $conflicts, tag: 'hero');
		}
		setting_set('seed_migrations', $done + ['home-headline-2026-10' => now()]);
		$this->log[] = 'Home: título do hero atualizado' . ($conflicts ? ' (edição do painel preservada)' : '') . '.';
	}

	/** Novo título do bloco Problema do FullHub (pedido do cliente, 24/10/2026), uma única vez. */
	private function migrateHubProblemOnce(array $c): void
	{
		$done = (array) setting('seed_migrations', []);
		if (!empty($done['hub-problem-2026-10'])) {
			return;
		}
		$conflicts = [];
		if (($id = $this->ids['platform']['hub'] ?? null) && !isset($this->created[$id])) {
			$this->migrateEntry($id, ['problem' => ['title' => $c['platforms']['hub']['meta']['problem']['title']]], ['problem' => ['title' => 'Aprovação por WhatsApp não é processo. É sorte.']], 'platform:hub', $conflicts, tag: 'hub-problem');
		}
		setting_set('seed_migrations', $done + ['hub-problem-2026-10' => now()]);
		$this->log[] = 'FullHub: título do bloco Problema atualizado' . ($conflicts ? ' (edição do painel preservada)' : '') . '.';
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
	 * Remove do menu principal e do rodapé o item "Sócios" (/#socios), uma
	 * única vez (menu-socios-removido-2026-10). Os sócios ficam só como
	 * seção da Home (pedido do cliente, 24/10/2026; revoga o ADR-013).
	 */
	private function removePartnersMenuOnce(): void
	{
		$done = (array) setting('seed_migrations', []);
		$id   = $this->find('global:site');
		if (!empty($done['menu-socios-removido-2026-10']) || !$id) {
			return;
		}
		$row = entry_raw($id);
		if (str_contains((string) $row['draft'], '#socios')) {
			$draft = json_dec($row['draft']);
			$keep  = static fn($it) => !str_contains(json_enc($it['link'] ?? []), '#socios');
			$draft['menu']['main'] = array_values(array_filter((array) ($draft['menu']['main'] ?? []), $keep));
			foreach ((array) ($draft['menu']['footer_groups'] ?? []) as $g => $group) {
				$draft['menu']['footer_groups'][$g]['links'] = array_values(array_filter((array) ($group['links'] ?? []), $keep));
			}
			$clean = $row['status'] === 'published';
			entry_save_draft($id, ['draft' => json_enc($draft)], 'seed');
			if ($clean) {
				entry_publish($id);
			}
			$this->log[] = 'Menu: item "Sócios" removido.';
		}
		setting_set('seed_migrations', $done + ['menu-socios-removido-2026-10' => now()]);
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
