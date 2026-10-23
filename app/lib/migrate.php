<?php
/**
 * Migrations versionadas (app/migrations/NNN_nome.sql), aplicadas em ordem
 * e registradas na tabela "migrations". Nunca destrutivas.
 */

declare(strict_types=1);

function migrations_pending(): array
{
	$files = glob(FCK_APP . '/migrations/*.sql') ?: [];
	sort($files);
	try {
		$applied = array_column(db_all('SELECT name FROM migrations'), 'name');
	} catch (Throwable) {
		$applied = [];
	}
	return array_values(array_filter($files, fn($f) => !in_array(basename($f), $applied, true)));
}

function migrate(): array
{
	$done = [];
	foreach (migrations_pending() as $file) {
		$sql = (string) file_get_contents($file);
		foreach (array_filter(array_map('trim', preg_split('/;\s*\n/', $sql) ?: [])) as $statement) {
			if (preg_match('/^(--[^\n]*\n?)*$/', $statement)) {
				continue;
			}
			db()->exec($statement);
		}
		db_insert('migrations', ['name' => basename($file), 'applied_at' => now()]);
		$done[] = basename($file);
	}
	return $done;
}

function setting(string $name, mixed $default = null): mixed
{
	static $cache = [];
	if (!array_key_exists($name, $cache)) {
		$row            = db_one('SELECT value FROM settings WHERE name = ?', [$name]);
		$cache[$name] = $row ? json_decode((string) $row['value'], true) : null;
	}
	return $cache[$name] ?? $default;
}

function setting_set(string $name, mixed $value): void
{
	db_query('REPLACE INTO settings (name, value) VALUES (?, ?)', [$name, json_encode($value, JSON_UNESCAPED_UNICODE)]);
}
