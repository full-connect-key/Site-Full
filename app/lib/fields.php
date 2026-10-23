<?php
/**
 * Campos do painel: renderização e sanitização a partir dos esquemas
 * (app/schema). Tipos: text, textarea, richtext, number, checkbox, select,
 * email, url, image, media, link, post, posts, repeater, heading.
 * Textos acima do limite NÃO são truncados: o valor anterior é mantido e um
 * erro é exibido ao editor.
 */

declare(strict_types=1);

final class Fields
{
	private static array $errors = [];

	// ------------------------------------------------------------------ render

	public static function group(array $group, array $values, string $prefix): void
	{
		if (!empty($group['description'])) {
			echo '<p class="fck-group-desc">' . e($group['description']) . '</p>';
		}
		echo '<div class="fck-fields">';
		foreach ($group['fields'] as $field) {
			self::field($field, $values[$field['name'] ?? ''] ?? null, $prefix, 0);
		}
		echo '</div>';
	}

	public static function field(array $field, mixed $value, string $prefix, int $depth): void
	{
		$type = $field['type'] ?? 'text';
		if ($type === 'heading') {
			echo '<h4 class="fck-heading">' . e($field['label']) . '</h4>';
			return;
		}
		$name     = $prefix . '[' . $field['name'] . ']';
		$id       = 'f-' . substr(md5($name . random_int(0, PHP_INT_MAX)), 0, 10);
		$label    = $field['label'] ?? $field['name'];
		$help     = (string) ($field['help'] ?? '');
		$max      = (int) ($field['max'] ?? 0);
		$required = !empty($field['required']);
		$value  ??= $field['default'] ?? null;
		$width    = isset($field['width']) ? ' fck-w-' . $field['width'] : '';

		echo '<div class="fck-field fck-type-' . e($type) . $width . '">';

		if ($type === 'checkbox') {
			printf(
				'<label class="fck-check"><input type="hidden" name="%1$s" value="0"><input type="checkbox" id="%2$s" name="%1$s" value="1"%3$s> %4$s</label>',
				e($name), e($id), $value ? ' checked' : '', e($label)
			);
			self::help($help);
			echo '</div>';
			return;
		}

		printf(
			'<label class="fck-label" for="%s">%s%s</label>',
			e($id), e($label), $required ? ' <span class="fck-req" aria-hidden="true">*</span><span class="sr-only">(obrigatório)</span>' : ''
		);

		$count = $max ? ' maxlength="' . $max . '" data-count' : '';
		$req   = $required ? ' data-required' : '';
		switch ($type) {
			case 'textarea':
				printf('<textarea id="%s" name="%s" rows="%d"%s%s>%s</textarea>', e($id), e($name), (int) ($field['rows'] ?? ($max > 400 ? 6 : 3)), $count, $req, e((string) $value));
				break;
			case 'richtext':
				printf('<div class="fck-richtext" data-richtext><textarea id="%s" name="%s" rows="14">%s</textarea></div>', e($id), e($name), e((string) $value));
				break;
			case 'number':
				printf(
					'<input type="number" id="%s" name="%s" value="%s"%s%s class="fck-small">',
					e($id), e($name), e((string) $value),
					isset($field['min']) ? ' min="' . (int) $field['min'] . '"' : '',
					isset($field['maxValue']) ? ' max="' . (int) $field['maxValue'] . '"' : ''
				);
				break;
			case 'select':
				printf('<select id="%s" name="%s">', e($id), e($name));
				if (empty($field['no_empty'])) {
					echo '<option value="">—</option>';
				}
				foreach ($field['options'] as $v => $l) {
					printf('<option value="%s"%s>%s</option>', e((string) $v), (string) $value === (string) $v ? ' selected' : '', e($l));
				}
				echo '</select>';
				break;
			case 'image':
			case 'media':
				self::media($id, $name, (int) $value, $type === 'image' ? 'image' : '');
				break;
			case 'link':
				self::link($id, $name, is_array($value) ? $value : []);
				break;
			case 'post':
				printf('<select id="%s" name="%s"><option value="">—</option>%s</select>', e($id), e($name), self::options((array) $field['post_type'], (int) $value));
				break;
			case 'posts':
				self::posts($id, $name, is_array($value) ? array_map('intval', $value) : [], (array) $field['post_type']);
				break;
			case 'repeater':
				self::repeater($name, is_array($value) ? array_values($value) : [], $field, $depth);
				break;
			default:
				printf(
					'<input type="%s" id="%s" name="%s" value="%s"%s%s>',
					in_array($type, ['url', 'email'], true) ? $type : 'text', e($id), e($name), e((string) $value), $count, $req
				);
		}
		$extra = $max && !in_array($type, ['repeater', 'link'], true) ? 'Até ' . $max . ' caracteres.' : '';
		self::help(trim($help . ' ' . $extra));
		echo '</div>';
	}

	private static function help(string $help): void
	{
		if ($help !== '') {
			echo '<p class="fck-help">' . e($help) . '</p>';
		}
	}

