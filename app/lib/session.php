<?php
/**
 * Sessão segura (painel) e proteção CSRF.
 */

declare(strict_types=1);

function session_boot(): void
{
	if (session_status() === PHP_SESSION_ACTIVE || PHP_SAPI === 'cli') {
		return;
	}
	$secure = str_starts_with(base_url(), 'https://');
	session_name('fck_session');
	session_set_cookie_params([
		'lifetime' => 0,
		'path'     => '/',
		'secure'   => $secure,
		'httponly' => true,
		'samesite' => 'Lax',
	]);
	ini_set('session.use_strict_mode', '1');
	ini_set('session.gc_maxlifetime', (string) (8 * 3600));
	$dir = FCK_ROOT . '/storage/sessions';
	if (!is_dir($dir)) {
		@mkdir($dir, 0770, true);
	}
	if (is_writable($dir)) {
		session_save_path($dir);
	}
	session_start();
}

/** Só inicia sessão se o cookie existir (o site público não cria sessões). */
function session_resume(): void
{
	if (!empty($_COOKIE['fck_session'])) {
		session_boot();
	}
}

function csrf_token(): string
{
	session_boot();
	if (empty($_SESSION['csrf'])) {
		$_SESSION['csrf'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['csrf'];
}

function csrf_field(): string
{
	return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
	session_boot();
	$sent = (string) ($_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
	if ($sent === '' || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $sent)) {
		http_response_code(419);
		exit('Sessão expirada. Volte e tente novamente.');
	}
}

function flash(string $type, string|array $message): void
{
	session_boot();
	$_SESSION['flash'][] = ['type' => $type, 'messages' => (array) $message];
}

function flashes(): array
{
	session_boot();
	$f = $_SESSION['flash'] ?? [];
	unset($_SESSION['flash']);
	return $f;
}

/**
 * Limite de tentativas por chave (login, formulário).
 * Retorna true se o limite foi excedido.
 */
function rate_limited(string $bucket, int $max, int $seconds): bool
{
	$key = substr($bucket . ':' . client_key(), 0, 120);
	db_query('DELETE FROM rate_limits WHERE expires_at < NOW()');
	$row = db_one('SELECT hits FROM rate_limits WHERE bucket = ?', [$key]);
	if ($row && (int) $row['hits'] >= $max) {
		return true;
	}
	if ($row) {
		db_query('UPDATE rate_limits SET hits = hits + 1 WHERE bucket = ?', [$key]);
	} else {
		db_insert('rate_limits', ['bucket' => $key, 'hits' => 1, 'expires_at' => date('Y-m-d H:i:s', time() + $seconds)]);
	}
	return false;
}
