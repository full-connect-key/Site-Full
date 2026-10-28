<?php
/**
 * Renderização de templates (app/views) e peças reutilizáveis.
 * As peças não contêm texto editorial: rótulos vêm do painel.
 */

declare(strict_types=1);

/** Renderiza um template com variáveis. */
function view(string $name, array $vars = []): void
{
	$file = FCK_APP . '/views/' . $name . '.php';
	if (!is_file($file)) {
		throw new RuntimeException("View não encontrada: $name");
	}
	extract($vars, EXTR_SKIP);
	require $file;
}

function view_capture(string $name, array $vars = []): string
{
	ob_start();
	view($name, $vars);
	return (string) ob_get_clean();
}

/** Página completa: cabeçalho + conteúdo + rodapé. */
function render_page(string $name, array $vars, array $seo): void
{
	send_security_headers();
	$content = view_capture($name, $vars);
	view('layout', ['content' => $content, 'seo' => seo_context($seo), 'body_class' => $vars['body_class'] ?? '']);
}

function send_security_headers(): void
{
	if (headers_sent()) {
		return;
	}
	header('Content-Type: text/html; charset=utf-8');
	header('X-Content-Type-Options: nosniff');
	header('Referrer-Policy: strict-origin-when-cross-origin');
	header('X-Frame-Options: SAMEORIGIN');
	header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
	if (fck_is_production() && str_starts_with(base_url(), 'https://')) {
		header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
	}
	header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-" . csp_nonce() . "'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob:; media-src 'self'; font-src 'self'; connect-src 'self'; frame-src https://www.youtube-nocookie.com https://player.vimeo.com; form-action 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'self'");
	if (is_preview()) {
		header('Cache-Control: no-store');
	}
}

/** Ícones SVG decorativos. */
function icon(string $name, string $class = ''): string
{
	$paths = [
		'arrow'      => '<path d="M4 12h15M13 6l6 6-6 6"/>',
		'arrow-left' => '<path d="M20 12H5M11 6l-6 6 6 6"/>',
		'arrow-up'   => '<path d="M12 20V5M6 11l6-6 6 6"/>',
		'external'   => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
		'chevron'    => '<path d="M6 9l6 6 6-6"/>',
		'close'      => '<path d="M5 5l14 14M19 5L5 19"/>',
		'pause'      => '<path d="M9 5v14M15 5v14"/>',
		'play'       => '<path d="M7 5l12 7-12 7z"/>',
		'plus'       => '<path d="M12 5v14M5 12h14"/>',
		'link'       => '<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/>',
		'share'      => '<path d="M12 4v11M7 9l5-5 5 5M5 14v5h14v-5"/>',
	];
	return isset($paths[$name])
		? '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[$name] . '</svg>'
		: '';
}

function link_attrs(array $link): string
{
	return $link['external'] ? ' target="_blank" rel="noopener noreferrer"' : '';
}

function external_notice(array $link): string
{
	$n = ui('a11y', 'external_link');
	return $link['external'] && $n !== '' ? '<span class="screen-reader-text"> ' . e($n) . '</span>' : '';
}

/** Botão-link a partir de um link do painel (sem link válido, nada). */
function button(mixed $link, string $variant = 'primary', array $opts = []): void
{
	$l = is_array($link) && isset($link['href']) ? $link : link_of($link);
	if (!$l) {
		return;
	}
	// Link para vídeo (ADR-022): sempre com o ícone de play, que avisa que abre um vídeo.
	$video = (bool) preg_match('/\.(mp4|webm)$/i', (string) parse_url($l['href'], PHP_URL_PATH));
	printf(
		'<a class="btn btn--%s %s" href="%s"%s><span>%s</span>%s%s</a>',
		e($variant), e(trim(($opts['class'] ?? '') . ($video ? ' btn--video' : ''))), e($l['href']), link_attrs($l), e($l['label']),
		$video ? icon('play', 'btn__arrow') : (($opts['arrow'] ?? true) ? icon($l['external'] ? 'external' : 'arrow', 'btn__arrow') : ''),
		external_notice($l)
	);
}

/** Logo oficial se cadastrado; senão o nome em texto (nunca logo inventado). */
function wordmark(string $name, int $logo = 0, string $class = ''): string
{
	if ($logo && media_ok($logo)) {
		return '<span class="wordmark wordmark--logo ' . e($class) . '">' . media_html($logo, ['class' => 'wordmark__img', 'alt' => $name, 'loading' => 'eager']) . '</span>';
	}
	return '<span class="wordmark ' . e($class) . '">' . e($name) . '</span>';
}

