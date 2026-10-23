<?php defined('FCK_ROOT') || exit; $opts = ['all' => 'Todos os assuntos'] + fck_contact_subjects(); $rows = array_values((array) $s['recipients']); $rows[] = ['subject' => 'all', 'email' => '']; ?>
<h1>Formulário de contato</h1>
<p class="adm-muted">Sem destinatário (ou sem SMTP em app/config.php), as mensagens ficam salvas apenas em "Mensagens de contato".</p>
<?php if (!mail_configured()) : ?><div class="adm-notice adm-notice--warning"><p>SMTP não configurado: nenhum e-mail será enviado até preencher <code>smtp</code> em app/config.php.</p></div><?php endif; ?>
<form method="post" class="adm-form">
	<?= csrf_field() ?>
	<table class="adm-table"><thead><tr><th>Assunto</th><th>E-mail de destino</th></tr></thead><tbody>
		<?php foreach ($rows as $i => $r) : ?>
			<tr><td><select name="recipients[<?= $i ?>][subject]" aria-label="Assunto"><?php foreach ($opts as $k => $l) : ?><option value="<?= e($k) ?>"<?= ($r['subject'] ?? '') === $k ? ' selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></td>
			<td><input type="email" name="recipients[<?= $i ?>][email]" value="<?= e($r['email'] ?? '') ?>" aria-label="E-mail"></td></tr>
		<?php endforeach; ?>
	</tbody></table>
	<p class="fck-help">Para remover, apague o e-mail. Uma linha vazia é adicionada a cada gravação.</p>
	<label for="ret">Apagar mensagens após (meses)</label>
	<input id="ret" class="fck-small" type="number" min="1" max="60" name="retention_months" value="<?= (int) $s['retention_months'] ?>">
	<p class="fck-help">Padrão sugerido: 12 meses — decisão a validar com o jurídico.</p>
	<button class="btn-admin btn-primary" type="submit">Salvar</button>
</form>
