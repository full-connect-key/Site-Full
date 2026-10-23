<?php defined('FCK_ROOT') || exit; ?>
<p class="adm-muted"><a href="<?= e(url($row['type'] === 'global' ? '/admin/settings/' . $row['slug'] : '/admin/edit/' . $row['id'])) ?>">← Voltar à edição</a></p>
<h1><?= e($title) ?></h1>
<p class="adm-muted">Restaurar uma versão a transforma no rascunho atual. Para aplicar no site, publique em seguida. São guardadas as 50 versões mais recentes.</p>
<table class="adm-table">
	<thead><tr><th>Data</th><th>Ação</th><th>Por</th><th></th></tr></thead>
	<tbody>
		<?php foreach ($revs as $i => $r) : ?>
			<tr>
				<td><?= e(date('d/m/Y H:i:s', strtotime($r['created_at']))) ?></td>
				<td><?= e(action_label($r['action'])) ?></td>
				<td><?= e($r['name'] ?: ($r['email'] ?: 'Sistema')) ?></td>
				<td><?php if ($i > 0) : ?><form method="post"><?= csrf_field() ?><input type="hidden" name="revision" value="<?= (int) $r['id'] ?>"><button class="btn-link" type="submit">Restaurar esta versão</button></form><?php else : ?><span class="adm-muted">versão atual</span><?php endif; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
