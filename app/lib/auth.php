<?php
/**
 * Usuários do painel: Administrador (tudo) e Editor (conteúdo e mídia; sem
 * usuários, configurações sensíveis ou mensagens de contato).
 */

declare(strict_types=1);

function current_user(): ?array
{
	static $user = false;
	if ($user === false) {
		$user = null;
		session_resume();
		$id = (int) ($_SESSION['user_id'] ?? 0);
		if ($id) {
			$user = db_one('SELECT id, email, name, role FROM users WHERE id = ?', [$id]);
			// Sessão expira após 8 h de inatividade.
			if ($user && (time() - (int) ($_SESSION['seen'] ?? 0)) > 8 * 3600) {
				$user = null;
				logout();
			} elseif ($user) {
				$_SESSION['seen'] = time();
			}
		}
	}
	return $user;
}

function is_admin(): bool
{
	return (current_user()['role'] ?? '') === 'admin';
}

function is_staff(): bool
{
	return in_array(current_user()['role'] ?? '', ['admin', 'editor'], true);
}

function require_staff(): void
{
	if (!is_staff()) {
		redirect_to('/admin/login?next=' . rawurlencode((string) ($_SERVER['REQUEST_URI'] ?? '/admin')));
	}
}

function require_admin(): void
{
	require_staff();
	if (!is_admin()) {
		http_response_code(403);
		exit('Sem permissão.');
	}
}

/** Tenta autenticar. Retorna mensagem de erro ou null. */
function attempt_login(string $email, string $password): ?string
{
	$generic = 'E-mail ou senha incorretos.';
	if (rate_limited('login', (int) (getenv('FCK_LOGIN_LIMIT') ?: 10), 15 * 60)) {
		return 'Muitas tentativas. Aguarde alguns minutos.';
	}
	$user = db_one('SELECT * FROM users WHERE email = ?', [strtolower(trim($email))]);
	if (!$user) {
		password_verify($password, '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG'); // tempo constante
		return $generic;
	}
	if ($user['locked_until'] && strtotime((string) $user['locked_until']) > time()) {
		return 'Acesso temporariamente bloqueado por tentativas incorretas. Tente em 15 minutos.';
	}
	if (!password_verify($password, (string) $user['password_hash'])) {
		$fails = (int) $user['failed_attempts'] + 1;
		db_update('users', [
			'failed_attempts' => $fails,
			'locked_until'    => $fails >= 5 ? date('Y-m-d H:i:s', time() + 15 * 60) : null,
		], ['id' => $user['id']]);
		return $generic;
	}
	if (password_needs_rehash((string) $user['password_hash'], PASSWORD_DEFAULT)) {
		db_update('users', ['password_hash' => password_hash($password, PASSWORD_DEFAULT)], ['id' => $user['id']]);
	}
	db_update('users', ['failed_attempts' => 0, 'locked_until' => null, 'last_login_at' => now()], ['id' => $user['id']]);
	session_boot();
	session_regenerate_id(true);
	$_SESSION['user_id'] = (int) $user['id'];
	$_SESSION['seen']    = time();
	return null;
}

function logout(): void
{
	session_boot();
	$_SESSION = [];
	if (ini_get('session.use_cookies')) {
		$p = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
	}
	session_destroy();
}

function create_user(string $email, string $password, string $role = 'editor', string $name = ''): int
{
	return db_insert('users', [
		'email'         => strtolower(trim($email)),
		'name'          => $name,
		'password_hash' => password_hash($password, PASSWORD_DEFAULT),
		'role'          => $role === 'admin' ? 'admin' : 'editor',
		'created_at'    => now(),
		'updated_at'    => now(),
	]);
}

function password_problem(string $password): ?string
{
	if (mb_strlen($password) < 12) {
		return 'A senha precisa ter pelo menos 12 caracteres.';
	}
	return null;
}
