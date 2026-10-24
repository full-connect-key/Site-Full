<?php
/**
 * Painel administrativo (/admin): rotas e ações.
 */

declare(strict_types=1);

require_once FCK_APP . '/admin/helpers.php';

function admin_dispatch(string $path, string $method): void
{
	send_admin_headers();
	$route = trim(substr($path, strlen('/admin')), '/');
	$parts = $route === '' ? [] : explode('/', $route);

	// Instalação (primeiro acesso).
	if (($parts[0] ?? '') === 'install') {
		admin_install($method);
		return;
	}
	if (!site_installed()) {
		redirect_to('/admin/install');
	}

	if (($parts[0] ?? '') === 'login') {
		admin_login($method);
		return;
	}
	if (($parts[0] ?? '') === 'logout' && $method === 'POST') {
		csrf_check();
		logout();
		redirect_to('/admin/login');
	}

	require_staff();
	if ($method === 'POST') {
		csrf_check();
	}

	switch ($parts[0] ?? '') {
		case '':
			admin_view('dashboard', ['title' => 'Painel', 'gaps' => content_gaps(), 'recent' => db_all('SELECT id, type, title, status, updated_at FROM entries WHERE type <> \'global\' ORDER BY updated_at DESC LIMIT 8')]);
			return;
		case 'entries':
			admin_entries($parts[1] ?? '', $parts[2] ?? '', $method);
			return;
		case 'edit':
			admin_edit((int) ($parts[1] ?? 0), $method);
			return;
		case 'settings':
			$id = (int) db_value("SELECT id FROM entries WHERE type = 'global' AND slug = ?", [$parts[1] ?? '']);
			$id ? admin_edit($id, $method) : admin_404();
			return;
		case 'revisions':
			admin_revisions((int) ($parts[1] ?? 0), $method);
			return;
		case 'preview':
			$row = entry_raw((int) ($parts[1] ?? 0));
			if (!$row) {
				admin_404();
			}
			$tmp = $row + ['data' => json_dec($row['draft'])];
			redirect_to((path_for($tmp) ?? '/') . '?preview=1');
		case 'media':
			require_once FCK_APP . '/admin/media.php';
			admin_media($parts, $method);
			return;
		case 'redirects':
			admin_redirects($method);
			return;
		case 'submissions':
			require_admin();
			admin_submissions((int) ($parts[1] ?? 0), $method);
			return;
		case 'contact-settings':
			require_admin();
			admin_contact_settings($method);
			return;
		case 'users':
			require_admin();
			admin_users((int) ($parts[1] ?? 0), $method);
			return;
		case 'profile':
			admin_profile($method);
			return;
		case 'tools':
			require_admin();
			admin_tools($method);
			return;
	}
	admin_404();
}

// ---------------------------------------------------------------------------
// Instalação e login
// ---------------------------------------------------------------------------

function admin_install(string $method): void
{
	$problems = [];
	if (!is_file(FCK_APP . '/config.php') && !getenv('FCK_DB_NAME')) {
		$problems[] = 'Crie o arquivo app/config.php a partir de app/config.sample.php (Gerenciador de Arquivos da Hostinger) com os dados do banco MySQL.';
	} elseif (!db_ready()) {
		$problems[] = 'Não foi possível conectar ao banco MySQL. Confira host, nome, usuário e senha em app/config.php.';
	}
	if ((string) fck_config('secret', '') === '' || strlen((string) fck_config('secret', '')) < 32) {
		$problems[] = 'Defina "secret" em app/config.php com pelo menos 32 caracteres aleatórios.';
	}
	foreach (['uploads', 'storage'] as $dir) {
		if (!is_writable(FCK_ROOT . '/' . $dir)) {
			$problems[] = "A pasta $dir/ precisa ter permissão de escrita.";
		}
	}

	$installed = !$problems && site_installed() && (int) db_value('SELECT COUNT(*) FROM users') > 0;
	if ($installed) {
		admin_view('install', ['title' => 'Instalação', 'done' => true], false);
		return;
	}

	$errors = [];
	if ($method === 'POST' && !$problems) {
		csrf_check();
		$email = trim((string) input('email', ''));
		$pass  = (string) input('password', '');
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Informe um e-mail válido.';
		}
		if ($p = password_problem($pass)) {
			$errors[] = $p;
		}
		if (!$errors) {
			migrate();
			if (!(int) db_value('SELECT COUNT(*) FROM users')) {
				create_user($email, $pass, 'admin', 'Administrador');
			}
			$log = run_seed();
			attempt_login($email, $pass);
			flash('success', array_merge(['Instalação concluída. Conteúdo inicial importado.'], array_slice($log, -5)));
			redirect_to('/admin');
		}
	}
	admin_view('install', ['title' => 'Instalação', 'problems' => $problems, 'errors' => $errors], false);
}

