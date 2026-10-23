<?php defined('FCK_ROOT') || exit; ?>
<h1>Painel do site</h1>
<div class="adm-grid">
	<section class="adm-box">
		<h2>Pendências de conteúdo</h2>
		<?php if (!$gaps) : ?>
			<p>Nenhuma pendência detectada.</p>
		<?php else : ?>
			<p class="adm-muted">Enquanto estes itens não forem preenchidos, as seções correspondentes ficam ocultas ou usam um substituto neutro.</p>
			<table class="adm-table"><tbody>
				<?php foreach ($gaps as $g) : ?>
					<tr><td><span class="adm-badge adm-badge--<?= e(strtolower($g['priority'])) ?>"><?= e($g['priority']) ?></span></td><td><?= e($g['item']) ?><br><span class="adm-muted"><?= e($g['where']) ?></span></td></tr>
				<?php endforeach; ?>
			</tbody></table>
		<?php endif; ?>
	</section>
	<section class="adm-box">
		<h2>Alterações recentes</h2>
		<table class="adm-table"><tbody>
			<?php foreach ($recent as $r) : ?>
				<tr><td><a href="<?= e(url('/admin/edit/' . $r['id'])) ?>"><?= e($r['title'] ?: '#' . $r['id']) ?></a><br><span class="adm-muted"><?= e(content_types()[$r['type']][0] ?? $r['type']) ?> · <?= e(status_label($r['status'])) ?></span></td><td class="adm-muted"><?= e(date('d/m/Y H:i', strtotime($r['updated_at']))) ?></td></tr>
			<?php endforeach; ?>
		</tbody></table>
	</section>
</div>
