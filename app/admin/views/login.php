<?php defined('FCK_ROOT') || exit; ?>
<h1>Entrar</h1>
<?php if ($error) : ?><div class="adm-notice adm-notice--error" role="alert"><p><?= e($error) ?></p></div><?php endif; ?>
<form method="post" action="<?= e(url('/admin/login')) ?>" class="adm-form">
	<?= csrf_field() ?>
	<input type="hidden" name="next" value="<?= e($next) ?>">
	<label for="email">E-mail</label>
	<input id="email" name="email" type="email" autocomplete="username" required autofocus>
	<label for="password">Senha</label>
	<input id="password" name="password" type="password" autocomplete="current-password" required>
	<button class="btn-admin btn-primary" type="submit">Entrar</button>
</form>
