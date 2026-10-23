<?php defined('FCK_ROOT') || exit; $labels = ['sent' => 'Enviado por e-mail', 'stored' => 'Salvo (sem destinatário/SMTP)', 'failed' => 'Falha no e-mail', 'pending' => 'Pendente']; ?>
<h1>Mensagens de contato</h1>
<p class="adm-muted">Dados pessoais: acesso restrito a administradores. Mensagens são apagadas após o prazo de retenção configurado.</p>
<table class="adm-table">
	<thead><tr><th>Data</th><th>Assunto</th><th>Nome</th><th>E-mail</th><th>Envio</th></tr></thead>
	<tbody>
		<?php if (!$rows) : ?><tr><td colspan="5" class="adm-muted">Nenhuma mensagem.</td></tr><?php endif; ?>
		<?php foreach ($rows as $r) : ?>
			<tr><td><a href="<?= e(url('/admin/submissions/' . $r['id'])) ?>"><?= e(date('d/m/Y H:i', strtotime($r['created_at']))) ?></a></td><td><?= e(fck_contact_subjects()[$r['subject']] ?? $r['subject']) ?></td><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($labels[$r['delivery']] ?? $r['delivery']) ?></td></tr>
		<?php endforeach; ?>
	</tbody>
</table>