function admin_login(string $method): void
{
	if (is_staff()) {
		redirect_to('/admin');
	}
	$error = null;
	if ($method === 'POST') {
		csrf_check();
		$error = attempt_login((string) input('email', ''), (string) input('password', ''));
		if ($error === null) {
			if (migrations_pending()) {
				migrate();
			}
			// Conteúdo inicial novo (campos, páginas) chega sozinho após um deploy; nunca sobrescreve edições.
			if (setting('seed_version') !== seed_version()) {
				run_seed();
				setting_set('seed_version', seed_version());
			}
			contact_retention();
			$next = (string) input('next', '/admin');
			redirect_to(str_starts_with($next, '/admin') ? $next : '/admin');
		}
	}
	admin_view('login', ['title' => 'Entrar', 'error' => $error, 'next' => (string) query('next', '/admin')], false);
}

// ---------------------------------------------------------------------------
// Conteúdo
// ---------------------------------------------------------------------------

function admin_entries(string $type, string $sub, string $method): void
{
	$types = content_types();
	if (!isset($types[$type]) || $type === 'global') {
		admin_404();
	}
	if ($sub === 'new') {
		if ($method === 'POST') {
			$title = clean_line((string) input('title', ''));
			if ($title === '') {
				flash('error', 'Informe o título.');
				redirect_to("/admin/entries/$type/new");
			}
			$tpl = $type === 'page' ? (string) input('template', '') : '';
			if ($type === 'page' && !isset(page_templates()[$tpl])) {
				flash('error', 'Escolha o modelo da página.');
				redirect_to("/admin/entries/$type/new");
			}
			$order = (int) db_value('SELECT COALESCE(MAX(menu_order), 0) + 1 FROM entries WHERE type = ?', [$type]);
			$id    = entry_create($type, $title, ['template' => $tpl, 'menu_order' => $order]);
			revision_add($id, $title, (string) entry_raw($id)['slug'], '[]', 'create');
			redirect_to("/admin/edit/$id");
		}
		admin_view('entry-new', ['title' => 'Adicionar ' . mb_strtolower($types[$type][0]), 'type' => $type]);
		return;
	}
	if ($method === 'POST' && input('action') === 'reorder') {
		foreach ((array) input('order', []) as $id => $pos) {
			db_update('entries', ['menu_order' => (int) $pos], ['id' => (int) $id, 'type' => $type]);
		}
		flash('success', 'Ordem atualizada.');
		redirect_to("/admin/entries/$type");
	}
	$rows = db_all('SELECT id, title, slug, template, status, menu_order, updated_at, published_at FROM entries WHERE type = ? ORDER BY menu_order, title', [$type]);
	admin_view('entries', ['title' => $types[$type][1], 'type' => $type, 'rows' => $rows]);
}

