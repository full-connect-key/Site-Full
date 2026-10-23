<?php
/**
 * Acesso ao MySQL via PDO (consultas sempre parametrizadas).
 */

declare(strict_types=1);

function db(): PDO
{
	static $pdo = null;
	if ($pdo === null) {
		$c = (array) fck_config('db', []);
		if (empty($c['name'])) {
			throw new RuntimeException('Banco de dados não configurado (app/config.php).');
		}
		$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $c['host'] ?? 'localhost', (int) ($c['port'] ?? 3306), $c['name']);
		$pdo = new PDO($dsn, (string) ($c['user'] ?? ''), (string) ($c['password'] ?? ''), [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		]);
		$pdo->exec("SET time_zone = '-03:00'");
	}
	return $pdo;
}

function db_ready(): bool
{
	try {
		db();
		return true;
	} catch (Throwable) {
		return false;
	}
}

function db_query(string $sql, array $params = []): PDOStatement
{
	$stmt = db()->prepare($sql);
	$stmt->execute($params);
	return $stmt;
}

function db_all(string $sql, array $params = []): array
{
	return db_query($sql, $params)->fetchAll();
}

function db_one(string $sql, array $params = []): ?array
{
	$row = db_query($sql, $params)->fetch();
	return $row === false ? null : $row;
}

function db_value(string $sql, array $params = []): mixed
{
	$v = db_query($sql, $params)->fetchColumn();
	return $v === false ? null : $v;
}

function db_insert(string $table, array $data): int
{
	$cols = array_keys($data);
	$sql  = sprintf(
		'INSERT INTO `%s` (%s) VALUES (%s)',
		$table,
		implode(',', array_map(fn($c) => "`$c`", $cols)),
		implode(',', array_fill(0, count($cols), '?'))
	);
	db_query($sql, array_values($data));
	return (int) db()->lastInsertId();
}

function db_update(string $table, array $data, array $where): int
{
	$set  = implode(',', array_map(fn($c) => "`$c` = ?", array_keys($data)));
	$cond = implode(' AND ', array_map(fn($c) => "`$c` = ?", array_keys($where)));
	return db_query("UPDATE `$table` SET $set WHERE $cond", [...array_values($data), ...array_values($where)])->rowCount();
}

function db_delete(string $table, array $where): int
{
	$cond = implode(' AND ', array_map(fn($c) => "`$c` = ?", array_keys($where)));
	return db_query("DELETE FROM `$table` WHERE $cond", array_values($where))->rowCount();
}

function json_dec(?string $json): array
{
	if ($json === null || $json === '') {
		return [];
	}
	$v = json_decode($json, true);
	return is_array($v) ? $v : [];
}

function json_enc(array $data): string
{
	return (string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
