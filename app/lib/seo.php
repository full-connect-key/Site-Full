<?php
/**
 * SEO: título, descrição, canonical, Open Graph/Twitter, noindex fora de
 * produção, dados estruturados (somente com dados reais), sitemap e robots.
 */

declare(strict_types=1);

function is_indexable(): bool
{
	return fck_is_production();
}

/**
 * Monta os dados de SEO de uma página.
 * $ctx: entry, title (fallback), description (fallback), image (fallback), type, path, breadcrumb
 */
function seo_context(array $ctx): array
{
	$entry    = $ctx['entry'] ?? null;
	$seo      = $entry ? g($entry, 'seo') : [];
	$defaults = site('seo_defaults');
	$brand    = (string) (site('brand')['site_name'] ?? '');

	$title = (string) ($seo['title'] ?? '');
	if ($title === '') {
		$fallback = (string) ($ctx['title'] ?? '');
		$tpl      = (string) ($defaults['title_template'] ?? '');
		$title    = $fallback === ''
			? (string) (($defaults['default_title'] ?? '') ?: $brand)
			: (str_contains($tpl, '%s') ? str_replace('%s', $fallback, $tpl) : $fallback);
	}
	return [
		'title'       => $title,
		'description' => (string) (($seo['description'] ?? '') ?: (($ctx['description'] ?? '') ?: ($defaults['default_description'] ?? ''))),
		'image'       => (int) (($seo['image'] ?? 0) ?: (($ctx['image'] ?? 0) ?: ($defaults['default_image'] ?? 0))),
		'noindex'     => !empty($seo['noindex']) || !empty($ctx['noindex']),
		'type'        => $ctx['type'] ?? 'website',
		'url'         => url($ctx['path'] ?? request_path()),
		'brand'       => $brand,
		'entry'       => $entry,
		'breadcrumb'  => $ctx['breadcrumb'] ?? [],
		'published'   => $ctx['published'] ?? null,
		'author'      => $ctx['author'] ?? null,
		'home'        => !empty($ctx['home']),
	];
}

function seo_head(array $s): void
{
	$robots = (!is_indexable() || $s['noindex']) ? 'noindex, nofollow' : 'index, follow, max-image-preview:large';
	echo '<title>' . e($s['title']) . "</title>\n";
	echo '<meta name="robots" content="' . e($robots) . "\">\n";
	if ($s['description'] !== '') {
		echo '<meta name="description" content="' . e($s['description']) . "\">\n";
	}
	echo '<link rel="canonical" href="' . e($s['url']) . "\">\n";
	echo '<meta property="og:type" content="' . e($s['type']) . "\">\n";
	echo '<meta property="og:locale" content="pt_BR">' . "\n";
	if ($s['brand'] !== '') {
		echo '<meta property="og:site_name" content="' . e($s['brand']) . "\">\n";
	}
	echo '<meta property="og:title" content="' . e($s['title']) . "\">\n";
	echo '<meta property="og:url" content="' . e($s['url']) . "\">\n";
	if ($s['description'] !== '') {
		echo '<meta property="og:description" content="' . e($s['description']) . "\">\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	if ($s['image'] && ($m = media_get($s['image']))) {
		$sizes = json_dec($m['sizes']);
		$og    = $sizes['og'] ?? null;
		echo '<meta property="og:image" content="' . e($og ? url(uploads_rel() . '/' . $og['path']) : url(uploads_rel() . '/' . $m['path'])) . "\">\n";
		if ($og) {
			echo '<meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">' . "\n";
		}
		if (media_alt($m) !== '') {
			echo '<meta property="og:image:alt" content="' . e(media_alt($m)) . "\">\n";
		}
	}
	if ($s['type'] === 'article' && $s['published']) {
		echo '<meta property="article:published_time" content="' . e(date('c', strtotime($s['published']))) . "\">\n";
	}
	foreach (structured_data($s) as $graph) {
		printf("<script type=\"application/ld+json\" nonce=\"%s\">%s</script>\n", e(csp_nonce()), json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG));
	}
}

function structured_data(array $s): array
{
	$out = [];
	if ($s['home']) {
		$org      = ['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => $s['brand'], 'url' => url('/')];
		$logo     = (int) (site('brand')['logo_light'] ?? 0);
		$contacts = site('contacts');
		if ($logo && ($m = media_get($logo))) {
			$org['logo'] = url(uploads_rel() . '/' . $m['path']);
		}
		$same = array_values(array_filter(array_map(fn($x) => $x['url'] ?? '', (array) ($contacts['socials'] ?? []))));
		if ($same) {
			$org['sameAs'] = $same;
		}
		if (!empty($contacts['email'])) {
			$org['email'] = $contacts['email'];
		}
		$out[] = $org;
		$out[] = ['@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => $s['brand'], 'url' => url('/'), 'inLanguage' => 'pt-BR'];
		return $out;
	}
	if ($s['breadcrumb']) {
		$out[] = [
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array_map(
				fn($c, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1]],
				$s['breadcrumb'],
				array_keys($s['breadcrumb'])
			),
		];
	}
	if ($s['type'] === 'article' && $s['entry']) {
		$a = [
			'@context'         => 'https://schema.org',
			'@type'            => 'Article',
			'headline'         => $s['entry']['title'],
			'mainEntityOfPage' => $s['url'],
			'inLanguage'       => 'pt-BR',
			'publisher'        => ['@type' => 'Organization', 'name' => $s['brand']],
		];
		if ($s['published']) {
			$a['datePublished'] = date('c', strtotime($s['published']));
		}
		if ($s['entry']['updated_at'] ?? null) {
			$a['dateModified'] = date('c', strtotime($s['entry']['updated_at']));
		}
		if ($s['author']) {
			$a['author'] = ['@type' => 'Person', 'name' => $s['author']];
		}
		if ($s['description'] !== '') {
			$a['description'] = $s['description'];
		}
		if ($s['image'] && ($m = media_get($s['image']))) {
			$a['image'] = media_url($m, 'wide');
		}
		$out[] = $a;
	}
	return $out;
}

/** /sitemap.xml — páginas, plataformas, cases e artigos publicados e indexáveis. */
function render_sitemap(): never
{
	header('Content-Type: application/xml; charset=utf-8');
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
	foreach (['page', 'platform', 'case', 'article'] as $type) {
		foreach (entries_of($type) as $entry) {
			if (!empty(g($entry, 'seo')['noindex'])) {
				continue;
			}
			printf("<url><loc>%s</loc><lastmod>%s</lastmod></url>\n", e(url_for($entry)), e(date('Y-m-d', strtotime((string) $entry['updated_at']))));
		}
	}
	echo '</urlset>';
	exit;
}

/** /robots.txt — produção indexável; demais ambientes bloqueados. */
function render_robots(): never
{
	header('Content-Type: text/plain; charset=utf-8');
	if (!is_indexable()) {
		echo "User-agent: *\nDisallow: /\n";
	} else {
		echo "User-agent: *\nDisallow: /admin\nSitemap: " . url('sitemap.xml') . "\n";
	}
	exit;
}
