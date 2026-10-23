<?php defined('FCK_ROOT') || exit; $me = current_user(); ?>
<h1>Usuários</h1>
<p class="adm-muted">Administrador: tudo. Editor: conteúdo, mídia, menus e textos — sem usuários, mensagens de contato e destinatários.</p>
<table class="adm-table">
	<thead><tr><th>Usuário</th><th>Papel</th><th>Último acesso</th><th>Ações</th></tr></thead>
	<tbody>
		<?php foreach ($rows as $u) : ?>
			<tr>
				<td><?= e($u['name'] ?: '—') ?><br><span class="adm-muted"><?= e($u['email']) ?></span></td>
				<td><?php if ((int) $u['id'] === (int) $me['id']) : ?><?= $u['role'] === 'admin' ? 'Administrador' : 'Editor' ?> (você)<?php else : ?>
					<form method="post" action="<?= e(url('/admin/users/' . $u['id'])) ?>" class="adm-inline"><?= csrf_field() ?><input type="hidden" name="action" value="role"><select name="role" aria-label="Papel"><option value="editor"<?= $u['role'] === 'editor' ? ' selected' : '' ?>>Editor</option><option value="admin"<?= $u['role'] === 'admin' ? ' selected' : '' ?>>Administrador</option></select> <button class="btn-link" type="submit">Salvar</button></form>
				<?php endif; ?></td>
				<td class="adm-muted"><?= $u['last_login_at'] ? e(date('d/m/Y H:i', strtotime($u['last_login_at']))) : '—' ?></td>
				<td>
					<form method="post" action="<?= e(url('/admin/users/' . $u['id'])) ?>" class="adm-inline"><?= csrf_field() ?><input type="hidden" name="action" value="password"><input type="password" name="password" placeholder="Nova senha (12+)" minlength="12" aria-label="Nova senha" autocomplete="new-password"> <button class="btn-link" type="submit">Redefinir</button></form>
					<?php if ((int) $u['id'] !== (int) $me['id']) : ?><form method="post" action="<?= e(url('/admin/users/' . $u['id'])) ?>" class="adm-inline"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><button class="btn-link danger" type="submit" data-confirm="Remover este usuário?">Remover</button></form><?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<h2>Adicionar usuário</h2>
<form method="post" class="adm-form adm-form--narrow">
	<?= csrf_field() ?>
	<input type="hidden" name="action" value="create">
	<label for="u-name">Nome</label><input id="u-name" name="name" maxlength="120">
	<label for="u-email">E-mail</label><input id="u-email" name="email" type="email" required>
	<label for="u-pass">Senha inicial (12+ caracteres)</label><input id="u-pass" name="password" type="password" minlength="12" required autocomplete="new-password">
	<label for="u-role">Papel</label><select id="u-role" name="role"><option value="editor">Editor</option><option value="admin">Administrador</option></select>
	<button class="btn-admin btn-primary" type="submit">Criar usuário</button>
</form>
