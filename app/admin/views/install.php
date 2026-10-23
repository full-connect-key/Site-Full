<?php defined('FCK_ROOT') || exit; ?>
<h1>Instalação do site</h1>
<?php if (!empty($done)) : ?>
	<p>O site já está instalado.</p>
	<p><a class="btn-admin btn-primary" href="<?= e(url('/admin/login')) ?>">Ir para o login</a></p>
<?php elseif (!empty($problems)) : ?>
	<div class="adm-notice adm-notice--error"><p>Antes de instalar, resolva:</p><ul><?php foreach ($problems as $p) : ?><li><?= e($p) ?></li><?php endforeach; ?></ul></div>
	<p>Depois, recarregue esta página. O passo a passo está em <code>docs/DEPLOYMENT.md</code>.</p>
<?php else : ?>
	<p>Este passo cria as tabelas no banco MySQL, o primeiro usuário administrador e importa o conteúdo inicial (textos aprovados e logos oficiais).</p>
	<?php if ($errors) : ?><div class="adm-notice adm-notice--error"><ul><?php foreach ($errors as $er) : ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
	<form method="post" class="adm-form">
		<?= csrf_field() ?>
		<label for="email">E-mail do administrador</label>
		<input id="email" name="email" type="email" required autocomplete="username">
		<label for="password">Senha (mínimo 12 caracteres)</label>
		<input id="password" name="password" type="password" required minlength="12" autocomplete="new-password">
		<button class="btn-admin btn-primary" type="submit">Instalar</button>
	</form>
<?php endif; ?>
