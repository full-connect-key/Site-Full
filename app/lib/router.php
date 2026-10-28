<?php
/**
 * Roteamento: site público, painel (/admin), formulário e arquivos SEO.
 */

declare(strict_types=1);

function fck_dispatch(): void
{
	$path   = request_path();
	$method = request_method();

	if ($path === '/admin' || str_starts_with($path, '/admin/')) {
		require_once FCK_APP . '/admin/admin.php';
		admin_dispatch($path, $method);
		return;
	}

	if (!site_installed()) {
		http_response_code(503);
		header('Retry-After: 3600');
		echo '<!doctype html><meta charset="utf-8"><meta name="robots" content="noindex"><title>Em instalação</title><p style="font-family:sans-serif;padding:2rem">Site em instalação. Acesse <a href="/admin/install">/admin/install</a>.</p>';
		return;
	}
	site_auto_update();
	session_resume(); // preview: identifica o usuário do painel

	match (true) {
		$path === '/robots.txt'                        => render_robots(),
		$path === '/sitemap.xml'                       => render_sitemap(),
		$path === '/contato/enviar' && $method === 'POST' => contact_handle(),
		default                                        => route_public($path),
	};
}

/**
 * Depois de um deploy, a primeira visita aplica as migrations e o conteúdo
 * inicial novo (seed idempotente: nunca sobrescreve edições do painel), sem
 * precisar entrar no /admin. Uma trava no MySQL evita execuções simultâneas;
 * qualquer erro fica no log e não derruba a página.
 */
function site_auto_update(): void
{
	try {
		if (!migrations_pending() && setting('seed_version') === seed_version()) {
			return;
		}
		if ((int) db_value("SELECT GET_LOCK('fck_auto_update', 0)") !== 1) {
			return;
		}
		try {
			if (migrations_pending()) {
				migrate();
			}
			$current = db_value("SELECT value FROM settings WHERE name = 'seed_version'");
			if ($current === null || json_decode((string) $current, true) !== seed_version()) {
				run_seed();
				setting_set('seed_version', seed_version());
			}
		} finally {
			db_value("SELECT RELEASE_LOCK('fck_auto_update')");
		}
	} catch (Throwable $e) {
		error_log('[fck] atualização automática: ' . $e->getMessage());
	}
}

/** Instalado = banco acessível e tabelas criadas. */
function site_installed(): bool
{
	static $ok = null;
	if ($ok === null) {
		try {
			$ok = db_value("SELECT COUNT(*) FROM migrations") !== null && !migrations_pending();
		} catch (Throwable) {
			$ok = false;
		}
	}
	return $ok;
}

function route_public(string $path): void
{
	$segments = $path === '/' ? [] : explode('/', trim($path, '/'));

	if (!$segments) {
		$home = page_by_template('home');
		$home ? render_entry($home) : not_found();
		return;
	}
	if (count($segments) === 1) {
		$page = entry_by_slug('page', $segments[0]);
		if ($page && $page['template'] !== 'home' && (!entry_video($page) || is_preview())) {
			render_entry($page);
			return;
		}
	}
	if (count($segments) === 2) {
		foreach (['platform', 'case', 'article'] as $type) {
			if ($segments[0] === route_base($type)) {
				$item = entry_by_slug($type, $segments[1]);
				if ($item && (!entry_video($item) || is_preview())) {
					render_entry($item);
					return;
				}
			}
		}
	}
	redirect_lookup($path);
	not_found();
}

/** Renderiza um registro público pelo tipo/modelo. */
function render_entry(array $entry): void
{
	switch ($entry['type']) {
		case 'page':
			$tpl  = $entry['template'] ?: 'legal';
			$hero = g($entry, 'hero');
			render_page('pages/' . $tpl, ['entry' => $entry, 'body_class' => 'tpl-' . $tpl], [
				'entry' => $entry,
				'title' => $tpl === 'home' ? '' : $entry['title'],
				'description' => (string) ($hero['lead'] ?? ''),
				'path'  => path_for($entry),
				'home'  => $tpl === 'home',
			]);
			return;
		case 'platform':
		case 'case':
		case 'article':
			$base  = page_by_template(['platform' => 'ecosystem', 'case' => 'cases', 'article' => 'insights'][$entry['type']]);
			$crumb = [[(string) (site('brand')['site_name'] ?? ''), url('/')]];
			if ($base) {
				$crumb[] = [$base['title'], url_for($base)];
			}
			$crumb[] = [$entry['title'], url_for($entry)];
			$details = g($entry, $entry['type'] === 'platform' ? 'identity' : 'details');
			render_page('pages/' . $entry['type'], ['entry' => $entry, 'base' => $base, 'body_class' => 'single-' . $entry['type']], [
				'entry'       => $entry,
				'title'       => $entry['title'],
				'description' => (string) ($entry['type'] === 'platform' ? (g($entry, 'hero')['lead'] ?? '') : ($details['summary'] ?? $details['excerpt'] ?? '')),
				'image'       => (int) ($details['kv'] ?? $details['cover'] ?? 0),
				'type'        => $entry['type'] === 'article' ? 'article' : 'website',
				'path'        => path_for($entry),
				'breadcrumb'  => $crumb,
				'published'   => $entry['type'] === 'article' ? (($details['date'] ?? '') ?: $entry['published_at']) : null,
				'author'      => $entry['type'] === 'article' && !empty($details['author']) ? (entry_find((int) $details['author'])['title'] ?? null) : null,
			]);
			return;
	}
	not_found();
}

function not_found(): void
{
	http_response_code(404);
	render_page('pages/404', ['body_class' => 'error-404'], ['title' => ui('notfound', 'title'), 'noindex' => true]);
}
