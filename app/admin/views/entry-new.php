<?php defined('FCK_ROOT') || exit; ?>
<h1><?= e($title) ?></h1>
<form method="post" class="adm-form adm-form--narrow">
	<?= csrf_field() ?>
	<label for="title">Título</label>
	<input id="title" name="title" required maxlength="140" autofocus>
	<?php if ($type === 'page') : ?>
		<label for="template">Modelo da página</label>
		<select id="template" name="template" required>
			<option value="">— escolha —</option>
			<?php foreach (page_templates() as $k => $l) : ?><option value="<?= e($k) ?>"><?= e($l) ?></option><?php endforeach; ?>
		</select>
		<p class="fck-help">O modelo define as seções da página. Cada função (ex.: base dos cases) deve ter uma única página publicada.</p>
	<?php endif; ?>
	<button class="btn-admin btn-primary" type="submit">Criar rascunho</button>
</form>
