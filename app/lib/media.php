<?php
/**
 * Biblioteca de mídia: upload validado, tamanhos gerados (WebP quando o
 * servidor suporta), renderização responsiva de imagens, SVG e vídeo.
 */

declare(strict_types=1);

const MEDIA_SIZES = ['thumb' => 480, 'card' => 960, 'wide' => 1600, 'full' => 2400];
const MEDIA_MAX_BYTES = 25 * 1024 * 1024;

function media_allowed_mimes(): array
{
	return [
		'image/jpeg'    => 'jpg',
		'image/png'     => 'png',
		'image/webp'    => 'webp',
		'image/gif'     => 'gif',
		'image/svg+xml' => 'svg',
		'video/mp4'     => 'mp4',
		'video/webm'    => 'webm',
		'application/pdf' => 'pdf',
	];
}

/** Pasta de uploads (relativa à raiz). Testes usam "uploads-test". */
function uploads_rel(): string
{
	$v = getenv('FCK_UPLOADS');
	return $v && preg_match('/^[a-z0-9-]+$/', $v) ? $v : 'uploads';
}

function uploads_dir(): string
{
	return FCK_ROOT . '/' . uploads_rel();
}

function media_get(int $id): ?array
{
	static $cache = [];
	if (!$id) {
		return null;
	}
	return $cache[$id] ??= db_one('SELECT * FROM media WHERE id = ?', [$id]);
}

function media_ok(mixed $id): bool
{
	return (int) $id > 0 && media_get((int) $id) !== null;
}

function media_is_svg(array $m): bool
{
	return $m['mime'] === 'image/svg+xml';
}

function media_is_video(array $m): bool
{
	return str_starts_with((string) $m['mime'], 'video/');
}

/** URL de um tamanho (cai no original se o tamanho não existir). */
function media_url(array $m, string $size = 'full'): string
{
	$sizes = json_dec($m['sizes'] ?? null);
	$path  = $sizes[$size]['path'] ?? $m['path'];
	return url(uploads_rel() . '/' . $path);
}

function media_alt(array $m): string
{
	return $m['decorative'] ? '' : (string) $m['alt'];
}

function media_focal(array $m): string
{
	return preg_match('/^\d{1,3}% \d{1,3}%$/', (string) $m['focal']) ? (string) $m['focal'] : '50% 50%';
}

/** Logos de terceiros e imprensa exigem direito de uso confirmado. */
function media_rights_ok(mixed $id): bool
{
	$m = media_get((int) $id);
	return $m !== null && (bool) $m['rights_confirmed'];
}

/**
 * <img>/<video> responsivo.
 * Opções: size, sizes, class, loading ('lazy'|'eager'), decorative, controls, alt
 */
function media_html(mixed $id, array $opts = []): string
{
	$m = media_get((int) $id);
	if (!$m) {
		return '';
	}
	$class   = (string) ($opts['class'] ?? '');
	$loading = (string) ($opts['loading'] ?? 'lazy');
	$alt     = array_key_exists('alt', $opts) ? (string) $opts['alt'] : (!empty($opts['decorative']) ? '' : media_alt($m));
	$style   = 'object-position:' . media_focal($m);

	if (media_is_video($m)) {
		$poster = $m['poster_id'] ? media_get((int) $m['poster_id']) : null;
		return sprintf(
			'<video class="%s" style="%s" src="%s"%s%s preload="none" playsinline%s></video>',
			e($class), e($style), e(url(uploads_rel() . '/' . $m['path'])),
			$poster ? ' poster="' . e(media_url($poster, 'wide')) . '"' : '',
			($opts['controls'] ?? true) ? ' controls' : '',
			$alt !== '' ? ' aria-label="' . e($alt) . '"' : ''
		);
	}

	$dims = $m['width'] && $m['height'] ? sprintf(' width="%d" height="%d"', $m['width'], $m['height']) : '';
	if (media_is_svg($m)) {
		return sprintf('<img src="%s" alt="%s" class="%s" loading="%s" decoding="async"%s>', e(url(uploads_rel() . '/' . $m['path'])), e($alt), e($class), e($loading), $dims);
	}

	$sizes  = json_dec($m['sizes'] ?? null);
	$srcset = [];
	foreach ($sizes as $name => $s) {
		if (isset(MEDIA_SIZES[$name]) && !empty($s['width'])) {
			$srcset[] = url(uploads_rel() . '/' . $s['path']) . ' ' . (int) $s['width'] . 'w';
		}
	}
	$main = media_url($m, (string) ($opts['size'] ?? 'wide'));
	return sprintf(
		'<img src="%s"%s%s alt="%s" class="%s" style="%s" loading="%s" decoding="async"%s%s>',
		e($main),
		$srcset ? ' srcset="' . e(implode(', ', $srcset)) . '"' : '',
		$srcset ? ' sizes="' . e((string) ($opts['sizes'] ?? '100vw')) . '"' : '',
		e($alt), e($class), e($style), e($loading),
		$loading === 'eager' ? ' fetchpriority="high"' : '',
		$dims
	);
}

