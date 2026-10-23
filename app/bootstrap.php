<?php
/**
 * Inicialização: configuração, erros, sessão, banco e bibliotecas.
 */

declare(strict_types=1);

define('FCK_ROOT', dirname(__DIR__));
define('FCK_APP', __DIR__);
define('FCK_VERSION', '1.0.0');

mb_internal_encoding('UTF-8');
date_default_timezone_set('America/Sao_Paulo');

/** Configuração: app/config.php (servidor) ou variáveis de ambiente (testes/dev). */
function fck_config(?string $key = null, mixed $default = null): mixed
{
	static $config = null;
	if ($config === null) {
		$file   = getenv('FCK_CONFIG') ?: FCK_APP . '/config.php';
		$config = is_file($file) ? (array) require $file : [];
		$env    = [
			'env'    => getenv('FCK_ENV'),
			'url'    => getenv('FCK_URL'),
			'secret' => getenv('FCK_SECRET'),
		];
		foreach ($env as $k => $v) {
			if ($v !== false && $v !== '') {
				$config[$k] = $v;
			}
		}
		foreach (['host', 'port', 'name', 'user', 'password'] as $k) {
			$v = getenv('FCK_DB_' . strtoupper($k));
			if ($v !== false) {
				$config['db'][$k] = $v;
			}
		}
		$config += ['env' => 'local', 'url' => '', 'secret' => '', 'db' => [], 'smtp' => []];
	}
	if ($key === null) {
		return $config;
	}
	$value = $config;
	foreach (explode('.', $key) as $part) {
		if (!is_array($value) || !array_key_exists($part, $value)) {
			return $default;
		}
		$value = $value[$part];
	}
	return $value;
}

function fck_env(): string
{
	return (string) fck_config('env', 'local');
}

function fck_is_production(): bool
{
	return fck_env() === 'production';
}

// Erros: nunca exibir detalhes em produção; registrar em storage/logs.
error_reporting(E_ALL);
ini_set('display_errors', fck_is_production() ? '0' : (PHP_SAPI === 'cli' ? '1' : '0'));
ini_set('log_errors', '1');
$logDir = FCK_ROOT . '/storage/logs';
if (!is_dir($logDir)) {
	@mkdir($logDir, 0775, true);
}
ini_set('error_log', $logDir . '/php-' . date('Y-m') . '.log');

require FCK_APP . '/lib/helpers.php';
require FCK_APP . '/lib/db.php';
require FCK_APP . '/lib/session.php';
require FCK_APP . '/lib/auth.php';
require FCK_APP . '/lib/media.php';
require FCK_APP . '/lib/sanitize.php';
require FCK_APP . '/schema/fields.php';
require FCK_APP . '/schema/templates.php';
require FCK_APP . '/schema/types.php';
require FCK_APP . '/schema/globals.php';
require FCK_APP . '/lib/content.php';
require FCK_APP . '/lib/fields.php';
require FCK_APP . '/lib/redirects.php';
require FCK_APP . '/lib/seo.php';
require FCK_APP . '/lib/mail.php';
require FCK_APP . '/lib/contact.php';
require FCK_APP . '/lib/migrate.php';
require FCK_APP . '/lib/seed.php';
require FCK_APP . '/lib/gaps.php';
require FCK_APP . '/lib/view.php';
require FCK_APP . '/lib/router.php';

set_exception_handler(static function (Throwable $e): void {
	error_log('[fck] ' . $e::class . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
	if (PHP_SAPI === 'cli') {
		fwrite(STDERR, $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
		exit(1);
	}
	if (!headers_sent()) {
		http_response_code(500);
	}
	fck_render_error_page();
});