/** Texto do painel dentro de uma tag (nada se vazio). */
function text_el(mixed $value, string $tag = 'p', string $class = '', array $attrs = []): void
{
	if (!has_text($value)) {
		return;
	}
	$extra = '';
	foreach ($attrs as $k => $v) {
		$extra .= ' ' . e($k) . '="' . e((string) $v) . '"';
	}
	$tag = preg_replace('/[^a-z0-9]/', '', $tag) ?: 'p';
	printf('<%1$s class="%2$s"%3$s>%4$s</%1$s>', $tag, e($class), $extra, nl2br(e($value), false));
}

function section_head(array $a): void
{
	$title = $a['title'] ?? '';
	$text  = $a['text'] ?? '';
	if (!has_text($title) && !has_text($text)) {
		return;
	}
	$level = (int) ($a['level'] ?? 2);
	echo '<div class="section-head ' . e($a['class'] ?? '') . '">';
	if (has_text($a['eyebrow'] ?? '')) {
		echo '<p class="eyebrow">' . e($a['eyebrow']) . '</p>';
	}
	if (has_text($title)) {
		printf('<h%1$d%2$s class="%3$s section-head__title">%4$s</h%1$d>', $level, !empty($a['id']) ? ' id="' . e($a['id']) . '"' : '', e($a['size'] ?? 'display-m'), e($title));
	}
	if (has_text($text)) {
		echo '<p class="lead section-head__text">' . nl2br(e($text), false) . '</p>';
	}
	echo '</div>';
}

function cta_band(array $cta, string $id = 'cta'): void
{
	if (!section_enabled($cta) || !has_text($cta['title'] ?? '')) {
		return;
	}
	view('parts/cta-band', ['args' => ['cta' => $cta, 'id' => $id]]);
}

/** Árvore de menu a partir do grupo "Menus" (links não publicados somem). */
function menu_tree(array $items): array
{
	$current = rtrim(url(request_path()), '/');
	$tree    = [];
	foreach ($items as $it) {
		$l        = link_of($it['link'] ?? null);
		$children = [];
		foreach ((array) ($it['children'] ?? []) as $c) {
			$cl = link_of($c['link'] ?? null);
			if ($cl) {
				$target            = (($c['link']['type'] ?? '') === 'post') ? entry_find((int) ($c['link']['post'] ?? 0)) : null;
				$cl['product']     = $target && $target['type'] === 'platform' ? (string) (g($target, 'identity')['product'] ?? '') : '';
				$cl['current']     = rtrim(strtok($cl['href'], '?'), '/') === $current;
				$children[]        = $cl;
			}
		}
		if (!$l) {
			// Destino oculto: com subitens, o item vira só um rótulo (ex.: Ecossistema Full com os vídeos).
			$label = trim((string) ($it['link']['label'] ?? ''));
			if (!$children || $label === '') {
				continue;
			}
			$l = ['href' => '', 'label' => $label, 'external' => false];
		}
		$href          = $l['href'] === '' ? '' : rtrim((string) strtok($l['href'], '?'), '/');
		$l['current']  = $href !== '' && $href === $current;
		$l['ancestor'] = $href !== '' && $href !== rtrim(url('/'), '/') && str_starts_with($current . '/', $href . '/');
		$l['children'] = $children;
		$tree[]        = $l;
	}
	return $tree;
}

function render_error_page(): void
{
	fck_render_error_page();
}

/** Erro temporário (500) com textos do painel quando o banco responde. */
function fck_render_error_page(): void
{
	$title = $text = $retry = '';
	try {
		$title = ui('error', 'title');
		$text  = ui('error', 'text');
		$retry = ui('error', 'retry');
	} catch (Throwable) {
		// Sem banco: página mínima.
	}
	echo '<!doctype html><html lang="pt-BR" data-theme="hero"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>' . e($title) . '</title><link rel="stylesheet" href="' . e(asset('css/main.css')) . '"></head><body><main class="error-page" data-theme="hero"><div class="container"><h1 class="display-l">' . e($title) . '</h1><p class="lead">' . e($text) . '</p>' . ($retry !== '' ? '<p><a class="btn btn--primary" href="">' . e($retry) . '</a></p>' : '') . '</div></main></body></html>';
}
