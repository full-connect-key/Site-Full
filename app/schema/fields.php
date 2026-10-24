<?php
declare(strict_types=1);
/**
 * Atalhos para declarar campos. Limites de caracteres e textos de ajuda
 * aparecem para o editor; o site nunca trunca texto.
 */


function fck_f( string $type, string $name, string $label, array $extra = array() ): array {
	return array_merge( array( 'type' => $type, 'name' => $name, 'label' => $label ), $extra );
}

function fck_text( string $name, string $label, int $max, array $extra = array() ): array {
	return fck_f( 'text', $name, $label, array_merge( array( 'max' => $max ), $extra ) );
}

function fck_textarea( string $name, string $label, int $max, array $extra = array() ): array {
	return fck_f( 'textarea', $name, $label, array_merge( array( 'max' => $max ), $extra ) );
}

function fck_image( string $name, string $label, array $extra = array() ): array {
	return fck_f( 'image', $name, $label, $extra );
}

function fck_link_field( string $name = 'cta', string $label = 'Botão (CTA)', array $extra = array() ): array {
	return fck_f( 'link', $name, $label, $extra );
}

/** Status de uma afirmação de funcionalidade (docs/CLAIMS_REGISTER.md). */
function fck_claim_statuses(): array {
	return array( 'confirmado' => 'Confirmado', 'validar' => 'A validar (não vai para produção)' );
}

function fck_claim_fields(): array {
	return array(
		fck_f( 'select', 'claim_status', 'Status da afirmação', array( 'options' => fck_claim_statuses(), 'no_empty' => true, 'width' => 'half', 'help' => '"A validar" aparece só no preview e no staging, com selo; em produção o item é omitido.' ) ),
		fck_text( 'claim_note', 'O que precisa ser validado (só no painel)', 200, array( 'width' => 'half' ) ),
	);
}

function fck_enabled(): array {
	return fck_f( 'checkbox', 'enabled', 'Exibir esta seção', array( 'default' => true ) );
}

function fck_heading( string $label ): array {
	return array( 'type' => 'heading', 'name' => '', 'label' => $label );
}

function fck_seo_group(): array {
	return array(
		'id'          => 'seo',
		'title'       => 'SEO e compartilhamento',
		'context'     => 'normal',
		'priority'    => 'low',
		'description' => 'Como a página aparece no Google e ao ser compartilhada. Vazio = usa o padrão do site.',
		'fields'      => array(
			fck_text( 'title', 'Título (meta title)', 70, array( 'help' => 'Recomendado: 50–60.' ) ),
			fck_textarea( 'description', 'Descrição (meta description)', 170, array( 'help' => 'Recomendado: 120–160.' ) ),
			fck_image( 'image', 'Imagem de compartilhamento (1200×630)' ),
			fck_f( 'checkbox', 'noindex', 'Ocultar dos buscadores (noindex)' ),
		),
	);
}

/** Grupo "CTA final" (título, texto, botão). */
function fck_cta_group( string $title = 'Chamada final (CTA)' ): array {
	return array(
		'id'     => 'cta',
		'title'  => $title,
		'fields' => array(
			fck_enabled(),
			fck_text( 'title', 'Título', 120 ),
			fck_textarea( 'text', 'Texto', 400 ),
			fck_link_field( 'cta', 'Botão' ),
		),
	);
}

/** Opções de produto: definem cor oficial (tokens no código) e interação. */
function fck_product_keys(): array {
	return array(
		'hub'   => 'Full Hub',
		'dash'  => 'Full Dash',
		'leads' => 'Full Leads',
		'mix'   => 'FullMix',
		'match' => 'FullMatch',
	);
}

function fck_contact_subjects(): array {
	return array(
		'projeto'  => 'Projeto',
		'imprensa' => 'Imprensa',
		'parceria' => 'Parceria',
		'outro'    => 'Outro assunto',
	);
}

function fck_press_kinds(): array {
	return array(
		'interview' => 'Entrevista',
		'report'    => 'Reportagem',
		'article'   => 'Artigo',
	);
}
