<?php defined('FCK_ROOT') || exit; ?>
<p class="adm-muted"><a href="<?= e(url('/admin/submissions')) ?>">← Mensagens</a></p>
<h1>Mensagem de <?= e($s['name']) ?></h1>
<table class="adm-table adm-table--kv"><tbody>
	<tr><th>Assunto</th><td><?= e(fck_contact_subjects()[$s['subject']] ?? $s['subject']) ?></td></tr>
	<tr><th>E-mail</th><td><a href="mailto:<?= e($s['email']) ?>"><?= e($s['email']) ?></a></td></tr>
	<tr><th>Empresa</th><td><?= e($s['company'] ?: '—') ?></td></tr>
	<tr><th>Consentimento</th><td><?= e(date('d/m/Y H:i', strtotime($s['consent_at']))) ?></td></tr>
	<tr><th>Envio</th><td><?= e($s['delivery']) ?><?= $s['delivery_error'] ? ' — ' . e($s['delivery_error']) : '' ?></td></tr>
	<tr><th>Mensagem</th><td><?= nl2br(e($s['message'])) ?></td></tr>
</tbody></table>
<form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $s['id'] ?>"><button class="btn-link danger" type="submit" data-confirm="Excluir esta mensagem?">Excluir mensagem</button></form>
