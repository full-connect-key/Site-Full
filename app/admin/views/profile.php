<?php defined('FCK_ROOT') || exit; ?>
<h1>Meu perfil</h1>
<form method="post" class="adm-form adm-form--narrow">
	<?= csrf_field() ?>
	<p class="adm-muted"><?= e($me['email']) ?></p>
	<label for="name">Nome</label><input id="name" name="name" value="<?= e($me['name']) ?>" maxlength="120">
	<label for="current">Senha atual</label><input id="current" name="current" type="password" required autocomplete="current-password">
	<label for="password">Nova senha (12+ caracteres)</label><input id="password" name="password" type="password" minlength="12" required autocomplete="new-password">
	<button class="btn-admin btn-primary" type="submit">Salvar</button>
</form>
