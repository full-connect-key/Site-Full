<?php defined('FCK_ROOT') || exit; ?>
<div class="adm-head">
	<h1>Biblioteca de mídia</h1>
	<form method="post" action="<?= e(url('/admin/media/upload')) ?>" enctype="multipart/form-data" class="adm-upload">
		<?= csrf_field() ?>
		<label class="btn-admin btn-primary">Enviar arquivos<input type="file" name="files[]" multiple hidden accept="image/*,video/mp4,video/webm,application/pdf" data-autosubmit></label>
	</form>
</div>
<p class="adm-muted">Use apenas arquivos com direito de uso confirmado. Logos de clientes e de veículos de imprensa só aparecem no site com "Direito de uso confirmado" marcado. Imagens até 25 MB; o site gera versões otimizadas automaticamente.</p>
<nav class="adm-filters" aria-label="Filtrar mídia">
	<?php foreach (['' => 'Todos', 'imagem' => 'Imagens', 'video' => 'Vídeos', 'sem-alt' => 'Imagens sem texto alternativo'] as $k => $l) : ?>
		<a href="<?= e(url('/admin/media' . ($k ? '?tipo=' . $k : ''))) ?>"<?= $filter === $k ? ' aria-current="page"' : '' ?>><?= e($l) ?></a>
	<?php endforeach; ?>
</nav>
<?php if (!$rows) : ?><p class="adm-empty">Nenhum arquivo.</p><?php endif; ?>
<ul class="adm-media-grid">
	<?php foreach ($rows as $m) : ?>
		<li>
			<a href="<?= e(url('/admin/media/' . $m['id'])) ?>">
				<?php if (str_starts_with($m['mime'], 'image/')) : ?><img src="<?= e(media_url($m, 'thumb')) ?>" alt=""><?php else : ?><span class="adm-file"><?= e(strtoupper(pathinfo($m['path'], PATHINFO_EXTENSION))) ?></span><?php endif; ?>
				<span class="adm-media-name"><?= e($m['original_name']) ?></span>
				<?php if (str_starts_with($m['mime'], 'image/') && !$m['decorative'] && $m['alt'] === '') : ?><span class="adm-badge adm-badge--p0">sem alt</span><?php endif; ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
