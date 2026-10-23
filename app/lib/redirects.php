<?php
/**
 * Redirecionamentos 301: automáticos (mudança de endereço ao publicar) e
 * manuais (ex.: endereços do site atual), com suporte a prefixo.
 */

declare(strict_types=1);

function normalize_path(string $path): string
{
	$path = '/' . trim((string) parse_url($path, PHP_URL_PATH), '/');
	return $path === '/' ? '/' : $path;
}

function redirect_add(string $from, string $to, string $match = 'exact', bool $automatic = false): void
{
	$from = normalize_path($from);
	$to   = preg_match('#^https?://#', $to) ? $to : normalize_path($to);
	if ($from === $to) {
		return;
	}
	db_query('DELETE FROM redirects WHERE from_path = ? OR from_path = ?', [$from, $to]);
	db_query('UPDATE redirects SET to_path = ? WHERE to_path = ?', [$to, $from]); // evita cadeias
	db_insert('redirects', ['from_path' => $from, 'to_path' => $to, 'match_type' => $match === 'prefix' ? 'prefix' : 'exact', 'automatic' => $automatic ? 1 : 0, 'created_at' => now()]);
}

/** Procura redirect para o caminho; responde 301 se encontrar. */
function redirect_lookup(string $path): void
{
	$path = normalize_path($path);
	$row  = db_one("SELECT * FROM redirects WHERE match_type = 'exact' AND from_path = ? LIMIT 1", [$path]);
	$to   = $row['to_path'] ?? null;
	if (!$to) {
		foreach (db_all("SELECT * FROM redirects WHERE match_type = 'prefix' ORDER BY LENGTH(from_path) DESC") as $r) {
			$prefix = rtrim($r['from_path'], '/');
			if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
				$to = rtrim($r['to_path'], '/') . substr($path, strlen($prefix));
				break;
			}
		}
	}
	if ($to) {
		$qs = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_QUERY);
		redirect_to(($to ?: '/') . ($qs ? '?' . $qs : ''), 301);
	}
}