function admin_edit(int $id, string $method): void
{
	$row = entry_raw($id);
	if (!$row) {
		admin_404();
	}
	$is_global = $row['type'] === 'global';
	$groups    = groups_for($row);

	if ($method === 'POST') {
		$action = (string) input('action', 'save');
		if ($action === 'delete' && !$is_global && (is_admin() || $row['seed_key'] === null)) {
			entry_delete($id);
			flash('success', 'Registro excluído.');
			redirect_to('/admin/entries/' . $row['type']);
		}
		if ($action === 'unpublish' && !$is_global) {
			entry_unpublish($id);
			flash('success', 'Despublicado: o registro saiu do site.');
			redirect_to("/admin/edit/$id");
		}

		// Campos básicos
		$title = clean_line((string) input('title', $row['title']));
		$slug  = $row['slug'];
		if (entry_has_slug($row)) {
			$candidate = (string) input('slug', $row['slug']);
			if (!is_valid_slug($candidate)) {
				flash('error', 'Slug inválido: use letras minúsculas, números e hífens.');
				$candidate = $row['slug'];
			}
			$slug = entry_unique_slug($row['type'], $candidate, $id);
		}
		$template = $row['template'];
		if ($row['type'] === 'page' && isset(page_templates()[(string) input('template', '')])) {
			$template = (string) input('template');
			$groups   = groups_for(['type' => 'page', 'template' => $template] + $row);
		}

		$input    = (array) ($_POST['fck'] ?? []);
		$previous = json_dec($row['draft']);
		$data     = $previous;
		foreach ($groups as $group) {
			if (array_key_exists($group['id'], $input)) {
				$data[$group['id']] = Fields::sanitize_group($group, (array) $input[$group['id']], (array) ($previous[$group['id']] ?? []));
			}
		}
		$errors = Fields::take_errors();
		entry_save_draft($id, [
			'title'      => $title !== '' ? $title : $row['title'],
			'slug'       => $slug,
			'template'   => $template,
			'menu_order' => (int) input('menu_order', $row['menu_order']),
			'draft'      => json_enc($data),
		]);

		if ($action === 'publish') {
			$missing = [];
			foreach ($groups as $group) {
				foreach (Fields::missing_required($group, (array) ($data[$group['id']] ?? [])) as $label) {
					$missing[] = $group['title'] . ' › ' . $label;
				}
			}
			if ($missing) {
				flash('warning', array_merge(['Rascunho salvo, mas não publicado: preencha os campos obrigatórios.'], $missing));
			} else {
				entry_publish($id);
				flash('success', $is_global ? 'Configurações publicadas no site.' : 'Publicado no site.');
			}
		} else {
			flash('success', 'Rascunho salvo. O site só muda ao publicar.');
		}
		if ($errors) {
			flash('error', $errors);
		}
		redirect_to(($is_global ? '/admin/settings/' . $row['slug'] : "/admin/edit/$id") . (input('preview') ? '?open_preview=1' : ''));
	}

	admin_view('edit', [
		'title'  => $row['title'],
		'row'    => $row,
		'data'   => json_dec($row['draft']),
		'groups' => $groups,
	]);
}

function admin_revisions(int $id, string $method): void
{
	$row = entry_raw($id);
	if (!$row) {
		admin_404();
	}
	if ($method === 'POST') {
		$rev = (int) input('revision');
		if (revision_restore($rev)) {
			flash('success', 'Versão restaurada como rascunho. Revise e publique para aplicar no site.');
		}
		redirect_to($row['type'] === 'global' ? '/admin/settings/' . $row['slug'] : "/admin/edit/$id");
	}
	$revs = db_all('SELECT r.id, r.title, r.action, r.created_at, u.name, u.email FROM revisions r LEFT JOIN users u ON u.id = r.user_id WHERE r.entry_id = ? ORDER BY r.id DESC', [$id]);
	admin_view('revisions', ['title' => 'Histórico — ' . $row['title'], 'row' => $row, 'revs' => $revs]);
}

// ---------------------------------------------------------------------------
// Redirecionamentos, mensagens, contato, usuários, ferramentas
// ---------------------------------------------------------------------------

function admin_redirects(string $method): void
{
	if ($method === 'POST') {
		if (input('action') === 'delete') {
			db_delete('redirects', ['id' => (int) input('id')]);
		} else {
			$from = trim((string) input('from', ''));
			$to   = trim((string) input('to', ''));
			if (str_starts_with($from, '/') && (str_starts_with($to, '/') || preg_match('#^https://#', $to))) {
				redirect_add($from, $to, (string) input('match', 'exact'));
				flash('success', 'Redirecionamento salvo.');
			} else {
				flash('error', 'Use caminhos começando com "/" (destino também pode ser https://).');
			}
		}
		redirect_to('/admin/redirects');
	}
	admin_view('redirects', ['title' => 'Redirecionamentos', 'rows' => db_all('SELECT * FROM redirects ORDER BY created_at DESC')]);
}

function admin_submissions(int $id, string $method): void
{
	if ($method === 'POST' && input('action') === 'delete') {
		db_delete('submissions', ['id' => (int) input('id')]);
		flash('success', 'Mensagem excluída.');
		redirect_to('/admin/submissions');
	}
	if ($id) {
		$s = db_one('SELECT * FROM submissions WHERE id = ?', [$id]);
		$s ? admin_view('submission', ['title' => 'Mensagem de contato', 's' => $s]) : admin_404();
		return;
	}
	admin_view('submissions', ['title' => 'Mensagens de contato', 'rows' => db_all('SELECT id, subject, platform, name, email, delivery, created_at FROM submissions ORDER BY id DESC LIMIT 500')]);
}

