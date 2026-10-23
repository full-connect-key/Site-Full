<?php
/**
 * Sanitização de entradas: texto simples e HTML de texto rico (allowlist).
 */

declare(strict_types=1);

/** Texto de uma linha: sem tags, sem quebras, espaços normalizados. */
function clean_line(string $value): string
{
	$value = strip_tags($value);
	$value = (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
	$value = (string) preg_replace('/\s+/u', ' ', $value);
	return trim($value);
}

/** Texto multilinha: sem tags, preserva quebras de linha. */
function clean_multiline(string $value): string
{
	$value = strip_tags($value);
	$value = str_replace(["\r\n", "\r"], "\n", $value);
	$value = (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
	$value = (string) preg_replace("/[ \t]+/u", ' ', $value);
	$value = (string) preg_replace("/\n{3,}/", "\n\n", $value);
	return trim($value);
}

/** Domínios aceitos em vídeos incorporados. */
function allowed_embed(string $src): bool
{
	return (bool) preg_match('#^https://(www\.youtube-nocookie\.com/embed/|www\.youtube\.com/embed/|player\.vimeo\.com/video/)[\w-]+#', $src);
}

/**
 * HTML de texto rico com lista de permissões. Remove scripts, estilos,
 * eventos e atributos desconhecidos; links externos ganham rel seguro.
 */
function html_sanitize(string $html): string
{
	$html = trim($html);
	if ($html === '') {
		return '';
	}
	$allowed = [
		'p' => [], 'br' => [], 'h2' => [], 'h3' => [], 'h4' => [], 'strong' => [], 'b' => [], 'em' => [], 'i' => [],
		'u' => [], 's' => [], 'a' => ['href'], 'ul' => [], 'ol' => [], 'li' => [], 'blockquote' => [], 'hr' => [],
		'figure' => [], 'figcaption' => [], 'img' => ['src', 'alt', 'width', 'height'], 'iframe' => ['src', 'title'],
		'code' => [], 'pre' => [], 'cite' => [],
	];

	$doc = new DOMDocument('1.0', 'UTF-8');
	libxml_use_internal_errors(true);
	$doc->loadHTML('<?xml encoding="UTF-8"><div id="fck-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
	libxml_clear_errors();
	$root = $doc->getElementById('fck-root');
	if (!$root) {
		return '';
	}

	$walk = static function (DOMNode $node) use (&$walk, $allowed, $doc): void {
		foreach (iterator_to_array($node->childNodes) as $child) {
			if ($child instanceof DOMComment) {
				$node->removeChild($child);
				continue;
			}
			if (!$child instanceof DOMElement) {
				continue;
			}
			$tag = strtolower($child->tagName);
			if (in_array($tag, ['script', 'style', 'object', 'embed', 'form', 'input', 'button', 'svg', 'math', 'template'], true)) {
				$node->removeChild($child);
				continue;
			}
			if (!isset($allowed[$tag])) {
				// Desembrulha: mantém o conteúdo, remove a tag.
				$walk($child);
				while ($child->firstChild) {
					$node->insertBefore($child->firstChild, $child);
				}
				$node->removeChild($child);
				continue;
			}
			foreach (iterator_to_array($child->attributes) as $attr) {
				if (!in_array(strtolower($attr->name), $allowed[$tag], true)) {
					$child->removeAttribute($attr->name);
				}
			}
			if ($tag === 'a') {
				$href = trim($child->getAttribute('href'));
				if (!preg_match('#^(https?://|mailto:|tel:|/|\#)#i', $href)) {
					$child->removeAttribute('href');
				} elseif (is_external_url($href)) {
					$child->setAttribute('target', '_blank');
					$child->setAttribute('rel', 'noopener noreferrer');
				}
			}
			if ($tag === 'img') {
				$src = trim($child->getAttribute('src'));
				if (!preg_match('#^(/' . uploads_rel() . '/|' . preg_quote(rtrim(base_url(), '/'), '#') . '/' . uploads_rel() . '/)#', $src)) {
					$node->removeChild($child);
					continue;
				}
				$child->setAttribute('loading', 'lazy');
				$child->setAttribute('decoding', 'async');
			}
			if ($tag === 'iframe') {
				$src = str_replace('www.youtube.com/embed/', 'www.youtube-nocookie.com/embed/', trim($child->getAttribute('src')));
				if (!allowed_embed($src)) {
					$node->removeChild($child);
					continue;
				}
				$child->setAttribute('src', $src);
				$child->setAttribute('loading', 'lazy');
				$child->setAttribute('allowfullscreen', '');
				$child->setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
				if (!$child->getAttribute('title')) {
					$child->setAttribute('title', 'Vídeo');
				}
			}
			$walk($child);
		}
	};
	$walk($root);

	$out = '';
	foreach ($root->childNodes as $child) {
		$out .= $doc->saveHTML($child);
	}
	// Remove parágrafos vazios gerados pelo editor.
	return trim((string) preg_replace('#<p>(\s|<br>|&nbsp;)*</p>#u', '', $out));
}
