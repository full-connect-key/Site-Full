<?php defined('FCK_ROOT') || exit; $img = str_starts_with($m['mime'], 'image/'); ?>
<p class="adm-muted"><a href="<?= e(url('/admin/media')) ?>">← Biblioteca</a></p>
<h1><?= e($m['original_name']) ?></h1>
<div class="adm-edit">
	<div class="adm-edit-main">
		<div class="adm-box adm-media-preview">
			<?php if ($img) : ?><img src="<?= e(media_url($m, 'wide')) ?>" alt="" data-focal-image><?php elseif (str_starts_with($m['mime'], 'video/')) : ?><video src="<?= e(url(uploads_rel() . '/' . $m['path'])) ?>" controls preload="metadata"></video><?php else : ?><p><a href="<?= e(url(uploads_rel() . '/' . $m['path'])) ?>" target="_blank" rel="noopener">Abrir arquivo ↗</a></p><?php endif; ?>
		</div>
		<p class="adm-muted"><?= e($m['mime']) ?> · <?= e(number_format($m['size'] / 1024, 0, ',', '.')) ?> KB<?= $m['width'] ? ' · ' . (int) $m['width'] . '×' . (int) $m['height'] : '' ?> · <?= e(url(uploads_rel() . '/' . $m['path'])) ?></p>
	</div>
	<form method="post" class="adm-edit-side adm-box adm-form">
		<?= csrf_field() ?>
		<?php if ($img) : ?>
			<label for="alt">Texto alternativo</label>
			<input id="alt" name="alt" value="<?= e($m['alt']) ?>" maxlength="255" data-count>
			<p class="fck-help">Descreva o que a imagem mostra para quem usa leitor de tela.</p>
			<label class="fck-check"><input type="checkbox" name="decorative" value="1"<?= $m['decorative'] ? ' checked' : '' ?>> Imagem decorativa (sem texto alternativo)</label>
			<label for="focal">Ponto focal</label>
			<input id="focal" name="focal" value="<?= e($m['focal']) ?>" placeholder="50% 50%" pattern="\d{1,3}% \d{1,3}%">
			<p class="fck-help">Clique na imagem para definir o ponto que nunca deve ser cortado.</p>
		<?php endif; ?>
		<?php if (str_starts_with($m['mime'], 'video/')) : ?>
			<label for="poster_id">Poster (ID de uma imagem da biblioteca)</label>
			<input id="poster_id" name="poster_id" type="number" value="<?= e((string) $m['poster_id']) ?>">
		<?php endif; ?>
		<label for="caption">Legenda</label>
		<input id="caption" name="caption" value="<?= e($m['caption']) ?>" maxlength="255">
		<label for="credit">Crédito / autoria</label>
		<input id="credit" name="credit" value="<?= e($m['credit']) ?>" maxlength="160">
		<label for="license">Licença / origem</label>
		<input id="license" name="license" value="<?= e($m['license']) ?>" maxlength="160">
		<label class="fck-check"><input type="checkbox" name="rights_confirmed" value="1"<?= $m['rights_confirmed'] ? ' checked' : '' ?>> Direito de uso confirmado</label>
		<button class="btn-admin btn-primary" type="submit">Salvar</button>
		<button class="btn-link danger" type="submit" name="action" value="delete" data-confirm="Excluir o arquivo? Se ele estiver em uso, a imagem some do site.">Excluir arquivo</button>
	</form>
</div>