function admin_contact_settings(string $method): void
{
	if ($method === 'POST') {
		$recipients = [];
		foreach ((array) input('recipients', []) as $r) {
			$email   = trim((string) ($r['email'] ?? ''));
			$subject = (string) ($r['subject'] ?? '');
			if (filter_var($email, FILTER_VALIDATE_EMAIL) && ($subject === 'all' || array_key_exists($subject, fck_contact_subjects()))) {
				$recipients[] = ['subject' => $subject, 'email' => $email];
			}
		}
		setting_set('contact', ['recipients' => $recipients, 'retention_months' => min(60, max(1, (int) input('retention_months', 12)))]);
		flash('success', 'Configurações do formulário salvas.');
		redirect_to('/admin/contact-settings');
	}
	admin_view('contact-settings', ['title' => 'Formulário de contato', 's' => contact_settings()]);
}

function admin_users(int $id, string $method): void
{
	if ($method === 'POST') {
		$action = (string) input('action');
		if ($action === 'create') {
			$email = trim((string) input('email'));
			$pass  = (string) input('password');
			$err   = !filter_var($email, FILTER_VALIDATE_EMAIL) ? 'E-mail inválido.' : password_problem($pass);
			if (!$err && db_value('SELECT id FROM users WHERE email = ?', [strtolower($email)])) {
				$err = 'Já existe um usuário com este e-mail.';
			}
			$err ? flash('error', $err) : (create_user($email, $pass, (string) input('role'), clean_line((string) input('name'))) && flash('success', 'Usuário criado.'));
		} elseif ($action === 'role' && $id && $id !== (int) current_user()['id']) {
			db_update('users', ['role' => input('role') === 'admin' ? 'admin' : 'editor', 'updated_at' => now()], ['id' => $id]);
			flash('success', 'Papel atualizado.');
		} elseif ($action === 'delete' && $id && $id !== (int) current_user()['id']) {
			db_delete('users', ['id' => $id]);
			flash('success', 'Usuário removido.');
		} elseif ($action === 'password' && $id) {
			$pass = (string) input('password');
			($e = password_problem($pass)) ? flash('error', $e) : (db_update('users', ['password_hash' => password_hash($pass, PASSWORD_DEFAULT), 'failed_attempts' => 0, 'locked_until' => null, 'updated_at' => now()], ['id' => $id]) && flash('success', 'Senha redefinida.'));
		}
		redirect_to('/admin/users');
	}
	admin_view('users', ['title' => 'Usuários', 'rows' => db_all('SELECT id, email, name, role, last_login_at FROM users ORDER BY email')]);
}

function admin_profile(string $method): void
{
	$me = current_user();
	if ($method === 'POST') {
		$row = db_one('SELECT password_hash FROM users WHERE id = ?', [$me['id']]);
		if (!password_verify((string) input('current'), (string) $row['password_hash'])) {
			flash('error', 'Senha atual incorreta.');
		} elseif ($e = password_problem((string) input('password'))) {
			flash('error', $e);
		} else {
			db_update('users', ['password_hash' => password_hash((string) input('password'), PASSWORD_DEFAULT), 'name' => clean_line((string) input('name', $me['name'])), 'updated_at' => now()], ['id' => $me['id']]);
			flash('success', 'Perfil atualizado.');
		}
		redirect_to('/admin/profile');
	}
	admin_view('profile', ['title' => 'Meu perfil', 'me' => $me]);
}

function admin_tools(string $method): void
{
	$log = null;
	if ($method === 'POST') {
		$action = (string) input('action');
		if ($action === 'seed') {
			$log = run_seed();
		} elseif ($action === 'migrate') {
			$log = migrate() ?: ['Nenhuma migration pendente.'];
		}
	}
	admin_view('tools', ['title' => 'Ferramentas', 'log' => $log, 'pending' => array_map('basename', migrations_pending())]);
}

function admin_404(): never
{
	http_response_code(404);
	admin_view('message', ['title' => 'Não encontrado', 'message' => 'A página do painel não existe.']);
	exit;
}
