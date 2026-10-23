<?php
defined('FCK_ROOT') || exit;
$is_global = $row['type'] === 'global';
$type_label = content_types()[$row['type']][0] ?? $row['type'];
$public_path = !$is_global ? path_for($row + ['data' => $data]) : null;
?>
<form method="post" class="adm-edit" data-edit-form>
	<?= csrf_field() ?>
	<div class="adm-edit-main">
		<p class="adm-muted"><a href="<?= e(url($is_global ? '/admin' : '/admin/entries/' . $row['type'])) ?>">← <?= e($is_global ? 'Painel' : content_types()[$row['type']][1]) ?></a></p>
		<label class="sr-only" for="title">Título</label>
		<input class="adm-title" id="title" name="title" value="<?= e($row['title']) ?>" maxlength="200" required aria-describedby="title-help">
		<p class="fck-help" id="title-help"><?php
			echo match ($row['type']) {
				'page' => 'Título interno da página (o título exibido fica nas seções).',
				'press' => 'Manchete original da matéria.',
				'testimonial', 'partner', 'team', 'author' => 'Nome da pessoa.',
				'global' => 'Nome interno desta configuração.',
				default => 'Nome exibido no site.',
			};
		?></p>
		<?php if ($row['type'] === 'page' && !$row['template']) : ?>
			<div class="adm-notice adm-notice--warning"><p>Escolha um modelo ao lado e salve para ver os campos.</p></div>
		<?php endif; ?>
		<?php foreach ($groups as $group) : ?>
			<details class="adm-group" open>
				<summary><?= e($group['title']) ?></summary>
				<div class="adm-group-body"><?php Fields::group($group, (array) ($data[$group['id']] ?? []), 'fck[' . $group['id'] . ']'); ?></div>
			</details>
		<?php endforeach; ?>
	</div>
	<aside class="adm-edit-side">
		<div class="adm-box adm-sticky">
			<p><span class="adm-status adm-status--<?= e($row['status']) ?>"><?= e(status_label($row['status'])) ?></span></p>
			<?php if ($row['published_at']) : ?><p class="adm-muted">Publicado pela primeira vez em <?= e(date('d/m/Y H:i', strtotime($row['published_at']))) ?></p><?php endif; ?>
			<div class="adm-actions">
				<button class="btn-admin" type="submit" name="action" value="save">Salvar rascunho</button>
				<button class="btn-admin btn-primary" type="submit" name="action" value="publish"><?= $row['published'] === null ? 'Publicar' : 'Publicar alterações' ?></button>
				<?php if ($public_path !== null || $is_global) : ?>
					<a class="btn-admin" href="<?= e(url('/admin/preview/' . $row['id'])) ?>" target="_blank" rel="noopener" data-preview>Visualizar rascunho ↗</a>
				<?php endif; ?>
			</div>
			<p class="fck-help">O site só muda ao publicar. A visualização mostra o rascunho salvo (salve antes de visualizar).</p>
			<?php if ($public_path !== null && $row['published'] !== null) : ?>
				<p><a href="<?= e(url($public_path)) ?>" target="_blank" rel="noopener">Ver no site ↗</a></p>
			<?php endif; ?>
		</div>
		<div class="adm-box">
			<?php if (entry_has_slug($row)) : ?>
				<label class="fck-label" for="slug">Slug (endereço)</label>
				<input id="slug" name="slug" value="<?= e($row['slug']) ?>" pattern="[a-z0-9]+(-[a-z0-9]+)*" required>
				<p class="fck-help">Minúsculas, números e hífens. Ao publicar uma mudança, o endereço antigo redireciona automaticamente (301).</p>
			<?php endif; ?>
			<?php if ($row['type'] === 'page') : ?>
				<label class="fck-label" for="template">Modelo</label>
				<select id="template" name="template">
					<option value="">—</option>
					<?php foreach (page_templates() as $k => $l) : ?><option value="<?= e($k) ?>"<?= $row['template'] === $k ? ' selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?>
				</select>
			<?php endif; ?>
			<?php if (!$is_global) : ?>
				<label class="fck-label" for="menu_order">Ordem</label>
				<input id="menu_order" class="fck-small" type="number" name="menu_order" value="<?= (int) $row['menu_order'] ?>">
			<?php endif; ?>
		</div>
		<div class="adm-box">
			<p><a href="<?= e(url('/admin/revisions/' . $row['id'])) ?>">Histórico de versões</a></p>
			<?php if (!$is_global && $row['published'] !== null) : ?>
				<button class="btn-link" type="submit" name="action" value="unpublish" data-confirm="Tirar do site? O conteúdo continua salvo como rascunho.">Despublicar</button>
			<?php endif; ?>
			<?php if (!$is_global && (is_admin() || $row['seed_key'] === null)) : ?>
				<button class="btn-link danger" type="submit" name="action" value="delete" data-confirm="Excluir definitivamente este registro e seu histórico?">Excluir</button>
			<?php endif; ?>
		</div>
	</aside>
</form>
<?php require FCK_APP . '/admin/views/media-modal.php'; ?>
