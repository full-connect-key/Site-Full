<?php
/**
 * Utilitários do painel: layout, cabeçalhos e rótulos.
 */

declare(strict_types=1);

function send_admin_headers(): void
{
	header('Content-Type: text/html; charset=utf-8');
	header('X-Robots-Tag: noindex, nofollow');
	header('X-Frame-Options: SAMEORIGIN');
	header('X-Content-Type-Options: nosniff');
	header('Referrer-Policy: same-origin');
	header('Cache-Control: no-store');
	header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob:; media-src 'self'; frame-src 'self' https://www.youtube-nocookie.com https://player.vimeo.com; form-action 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'self'");
}

/** Renderiza uma tela do painel dentro do layout (ou sozinha). */
function admin_view(string $name, array $vars = [], bool $layout = true): void
{
	extract($vars, EXTR_SKIP);
	ob_start();
	require FCK_APP . '/admin/views/' . $name . '.php';
	$content = (string) ob_get_clean();
	$page_title = $vars['title'] ?? '';
	require FCK_APP . '/admin/views/' . ($layout ? 'layout' : 'bare') . '.php';
}

/** Registros com endereço/slug editável. */
function entry_has_slug(array $row): bool
{
	if ($row['type'] === 'page') {
		return $row['template'] !== 'home';
	}
	return in_array($row['type'], ['platform', 'case', 'article', 'sector', 'category'], true);
}

function status_label(string $status): string
{
	return ['draft' => 'Rascunho', 'published' => 'Publicado', 'changed' => 'Publicado · alterações não publicadas'][$status] ?? $status;
}

function action_label(string $action): string
{
	return ['save' => 'Rascunho salvo', 'publish' => 'Publicado', 'unpublish' => 'Despublicado', 'restore' => 'Versão restaurada', 'seed' => 'Conteúdo inicial', 'create' => 'Criado'][$action] ?? $action;
}

/** Menu lateral agrupado. */
function admin_nav(): array
{
	$groups = [];
	foreach (content_types() as $type => $t) {
		if ($type === 'global') {
			continue;
		}
		$groups[$t['group']][] = ['href' => "/admin/entries/$type", 'label' => $t[1]];
	}
	$groups['Mídia'][]         = ['href' => '/admin/media', 'label' => 'Biblioteca de mídia'];
	$groups['Configurações'][] = ['href' => '/admin/settings/site', 'label' => 'Marca, menus, rodapé e SEO'];
	$groups['Configurações'][] = ['href' => '/admin/settings/ui', 'label' => 'Textos de interface'];
	$groups['Configurações'][] = ['href' => '/admin/redirects', 'label' => 'Redirecionamentos'];
	if (is_admin()) {
		$groups['Configurações'][] = ['href' => '/admin/submissions', 'label' => 'Mensagens de contato'];
		$groups['Configurações'][] = ['href' => '/admin/contact-settings', 'label' => 'Formulário: destinatários'];
		$groups['Configurações'][] = ['href' => '/admin/users', 'label' => 'Usuários'];
		$groups['Configurações'][] = ['href' => '/admin/tools', 'label' => 'Ferramentas'];
	}
	$order = ['Home', 'Páginas', 'Ecossistema', 'Cases', 'Insights', 'Imprensa', 'Pessoas', 'Mídia', 'Configurações'];
	uksort($groups, fn($a, $b) => array_search($a, $order, true) <=> array_search($b, $order, true));
	return $groups;
}
