<?php defined('FCK_ROOT') || exit; $t = content_types()[$type]; $sortable = $type !== 'article'; ?>
<div class="adm-head">
	<h1><?= e($t[1]) ?></h1>
	<a class="btn-admin btn-primary" href="<?= e(url("/admin/entries/$type/new")) ?>">+ Adicionar <?= e(mb_strtolower($t[0])) ?></a>
</div>
<?php if (!$rows) : ?>
	<p class="adm-empty">Nenhum registro ainda.</p>
<?php else : ?>
	<form method="post">
		<?= csrf_field() ?>
		<input type="hidden" name="action" value="reorder">
		<table class="adm-table adm-table--list">
			<thead><tr><th>Título</th><?php if ($type === 'page') : ?><th>Modelo</th><?php endif; ?><th>Status</th><th>Atualizado</th><?php if ($sortable) : ?><th>Ordem</th><?php endif; ?></tr></thead>
			<tbody>
				<?php foreach ($rows as $r) : ?>
					<tr>
						<td><a href="<?= e(url('/admin/edit/' . $r['id'])) ?>"><strong><?= e($r['title'] ?: '(sem título)') ?></strong></a><?php if ($r['slug'] && entry_has_slug(['type' => $type] + $r)) : ?><br><span class="adm-muted">/<?= e($r['slug']) ?></span><?php endif; ?></td>
						<?php if ($type === 'page') : ?><td><?= e(page_templates()[$r['template']] ?? '—') ?></td><?php endif; ?>
						<td><span class="adm-status adm-status--<?= e($r['status']) ?>"><?= e(status_label($r['status'])) ?></span></td>
						<td class="adm-muted"><?= e(date('d/m/Y H:i', strtotime($r['updated_at']))) ?></td>
						<?php if ($sortable) : ?><td><input class="fck-small" type="number" name="order[<?= (int) $r['id'] ?>]" value="<?= (int) $r['menu_order'] ?>" aria-label="Ordem de <?= e($r['title']) ?>"></td><?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php if ($sortable) : ?><p><button class="btn-admin" type="submit">Salvar ordem</button> <span class="adm-muted">Menor número aparece primeiro no site.</span></p><?php endif; ?>
	</form>
<?php endif; ?>
