<?php
/**
 * Pendências de conteúdo detectadas no banco (espelha docs/CONTENT_GAPS.md).
 */

declare(strict_types=1);

function content_gaps(): array
{
	$gaps = [];
	$add  = function (string $p, string $item, string $where) use (&$gaps): void {
		$gaps[] = ['priority' => $p, 'item' => $item, 'where' => $where];
	};
	$count = fn(string $type) => (int) db_value('SELECT COUNT(*) FROM entries WHERE type = ? AND published IS NOT NULL', [$type]);

	$brand = site('brand');
	if (empty($brand['logo_dark']) || empty($brand['logo_light'])) {
		$add('P0', 'Logos oficiais da Full (SVG)', 'Cabeçalho e rodapé (hoje: nome em texto)');
	}
	foreach (db_all("SELECT id, title, draft FROM entries WHERE type = 'platform'") as $p) {
		$id = json_dec($p['draft'])['identity'] ?? [];
		if (empty($id['logo']) || empty($id['logo_light'])) {
			$add('P0', 'Logo oficial (escuro e claro) — ' . $p['title'], 'Páginas do ecossistema');
		}
	}
	foreach (pending_claims() as $c) {
		$add('P0', 'Afirmação a validar — ' . $c['platform'] . ' › ' . $c['where'] . ': "' . mb_strimwidth($c['text'], 0, 80, '…') . '"' . ($c['note'] !== '' ? ' (' . $c['note'] . ')' : ''), 'Página da plataforma (oculta em produção)');
	}
	foreach ((array) setting('eco_migration_conflicts', []) as $path) {
		$add('Revisar', 'Edição do painel preservada na migração do Ecossistema: ' . $path, 'Comparar com docs/BRIEFING_ECOSSISTEMA.md');
	}
	foreach (['privacy' => 'Política de Privacidade', 'cookies' => 'Política de Cookies'] as $k => $label) {
		if (!db_value('SELECT id FROM entries WHERE seed_key = ? AND published IS NOT NULL', ['page:' . $k])) {
			$add('P0', $label . ' (texto jurídico aprovado)', 'Rodapé e consentimento do formulário');
		}
	}
	if (!contact_settings()['recipients']) {
		$add('P0', 'Destinatários do formulário de contato', 'Contato (mensagens ficam só no painel)');
	}
	if (!mail_configured()) {
		$add('P0', 'SMTP para envio de e-mails (app/config.php)', 'Contato (mensagens ficam só no painel)');
	}
	if ($count('partner') < 3) {
		$add('P1', 'Sócios: retrato vertical de Rita Frare, Emerson Aguiar e Felipe Pelá (cargo e bio já cadastrados, em rascunho — adicionar a foto e publicar)', 'Home › Os Sócios (seção oculta)');
	}
	if (!$count('team')) {
		$add('P1', 'Time (fotos e frases "Conecta X a Y.")', 'Home › O Time (seção oculta)');
	}
	if (!$count('testimonial')) {
		$add('P1', 'Depoimentos autorizados', 'Home › Depoimentos (seção oculta)');
	}
	if (!$count('case')) {
		$add('P1', 'Cases com KVs autorizados', 'Home › Cases e página Cases');
	}
	if (!$count('client')) {
		$add('P1', 'Logos de clientes autorizados', 'Home › faixa de logos (oculta)');
	}
	if (!$count('article')) {
		$add('P2', 'Artigos reais', 'Home › Insights e página Insights');
	}
	if (!$count('press')) {
		$add('P2', 'Matérias de imprensa reais', 'Full na Mídia (estado vazio)');
	}
	$c = site('contacts');
	if (empty($c['email']) && empty($c['phone']) && empty($c['socials'])) {
		$add('P2', 'Canais oficiais de contato e redes', 'Rodapé');
	}
	return $gaps;
}

/**
 * Afirmações "a validar" publicadas nas plataformas (docs/CLAIMS_REGISTER.md).
 * @return list<array{platform:string,where:string,text:string,note:string}>
 */
function pending_claims(): array
{
	$out = [];
	foreach (db_all("SELECT title, published FROM entries WHERE type = 'platform' AND published IS NOT NULL ORDER BY menu_order") as $p) {
		$d    = json_dec($p['published']);
		$name = (string) $p['title'];
		$push = function (string $where, array $item, string $text, string $status = 'claim_status', string $note = 'claim_note') use (&$out, $name): void {
			if (($item[$status] ?? '') === 'validar') {
				$out[] = ['platform' => $name, 'where' => $where, 'text' => str_replace('{platform}', $name, $text), 'note' => (string) ($item[$note] ?? '')];
			}
		};
		foreach ((array) ($d['how']['steps'] ?? []) as $s) {
			$push('Como funciona', (array) $s, (string) ($s['title'] ?? '') . ': ' . ($s['text'] ?? ''));
		}
		foreach ((array) ($d['benefits']['items'] ?? []) as $b) {
			$push('Benefícios', (array) $b, (string) ($b['title'] ?? '') . ': ' . ($b['text'] ?? ''));
		}
		if (!empty($d['benefits']['note'])) {
			$push('Nota dos benefícios', (array) $d['benefits'], (string) $d['benefits']['note'], 'note_claim_status', 'note_claim_note');
		}
		foreach ((array) ($d['connections']['items'] ?? []) as $c) {
			$push('Conexões', (array) $c, (string) ($c['text'] ?? ''));
		}
	}
	return $out;
}