/** Dimensões de um SVG (width/height ou viewBox). */
function svg_dimensions(string $file): array
{
	$xml = @simplexml_load_file($file);
	if (!$xml) {
		return [null, null];
	}
	$w = (float) $xml['width'];
	$h = (float) $xml['height'];
	if ((!$w || !$h) && isset($xml['viewBox'])) {
		$vb = preg_split('/[\s,]+/', trim((string) $xml['viewBox'])) ?: [];
		$w  = (float) ($vb[2] ?? 0);
		$h  = (float) ($vb[3] ?? 0);
	}
	return [$w ? (int) round($w) : null, $h ? (int) round($h) : null];
}

/** SVG seguro? (sem scripts, eventos, foreignObject ou referências externas) */
function svg_is_safe(string $content): bool
{
	return !preg_match('/<script|<foreignObject|\son\w+\s*=|javascript:|<!ENTITY|<!DOCTYPE|xlink:href\s*=\s*["\']\s*(?!#)|href\s*=\s*["\']\s*(?!#)/i', $content);
}

/**
 * Importa um arquivo (upload ou caminho local) para a biblioteca.
 * Retorna [id, erro].
 */
function media_import(string $source, string $original_name, array $meta = []): array
{
	if (!is_file($source)) {
		return [null, 'Arquivo não encontrado.'];
	}
	$size = (int) filesize($source);
	if ($size > MEDIA_MAX_BYTES) {
		return [null, 'Arquivo acima de 25 MB.'];
	}
	$finfo = new finfo(FILEINFO_MIME_TYPE);
	$mime  = (string) $finfo->file($source);
	if ($mime === 'text/xml' || $mime === 'application/xml' || ($mime === 'text/plain' && str_ends_with(strtolower($original_name), '.svg'))) {
		$mime = 'image/svg+xml';
	}
	$allowed = media_allowed_mimes();
	if (!isset($allowed[$mime])) {
		return [null, 'Tipo de arquivo não permitido (' . $mime . ').'];
	}
	if ($mime === 'image/svg+xml') {
		if (!svg_is_safe((string) file_get_contents($source))) {
			return [null, 'SVG recusado: contém scripts, eventos ou referências externas.'];
		}
	}

	$sub  = date('Y/m');
	$dir  = uploads_dir() . '/' . $sub;
	if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
		return [null, 'Não foi possível criar a pasta de uploads.'];
	}
	$base = slugify(pathinfo($original_name, PATHINFO_FILENAME)) ?: 'arquivo';
	$name = $base . '.' . $allowed[$mime];
	$i    = 2;
	while (is_file($dir . '/' . $name)) {
		$name = $base . '-' . $i++ . '.' . $allowed[$mime];
	}
	$dest = $dir . '/' . $name;
	$ok   = is_uploaded_file($source) ? move_uploaded_file($source, $dest) : copy($source, $dest);
	if (!$ok) {
		return [null, 'Falha ao gravar o arquivo.'];
	}
	@chmod($dest, 0644);

	[$w, $h] = [null, null];
	$sizes   = [];
	if ($mime === 'image/svg+xml') {
		[$w, $h] = svg_dimensions($dest);
	} elseif (str_starts_with($mime, 'image/')) {
		$info = @getimagesize($dest);
		if ($info) {
			[$w, $h] = [$info[0], $info[1]];
			$sizes   = media_generate_sizes($dest, $sub, $mime, (int) $w, (int) $h);
		}
	}

	$id = db_insert('media', [
		'path'             => $sub . '/' . $name,
		'original_name'    => mb_substr($original_name, 0, 255),
		'mime'             => $mime,
		'size'             => $size,
		'width'            => $w,
		'height'           => $h,
		'sizes'            => json_enc($sizes),
		'alt'              => (string) ($meta['alt'] ?? ''),
		'rights_confirmed' => !empty($meta['rights_confirmed']) ? 1 : 0,
		'license'          => (string) ($meta['license'] ?? ''),
		'seed_key'         => $meta['seed_key'] ?? null,
		'created_at'       => now(),
		'created_by'       => current_user()['id'] ?? null,
	]);
	return [$id, null];
}

