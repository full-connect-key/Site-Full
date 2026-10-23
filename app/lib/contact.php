<?php
/**
 * Formulário de contato: validação no servidor, honeypot, tempo mínimo,
 * limite por IP, gravação em "Mensagens de contato" e e-mail para os
 * destinatários configurados. Funciona com e sem JavaScript.
 */

declare(strict_types=1);

const CONTACT_LIMITS = ['name' => 120, 'email' => 254, 'company' => 120, 'message' => 4000];

function contact_settings(): array
{
	return (array) setting('contact', []) + ['recipients' => [], 'retention_months' => 12];
}

function contact_recipients(string $subject): array
{
	$out = [];
	foreach ((array) contact_settings()['recipients'] as $r) {
		if ((($r['subject'] ?? '') === 'all' || ($r['subject'] ?? '') === $subject) && filter_var($r['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
			$out[] = $r['email'];
		}
	}
	return array_values(array_unique($out));
}

/** @return array{0:array,1:array} [dados limpos, erros por campo] */
function contact_validate(array $raw): array
{
	$data = [
		'subject' => (string) ($raw['subject'] ?? ''),
		'name'    => clean_line((string) ($raw['name'] ?? '')),
		'email'   => trim((string) ($raw['email'] ?? '')),
		'company' => clean_line((string) ($raw['company'] ?? '')),
		'message' => clean_multiline((string) ($raw['message'] ?? '')),
		'consent' => !empty($raw['consent']),
	];
	$errors = [];
	if (!array_key_exists($data['subject'], fck_contact_subjects())) {
		$errors['subject'] = ui('form', 'required');
	}
	foreach (['name', 'message'] as $f) {
		if ($data[$f] === '') {
			$errors[$f] = ui('form', 'required');
		}
	}
	if ($data['email'] === '') {
		$errors['email'] = ui('form', 'required');
	} elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
		$errors['email'] = ui('form', 'invalid_email');
	}
	foreach (CONTACT_LIMITS as $f => $max) {
		if (!isset($errors[$f]) && mb_strlen((string) $data[$f]) > $max) {
			$errors[$f] = fill(ui('form', 'too_long'), ['max' => $max]);
		}
	}
	if (!$data['consent']) {
		$errors['consent'] = ui('form', 'consent_required');
	}
	return [$data, $errors];
}

/** Token do formulário (CSRF sem sessão): HMAC do horário de renderização. */
function contact_token(): string
{
	$t = (string) time();
	return $t . '.' . hash_hmac('sha256', 'contact|' . $t, (string) fck_config('secret', 'fck'));
}

function contact_token_valid(string $token): ?int
{
	[$t, $sig] = array_pad(explode('.', $token, 2), 2, '');
	if (!ctype_digit($t) || !hash_equals(hash_hmac('sha256', 'contact|' . $t, (string) fck_config('secret', 'fck')), $sig)) {
		return null;
	}
	$age = time() - (int) $t;
	return $age >= 0 && $age < 6 * 3600 ? $age : null;
}

function contact_handle(): never
{
	$json = wants_json();
	$back = (string) ($_SERVER['HTTP_REFERER'] ?? url('/'));
	if (!str_starts_with($back, base_url())) {
		$back = url('/');
	}
	$respond = static function (int $status, array $body) use ($json, $back): never {
		if ($json) {
			json_response($body, $status);
		}
		$sep = str_contains($back, '?') ? '&' : '?';
		$clean = (string) preg_replace('/([?&])contato=[^&#]*&?/', '$1', $back);
		redirect_to(rtrim($clean, '?&') . $sep . 'contato=' . ($body['ok'] ? 'sucesso' : 'erro') . '#formulario', 303);
	};

	// Origem: somente do próprio site.
	$origin = (string) ($_SERVER['HTTP_ORIGIN'] ?? '');
	if ($origin !== '' && parse_url($origin, PHP_URL_HOST) !== parse_url(base_url(), PHP_URL_HOST)) {
		$respond(403, ['ok' => false, 'message' => ui('form', 'send_error')]);
	}
	$age = contact_token_valid((string) ($_POST['_token'] ?? ''));
	if ($age === null) {
		$respond(400, ['ok' => false, 'message' => ui('form', 'send_error')]);
	}
	// Honeypot ou envio rápido demais: robôs recebem "ok" e nada é salvo.
	if (!empty($_POST['website']) || $age < 3) {
		$respond(200, ['ok' => true]);
	}
	if (rate_limited('contact', 5, 600)) {
		$respond(429, ['ok' => false, 'message' => ui('form', 'rate_limited')]);
	}
	[$data, $errors] = contact_validate($_POST);
	if ($errors) {
		$respond(422, ['ok' => false, 'errors' => $errors, 'message' => ui('form', 'error_summary')]);
	}
	try {
		contact_store_and_send($data);
	} catch (Throwable $e) {
		error_log('[fck] contato: ' . $e->getMessage());
		$respond(500, ['ok' => false, 'message' => ui('form', 'send_error')]);
	}
	$respond(200, ['ok' => true]);
}

function contact_store_and_send(array $data): void
{
	$id = db_insert('submissions', [
		'subject'    => $data['subject'],
		'name'       => $data['name'],
		'email'      => $data['email'],
		'company'    => $data['company'],
		'message'    => $data['message'],
		'consent_at' => now(),
		'delivery'   => 'pending',
		'created_at' => now(),
	]);
	$to = contact_recipients($data['subject']);
	if (!$to || !mail_configured()) {
		db_update('submissions', ['delivery' => 'stored'], ['id' => $id]);
		return;
	}
	$subjects = fck_contact_subjects();
	$body     = implode("\n", [
		'Assunto: ' . $subjects[$data['subject']],
		'Nome: ' . $data['name'],
		'E-mail: ' . $data['email'],
		'Empresa: ' . ($data['company'] ?: '—'),
		'',
		$data['message'],
		'',
		'Consentimento registrado em ' . date('d/m/Y H:i'),
	]);
	[$sent, $error] = send_mail($to, '[Site] ' . $subjects[$data['subject']] . ' — ' . $data['name'], $body, [$data['email'], $data['name']]);
	// Falha de e-mail não perde a mensagem: ela fica salva no painel.
	db_update('submissions', ['delivery' => $sent ? 'sent' : 'failed', 'delivery_error' => $error], ['id' => $id]);
}

/** Apaga mensagens mais antigas que a retenção configurada (roda no login do painel). */
function contact_retention(): int
{
	$months = max(1, (int) contact_settings()['retention_months']);
	return db_query('DELETE FROM submissions WHERE created_at < DATE_SUB(NOW(), INTERVAL ? MONTH)', [$months])->rowCount();
}
