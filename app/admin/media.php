<?php
/**
 * Biblioteca de mídia do painel: upload, listagem, seletor (JSON), edição e exclusão.
 */

declare(strict_types=1);

function admin_media(array $parts, string $method): void
{
	$sub = $parts[1] ?? '';

	if ($sub === 'upload' && $method === 'POST') {
		$files   = $_FILES['files'] ?? null;
		$created = [];
		$errors  = [];
		if ($files && is_array($files['name'])) {
			foreach ($files['name'] as $i => $name) {
				if ($files['error'][$i] !== UPLOAD_ERR_OK) {
					$errors[] = $name . ': falha no envio (código ' . $files['error'][$i] . ').';
					continue;
				}
				if (str_ends_with(strtolower($name), '.svg') && !is_admin()) {
					$errors[] = $name . ': apenas administradores podem enviar SVG.';
					continue;
				}
				[$id, $err] = media_import($files['tmp_name'][$i], $name);
				$id ? $created[] = media_json(media_get($id)) : $errors[] = $name . ': ' . $err;
			}
		}
		if (wants_json()) {
			json_response(['created' => $created, 'errors' => $errors], $errors && !$created ? 422 : 200);
		}
		if ($created) {
			flash('success', count($created) . ' arquivo(s) enviado(s). Preencha o texto alternativo.');
		}
		if ($errors) {
			flash('error', $errors);
		}
		redirect_to('/admin/media');
	}

	if ($sub === 'list.json') {
		$type   = query('type');
		$q      = '%' . query('q') . '%';
		$params = [$q, $q];
		$where  = '(original_name LIKE ? OR alt LIKE ?)';
		if ($type === 'image') {
			$where .= " AND mime LIKE 'image/%'";
		}
		$rows = db_all("SELECT * FROM media WHERE $where ORDER BY id DESC LIMIT 200", $params);
		json_response(['items' => array_map('media_json', $rows)]);
	}

	if (ctype_digit($sub)) {
		$m = media_get((int) $sub);
		if (!$m) {
			admin_404();
		}
		if ($method === 'POST') {
			if (input('action') === 'delete') {
				media_delete((int) $sub);
				flash('success', 'Arquivo excluído.');
				redirect_to('/admin/media');
			}
			$focal = trim((string) input('focal', ''));
			db_update('media', [
				'alt'              => mb_substr(clean_line((string) input('alt', '')), 0, 255),
				'decorative'       => input('decorative') ? 1 : 0,
				'caption'          => mb_substr(clean_line((string) input('caption', '')), 0, 255),
				'credit'           => mb_substr(clean_line((string) input('credit', '')), 0, 160),
				'license'          => mb_substr(clean_line((string) input('license', '')), 0, 160),
				'rights_confirmed' => input('rights_confirmed') ? 1 : 0,
				'focal'            => preg_match('/^\d{1,3}% \d{1,3}%$/', $focal) ? $focal : '',
				'poster_id'        => (int) input('poster_id') ?: null,
			], ['id' => (int) $sub]);
			flash('success', 'Informações salvas.');
			redirect_to('/admin/media/' . $sub);
		}
		admin_view('media-edit', ['title' => $m['original_name'], 'm' => $m]);
		return;
	}

	$type  = query('tipo');
	$where = $type === 'imagem' ? "WHERE mime LIKE 'image/%'" : ($type === 'video' ? "WHERE mime LIKE 'video/%'" : ($type === 'sem-alt' ? "WHERE mime LIKE 'image/%' AND decorative = 0 AND alt = ''" : ''));
	admin_view('media', ['title' => 'Biblioteca de mídia', 'rows' => db_all("SELECT * FROM media $where ORDER BY id DESC LIMIT 300"), 'filter' => $type]);
}

function media_json(array $m): array
{
	return [
		'id'    => (int) $m['id'],
		'name'  => $m['original_name'],
		'mime'  => $m['mime'],
		'alt'   => $m['alt'],
		'thumb' => str_starts_with($m['mime'], 'image/') ? media_url($m, 'thumb') : '',
		'url'   => url(uploads_rel() . '/' . $m['path']),
		'wide'  => str_starts_with($m['mime'], 'image/') ? media_url($m, 'wide') : '',
		'width' => (int) $m['width'],
		'height'=> (int) $m['height'],
	];
}