/** Gera versões redimensionadas (WebP se disponível) com GD. */
function media_generate_sizes(string $file, string $sub, string $mime, int $w, int $h): array
{
	if (!function_exists('imagecreatetruecolor') || $mime === 'image/gif') {
		return [];
	}
	$src = match ($mime) {
		'image/jpeg' => @imagecreatefromjpeg($file),
		'image/png'  => @imagecreatefrompng($file),
		'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file) : false,
		default      => false,
	};
	if (!$src) {
		return [];
	}
	$webp  = function_exists('imagewebp');
	$ext   = $webp ? 'webp' : ($mime === 'image/png' ? 'png' : 'jpg');
	$base  = pathinfo($file, PATHINFO_FILENAME);
	$out   = [];
	$specs = MEDIA_SIZES + ['og' => [1200, 630]];
	foreach ($specs as $name => $spec) {
		if (is_array($spec)) {
			[$tw, $th] = $spec; // recorte centralizado
			$scale     = max($tw / $w, $th / $h);
			$cw        = (int) round($tw / $scale);
			$ch        = (int) round($th / $scale);
			$sx        = (int) (($w - $cw) / 2);
			$sy        = (int) (($h - $ch) / 2);
		} else {
			if ($w <= $spec && $name !== 'thumb') {
				continue;
			}
			$tw = min($spec, $w);
			$th = (int) round($h * $tw / $w);
			[$sx, $sy, $cw, $ch] = [0, 0, $w, $h];
		}
		$dst = imagecreatetruecolor($tw, $th);
		imagealphablending($dst, false);
		imagesavealpha($dst, true);
		imagecopyresampled($dst, $src, 0, 0, $sx, $sy, $tw, $th, $cw, $ch);
		$fname = "$base-$name.$ext";
		$path  = dirname($file) . '/' . $fname;
		$saved = match ($ext) {
			'webp' => imagewebp($dst, $path, 82),
			'png'  => imagepng($dst, $path, 8),
			default => imagejpeg($dst, $path, 82),
		};
		imagedestroy($dst);
		if ($saved) {
			$out[$name] = ['path' => "$sub/$fname", 'width' => $tw, 'height' => $th];
		}
	}
	imagedestroy($src);
	return $out;
}

function media_delete(int $id): void
{
	$m = media_get($id);
	if (!$m) {
		return;
	}
	$files = [$m['path']];
	foreach (json_dec($m['sizes']) as $s) {
		$files[] = $s['path'];
	}
	foreach ($files as $f) {
		$full = uploads_dir() . '/' . $f;
		if (is_file($full) && str_starts_with(realpath($full) ?: '', realpath(uploads_dir()) ?: '#')) {
			@unlink($full);
		}
	}
	db_delete('media', ['id' => $id]);
}
