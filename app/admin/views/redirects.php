<?php defined('FCK_ROOT') || exit; ?>
<h1>Redirecionamentos</h1>
<p class="adm-muted">Criados automaticamente quando o endereço de algo publicado muda. Use também para preservar endereços do site atual (SEO).</p>
<form method="post" class="adm-inline-form">
	<?= csrf_field() ?>
	<label>De <input name="from" placeholder="/servicos/branding" required></label>
	<label>Para <input name="to" placeholder="/cases ou https://..." required></label>
	<label>Tipo <select name="match"><option value="exact">Exato</option><option value="prefix">Prefixo (inclui subpáginas)</option></select></label>
	<button class="btn-admin btn-primary" type="submit">Adicionar</button>
</form>
<table class="adm-table">
	<thead><tr><th>De</th><th>Para</th><th>Tipo</th><th>Origem</th><th></th></tr></thead>
	<tbody>
		<?php if (!$rows) : ?><tr><td colspan="5" class="adm-muted">Nenhum redirecionamento.</td></tr><?php endif; ?>
		<?php foreach ($rows as $r) : ?>
			<tr><td><code><?= e($r['from_path']) ?></code></td><td><code><?= e($r['to_path']) ?></code></td><td><?= $r['match_type'] === 'prefix' ? 'Prefixo' : 'Exato' ?></td><td><?= $r['automatic'] ? 'Automático' : 'Manual' ?></td>
			<td><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button class="btn-link danger" type="submit">Excluir</button></form></td></tr>
		<?php endforeach; ?>
	</tbody>
</table>