	private static function media(string $id, string $name, int $media_id, string $library): void
	{
		$m       = $media_id ? media_get($media_id) : null;
		$preview = '';
		if ($m) {
			$preview = str_starts_with((string) $m['mime'], 'image/')
				? '<img src="' . e(media_url($m, 'thumb')) . '" alt="">'
				: '<span class="fck-file">' . e($m['original_name']) . '</span>';
		}
		printf(
			'<div class="fck-media" data-media data-library="%5$s"><input type="hidden" id="%1$s" name="%2$s" value="%3$s"><div class="fck-media-preview">%4$s</div><button type="button" class="btn-admin" data-media-select>Selecionar</button> <button type="button" class="btn-link" data-media-remove%6$s>Remover</button></div>',
			e($id), e($name), $m ? (int) $media_id : '', $preview, e($library), $m ? '' : ' hidden'
		);
	}

	/** Tipos que podem ser destino de links internos. */
	public static function linkable(): array
	{
		return ['page' => 'Páginas', 'platform' => 'Plataformas', 'case' => 'Cases', 'article' => 'Artigos'];
	}

	private static function options(array $types, int $selected): string
	{
		$html    = '';
		$grouped = count($types) > 1;
		foreach ($types as $type) {
			$rows = db_all('SELECT id, title, published FROM entries WHERE type = ? ORDER BY menu_order, title', [$type]);
			if (!$rows) {
				continue;
			}
			if ($grouped) {
				$html .= '<optgroup label="' . e(content_types()[$type][1] ?? $type) . '">';
			}
			foreach ($rows as $r) {
				$html .= sprintf(
					'<option value="%d"%s>%s</option>',
					$r['id'], (int) $r['id'] === $selected ? ' selected' : '',
					e(($r['title'] ?: '#' . $r['id']) . ($r['published'] === null ? ' (rascunho)' : ''))
				);
			}
			if ($grouped) {
				$html .= '</optgroup>';
			}
		}
		return $html;
	}

	private static function link(string $id, string $name, array $v): void
	{
		$type = ($v['type'] ?? 'post') === 'url' ? 'url' : 'post';
		echo '<div class="fck-link" data-link>';
		printf('<span class="fck-sub"><label for="%1$s-l">Texto</label><input type="text" id="%1$s-l" name="%2$s[label]" value="%3$s" maxlength="60" data-count></span>', e($id), e($name), e((string) ($v['label'] ?? '')));
		printf(
			'<span class="fck-sub"><label for="%1$s-t">Destino</label><select id="%1$s-t" name="%2$s[type]" data-link-type><option value="post"%3$s>Página do site</option><option value="url"%4$s>Endereço (URL)</option></select></span>',
			e($id), e($name), $type === 'post' ? ' selected' : '', $type === 'url' ? ' selected' : ''
		);
		printf('<span class="fck-sub" data-show="post"><label for="%1$s-p">Página</label><select id="%1$s-p" name="%2$s[post]"><option value="">—</option>%3$s</select></span>', e($id), e($name), self::options(array_keys(self::linkable()), (int) ($v['post'] ?? 0)));
		printf('<span class="fck-sub" data-show="url"><label for="%1$s-u">URL</label><input type="text" id="%1$s-u" name="%2$s[url]" value="%3$s" placeholder="/contato?assunto=imprensa ou https://..."></span>', e($id), e($name), e((string) ($v['url'] ?? '')));
		echo '</div><p class="fck-help">Deixe o texto vazio para ocultar o botão. Links para páginas acompanham mudanças de endereço.</p>';
	}

	private static function posts(string $id, string $name, array $values, array $types): void
	{
		echo '<div class="fck-posts" data-posts>';
		printf('<ol class="fck-posts-list" data-name="%s">', e($name . '[]'));
		foreach ($values as $pid) {
			$t = db_value('SELECT title FROM entries WHERE id = ?', [$pid]);
			if ($t === null) {
				continue;
			}
			printf(
				'<li><input type="hidden" name="%s[]" value="%d"><span>%s</span> <button type="button" class="btn-link" data-up aria-label="Mover para cima">↑</button> <button type="button" class="btn-link" data-down aria-label="Mover para baixo">↓</button> <button type="button" class="btn-link" data-del aria-label="Remover">×</button></li>',
				e($name), $pid, e((string) $t)
			);
		}
		echo '</ol>';
		printf('<select id="%s" data-posts-add><option value="">+ Adicionar…</option>%s</select></div>', e($id), self::options($types, 0));
	}

	private static function repeater(string $name, array $rows, array $field, int $depth): void
	{
		$ph = '__i' . $depth . '__';
		printf('<div class="fck-repeater" data-repeater data-max="%d" data-placeholder="%s">', (int) ($field['max_rows'] ?? 50), e($ph));
		echo '<ol class="fck-rows">';
		foreach ($rows as $i => $row) {
			self::row($name . '[' . $i . ']', is_array($row) ? $row : [], $field, $i + 1, $depth);
		}
		echo '</ol><template>';
		self::row($name . '[' . $ph . ']', [], $field, 0, $depth);
		echo '</template>';
		printf('<button type="button" class="btn-admin" data-add-row>+ %s</button></div>', e($field['row_label'] ?? 'Adicionar item'));
	}

