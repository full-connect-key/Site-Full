<?php
/**
 * Utilitários gerais: escape, URLs, requisição/resposta.
 */

declare(strict_types=1);

/** Escapa texto para HTML. */
function e(mixed $value): string
{
	return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Imprime texto escapado. */
function ee(mixed $value): void
{
	echo e($value);
}

/** URL absoluta do site para um caminho interno. */
function url(string $path = '/'): string
{
	if (preg_match('#^(https?:|mailto:|tel:|\#)#', $path)) {
		return $path;
	}
	return rtrim(base_url(), '/') . '/' . ltrim($path, '/');
}

/** Endereço base: configuração ou detectado pela requisição. */
function base_url(): string
{
	static $base = null;
	if ($base === null) {
		$configured = (string) fck_config('url', '');
		if ($configured !== '') {
			$base = rtrim($configured, '/');
		} elseif (PHP_SAPI !== 'cli' && !empty($_SERVER['HTTP_HOST'])) {
			$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
			$base  = ($https ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
		} else {
			$base = 'http://localhost:8080';
		}
	}
	return $base;
}

/** URL de asset com versão (cache busting pelo mtime). */
function asset(string $path): string
{
	$file = FCK_ROOT . '/assets/' . ltrim($path, '/');
	$v    = is_file($file) ? (string) filemtime($file) : FCK_VERSION;
	return url('assets/' . ltrim($path, '/')) . '?v=' . $v;
}

/** Substitui {chave} em microtextos. */
function fill(string $template, array $values): string
{
	return (string) preg_replace_callback(
		'/\{(\w+)\}/',
		static fn(array $m): string => array_key_exists($m[1], $values) ? (string) $values[$m[1]] : $m[0],
		$template
	);
}

/** Texto presente e que não seja pendência "[PENDENTE...]". */
function has_text(mixed $value): bool
{
	return is_string($value) && trim($value) !== '' && !preg_match('/^\s*\[PENDENTE/i', $value);
}

function request_method(): string
{
	return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
}

/** Caminho da requisição normalizado ("/", "/cases", "/cases/x"). */
function request_path(): string
{
	$path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
	$path = '/' . trim(rawurldecode($path), '/');
	return $path === '/' ? '/' : rtrim($path, '/');
}

function input(string $key, mixed $default = null): mixed
{
	return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function query(string $key, string $default = ''): string
{
	$v = $_GET[$key] ?? $default;
	return is_string($v) ? $v : $default;
}

function wants_json(): bool
{
	return str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');
}

function json_response(array $data, int $status = 200): never
{
	http_response_code($status);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	exit;
}

function redirect_to(string $location, int $status = 302): never
{
	header('Location: ' . (preg_match('#^https?://#', $location) ? $location : url($location)), true, $status);
	exit;
}

/** Slug simples: minúsculas, números e hífens. */
function slugify(string $text): string
{
	$text = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text);
	$text = (string) preg_replace('/[^a-z0-9]+/', '-', $text);
	return trim($text, '-');
}

function is_valid_slug(string $slug): bool
{
	return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug);
}

/** Nonce do CSP desta requisição (scripts inline JSON-LD). */
function csp_nonce(): string
{
	static $nonce = null;
	return $nonce ??= base64_encode(random_bytes(16));
}

function is_external_url(string $href): bool
{
	if (!preg_match('#^https?://#', $href)) {
		return false;
	}
	return parse_url($href, PHP_URL_HOST) !== parse_url(base_url(), PHP_URL_HOST);
}

/** Datas em pt-BR. */
function format_date(?string $date, string $style = 'long'): string
{
	if (!$date) {
		return '';
	}
	$ts = strtotime($date);
	if (!$ts) {
		return '';
	}
	if ($style === 'short') {
		return date('d/m/Y', $ts);
	}
	$months = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
	return date('j', $ts) . ' de ' . $months[(int) date('n', $ts) - 1] . ' de ' . date('Y', $ts);
}

function now(): string
{
	return date('Y-m-d H:i:s');
}

/** Hash do IP (limite de tentativas sem guardar o IP). */
function client_key(): string
{
	return hash_hmac('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? 'cli'), (string) fck_config('secret', 'fck'));
}

/** Contagem de palavras para leitura estimada. */
function reading_minutes(string $html): int
{
	$words = preg_match_all('/[\p{L}\p{N}]+/u', strip_tags($html));
	return max(1, (int) round($words / 200));
}

/**
 * Versão publicada: commit atual (lido de .git, criado pelo deploy por Git
 * da Hostinger) e horário do deploy. Alternativa: arquivo VERSION na raiz.
 *
 * @return array{commit:string,branch:string,deployed:string}|null
 */
function deploy_info(): ?array
{
	static $info = false;
	if ($info !== false) {
		return $info;
	}
	$info = null;
	$git  = FCK_ROOT . '/.git';
	if (is_file($git)) { // worktree/submódulo: "gitdir: caminho"
		$line = trim((string) file_get_contents($git));
		if (str_starts_with($line, 'gitdir:')) {
			$git = trim(substr($line, 7));
			$git = str_starts_with($git, '/') ? $git : FCK_ROOT . '/' . $git;
		}
	}
	if (is_dir($git) && is_file($git . '/HEAD')) {
		$head   = trim((string) file_get_contents($git . '/HEAD'));
		$branch = '';
		$commit = '';
		$stamp  = filemtime($git . '/HEAD') ?: 0;
		if (str_starts_with($head, 'ref:')) {
			$ref    = trim(substr($head, 4));
			$branch = (string) preg_replace('#^refs/heads/#', '', $ref);
			if (is_file($git . '/' . $ref)) {
				$commit = trim((string) file_get_contents($git . '/' . $ref));
				$stamp  = max($stamp, (int) filemtime($git . '/' . $ref));
			} elseif (is_file($git . '/packed-refs')) {
				foreach (file($git . '/packed-refs') ?: [] as $l) {
					if (str_ends_with(trim($l), ' ' . $ref)) {
						$commit = substr(trim($l), 0, 40);
					}
				}
			}
		} else {
			$commit = $head; // HEAD destacado
		}
		foreach (['FETCH_HEAD', 'ORIG_HEAD', 'index'] as $f) {
			if (is_file($git . '/' . $f)) {
				$stamp = max($stamp, (int) filemtime($git . '/' . $f));
			}
		}
		if (preg_match('/^[0-9a-f]{7,40}$/', $commit)) {
			$info = ['commit' => substr($commit, 0, 7), 'branch' => $branch, 'deployed' => $stamp ? date('d/m/Y H:i', $stamp) : ''];
		}
	}
	if (!$info && is_file(FCK_ROOT . '/VERSION')) {
		$v = trim((string) file_get_contents(FCK_ROOT . '/VERSION'));
		if ($v !== '') {
			$info = ['commit' => mb_substr(clean_line($v), 0, 40), 'branch' => '', 'deployed' => date('d/m/Y H:i', (int) filemtime(FCK_ROOT . '/VERSION'))];
		}
	}
	return $info;
}
