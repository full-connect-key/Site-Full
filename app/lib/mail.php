<?php
/**
 * Envio de e-mail via SMTP (PHPMailer, em app/vendor). Configurado em
 * app/config.php › smtp. Sem SMTP configurado, nada é enviado.
 */

declare(strict_types=1);

require_once FCK_APP . '/vendor/phpmailer/Exception.php';
require_once FCK_APP . '/vendor/phpmailer/PHPMailer.php';
require_once FCK_APP . '/vendor/phpmailer/SMTP.php';

function mail_configured(): bool
{
	return (string) fck_config('smtp.host', '') !== '';
}

/** @return array{0:bool,1:string} [enviado, erro] */
function send_mail(array $to, string $subject, string $body, ?array $reply_to = null): array
{
	if (!mail_configured()) {
		return [false, 'SMTP não configurado'];
	}
	$m = new PHPMailer\PHPMailer\PHPMailer(true);
	try {
		$m->isSMTP();
		$m->CharSet  = 'UTF-8';
		$m->Host     = (string) fck_config('smtp.host');
		$m->Port     = (int) fck_config('smtp.port', 587);
		$user        = (string) fck_config('smtp.user', '');
		$m->SMTPAuth = $user !== '';
		$m->Username = $user;
		$m->Password = (string) fck_config('smtp.password', '');
		$secure      = (string) fck_config('smtp.secure', '');
		$m->SMTPSecure  = $secure;
		$m->SMTPAutoTLS = $secure !== '';
		$m->Timeout  = 15;
		$from        = (string) fck_config('smtp.from', $user);
		$m->setFrom($from, (string) fck_config('smtp.from_name', ''));
		foreach ($to as $addr) {
			$m->addAddress($addr);
		}
		if ($reply_to) {
			$m->addReplyTo($reply_to[0], $reply_to[1] ?? '');
		}
		$m->Subject = $subject;
		$m->Body    = $body;
		$m->send();
		return [true, ''];
	} catch (Throwable $e) {
		error_log('[fck] e-mail: ' . $m->ErrorInfo);
		return [false, mb_substr($m->ErrorInfo ?: $e->getMessage(), 0, 250)];
	}
}