	private static function row(string $prefix, array $row, array $field, int $n, int $depth): void
	{
		echo '<li class="fck-row"><div class="fck-row-bar"><span class="fck-row-num">' . ($n ?: '') . '</span>';
		echo '<button type="button" class="btn-link" data-up aria-label="Mover para cima">↑</button> <button type="button" class="btn-link" data-down aria-label="Mover para baixo">↓</button> <button type="button" class="btn-link danger" data-del>Remover</button></div><div class="fck-row-fields">';
		foreach ($field['fields'] as $sub) {
			self::field($sub, $row[$sub['name'] ?? ''] ?? null, $prefix, $depth + 1);
		}
		echo '</div></li>';
	}

	// ---------------------------------------------------------------- sanitize

	public static function sanitize_group(array $group, array $input, array $previous): array
	{
		$out = [];
		foreach ($group['fields'] as $field) {
			if (($field['type'] ?? '') === 'heading') {
				continue;
			}
			$k       = $field['name'];
			$out[$k] = self::sanitize_field($field, $input[$k] ?? null, $previous[$k] ?? null);
		}
		return $out;
	}

	public static function sanitize_field(array $field, mixed $raw, mixed $previous): mixed
	{
		$type = $field['type'] ?? 'text';
		switch ($type) {
			case 'text':
			case 'textarea':
				$value = $type === 'textarea' ? clean_multiline((string) $raw) : clean_line((string) $raw);
				$max   = (int) ($field['max'] ?? 0);
				if ($max && mb_strlen($value) > $max) {
					self::$errors[] = sprintf('"%s" passou de %d caracteres e não foi alterado.', $field['label'], $max);
					return $previous;
				}
				return $value;
			case 'richtext':
				return html_sanitize((string) $raw);
			case 'url':
				$v = trim((string) $raw);
				return $v === '' || preg_match('#^https?://#', $v) && filter_var($v, FILTER_VALIDATE_URL) ? $v : self::err($field, 'URL inválida (use https://...).', $previous);
			case 'email':
				$v = trim((string) $raw);
				return $v === '' || filter_var($v, FILTER_VALIDATE_EMAIL) ? $v : self::err($field, 'E-mail inválido.', $previous);
			case 'number':
				return $raw === '' || $raw === null ? null : (int) $raw;
			case 'checkbox':
				return !empty($raw) && $raw !== '0';
			case 'select':
				$raw = (string) $raw;
				return array_key_exists($raw, $field['options']) ? $raw : '';
			case 'image':
			case 'media':
			case 'post':
				return (int) $raw > 0 ? (int) $raw : null;
			case 'posts':
				return array_values(array_filter(array_map('intval', (array) $raw)));
			case 'link':
				$raw   = is_array($raw) ? $raw : [];
				$label = clean_line((string) ($raw['label'] ?? ''));
				$url   = trim((string) ($raw['url'] ?? ''));
				if ($url !== '' && !preg_match('#^(/|https?://|mailto:|tel:)#', $url)) {
					self::$errors[] = sprintf('Link "%s": use um caminho que comece com "/" ou uma URL completa.', $field['label']);
					$url = '';
				}
				return [
					'label' => mb_strlen($label) <= 60 ? $label : (string) ($previous['label'] ?? ''),
					'type'  => ($raw['type'] ?? '') === 'url' ? 'url' : 'post',
					'post'  => (int) ($raw['post'] ?? 0) ?: null,
					'url'   => $url,
				];
			case 'repeater':
				$rows = [];
				$prev = is_array($previous) ? array_values($previous) : [];
				foreach (array_values(is_array($raw) ? $raw : []) as $i => $row) {
					if (!is_array($row)) {
						continue;
					}
					$clean = self::sanitize_group(['fields' => $field['fields']], $row, $prev[$i] ?? []);
					if (!self::row_empty($clean)) {
						$rows[] = $clean;
					}
				}
				$max = (int) ($field['max_rows'] ?? 0);
				return $max ? array_slice($rows, 0, $max) : $rows;
		}
		return clean_line((string) $raw);
	}

	private static function err(array $field, string $msg, mixed $previous): mixed
	{
		self::$errors[] = '"' . $field['label'] . '": ' . $msg;
		return $previous;
	}

	private static function row_empty(array $row): bool
	{
		foreach ($row as $v) {
			if (is_array($v)) {
				if (array_filter($v, fn($x) => $x !== '' && $x !== null && $x !== 'post' && $x !== [] && $x !== false)) {
					return false;
				}
			} elseif ($v !== '' && $v !== null && $v !== false) {
				return false;
			}
		}
		return true;
	}

	public static function missing_required(array $group, array $values): array
	{
		$missing = [];
		foreach ($group['fields'] as $f) {
			if (!empty($f['required'])) {
				$v = $values[$f['name']] ?? null;
				if ($v === null || $v === '' || $v === []) {
					$missing[] = $f['label'];
				}
			}
		}
		return $missing;
	}

	public static function take_errors(): array
	{
		$e            = self::$errors;
		self::$errors = [];
		return $e;
	}
}
