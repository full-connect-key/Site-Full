<?php
/**
 * Linha de comando (desenvolvimento ou servidor com SSH):
 *   php scripts/fck.php migrate
 *   php scripts/fck.php seed [--reset]
 *   php scripts/fck.php create-admin email senha
 *   php scripts/fck.php gaps
 *   php scripts/fck.php claims        (sai com erro se houver afirmação "a validar" publicada)
 *   php scripts/fck.php fixtures      (somente banco de teste)
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
	exit;
}
require __DIR__ . '/../app/bootstrap.php';

$cmd = $argv[1] ?? 'help';
switch ($cmd) {
	case 'migrate':
		$done = migrate();
		echo $done ? 'Aplicadas: ' . implode(', ', $done) . "\n" : "Nada pendente.\n";
		break;
	case 'seed':
		foreach (run_seed(in_array('--reset', $argv, true)) as $line) {
			echo $line, "\n";
		}
		break;
	case 'create-admin':
		[$email, $pass] = [$argv[2] ?? '', $argv[3] ?? ''];
		if ($p = password_problem($pass)) {
			fwrite(STDERR, $p . "\n");
			exit(1);
		}
		$id = db_value('SELECT id FROM users WHERE email = ?', [strtolower($email)]);
		$id ? db_update('users', ['password_hash' => password_hash($pass, PASSWORD_DEFAULT), 'role' => 'admin'], ['id' => $id]) : create_user($email, $pass, 'admin', 'Administrador');
		echo "Administrador pronto: $email\n";
		break;
	case 'gaps':
		foreach (content_gaps() as $g) {
			echo "{$g['priority']}\t{$g['item']}\t({$g['where']})\n";
		}
		break;
	case 'claims':
		$claims = pending_claims();
		foreach ($claims as $c) {
			echo "A validar\t{$c['platform']} › {$c['where']}\t{$c['text']}" . ($c['note'] !== '' ? "\t({$c['note']})" : '') . "\n";
		}
		echo $claims ? count($claims) . " afirmação(ões) a validar. Não publique em produção sem validar (docs/CLAIMS_REGISTER.md).\n" : "Nenhuma afirmação a validar.\n";
		exit($claims ? 1 : 0);
	case 'fixtures':
		require __DIR__ . '/../tests/fixtures/fixtures.php';
		break;
	default:
		echo "Comandos: migrate | seed [--reset] | create-admin email senha | gaps | claims | fixtures\n";
}
