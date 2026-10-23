<?php defined('FCK_ROOT') || exit; ?>
<h1>Ferramentas</h1>
<?php if ($log) : ?><div class="adm-notice adm-notice--success"><ul><?php foreach ($log as $l) : ?><li><?= e($l) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<section class="adm-box">
	<h2>Conteúdo inicial</h2>
	<p>Cria o que ainda não existe (páginas, plataformas, setores, categorias, menus, textos e logos oficiais) a partir dos textos aprovados. Nunca sobrescreve o que foi editado.</p>
	<form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="seed"><button class="btn-admin" type="submit">Importar conteúdo inicial</button></form>
</section>
<section class="adm-box">
	<h2>Banco de dados</h2>
	<p><?= $pending ? 'Atualizações pendentes: ' . e(implode(', ', $pending)) : 'Estrutura do banco atualizada.' ?></p>
	<?php if ($pending) : ?><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="migrate"><button class="btn-admin" type="submit">Aplicar atualizações</button></form><?php endif; ?>
</section>
