<?php
declare(strict_types=1);
/**
 * Configurações globais (registros únicos com histórico de versões).
 * - site: marca, botões do menu/rodapé, contatos, SEO padrão, cookies.
 * - ui:   microtextos de interface (rótulos, mensagens, acessibilidade).
 * Menus (principal, rodapé e links legais) ficam no grupo "Menus".
 */


function fck_global_keys(): array {
	return array(
		'site' => 'Marca, rodapé, contatos e SEO',
		'ui'   => 'Textos de interface',
	);
}

/**
 * Microtextos de interface: grupo => [campo => [rótulo, limite, ajuda]].
 * Tokens entre chaves ({n}, {total}, {label}, {max}) são substituídos pelo site.
 */
function fck_ui_definition(): array {
	return array(
		'a11y'     => array(
			'_title'              => 'Acessibilidade e navegação',
			'skip_to_content'     => array( 'Pular para o conteúdo', 60 ),
			'home_link'           => array( 'Link do logo (leitor de tela)', 80 ),
			'main_nav'            => array( 'Nome da navegação principal', 60 ),
			'footer_nav'          => array( 'Nome da navegação do rodapé', 60 ),
			'open_menu'           => array( 'Abrir menu', 40 ),
			'close_menu'          => array( 'Fechar menu', 40 ),
			'open_submenu'        => array( 'Abrir submenu', 60, 'Use {label} para o nome do item.' ),
			'back_to_top'         => array( 'Voltar ao topo', 40 ),
			'next_project'        => array( 'Próximo projeto', 40 ),
			'previous_project'    => array( 'Projeto anterior', 40 ),
			'next_article'        => array( 'Próximo artigo', 40 ),
			'previous_article'    => array( 'Artigo anterior', 40 ),
			'view_all_publications' => array( 'Ver todas as publicações', 60 ),
			'pause'               => array( 'Pausar movimento', 40 ),
			'play'                => array( 'Retomar movimento', 40 ),
			'previous'            => array( 'Anterior', 40 ),
			'next'                => array( 'Próximo', 40 ),
			'go_to_item'          => array( 'Ir para item', 60, 'Use {n} e {total}.' ),
			'external_link'       => array( 'Aviso de link externo', 60 ),
			'loading'             => array( 'Carregando', 40 ),
			'breadcrumb'          => array( 'Nome da trilha de navegação', 60 ),
			'logos_region'        => array( 'Nome da faixa de logos (leitor de tela)', 60 ),
			'illustrative'        => array( 'Selo de conteúdo ilustrativo', 40 ),
			'scroll_hint'         => array( 'Dica de rolagem/arraste', 80 ),
			'progress'            => array( 'Progresso', 60, 'Use {n} e {total}.' ),
			'deploy_label'        => array( 'Rótulo da versão publicada (rodapé)', 40, 'Ex.: Versão. Use {commit} e {data}. Vazio = não exibe.' ),
		),
		'form'     => array(
			'_title'           => 'Formulários',
			'required'         => array( 'Campo obrigatório', 120 ),
			'invalid_email'    => array( 'E-mail inválido', 120 ),
			'too_long'         => array( 'Texto muito longo', 120, 'Use {max}.' ),
			'consent_required' => array( 'Consentimento obrigatório', 120 ),
			'send_error'       => array( 'Falha no envio', 160 ),
			'rate_limited'     => array( 'Muitas tentativas', 160 ),
			'sending'          => array( 'Enviando', 80 ),
			'error_summary'    => array( 'Resumo de erros', 120 ),
			'optional'         => array( 'Indicação de campo opcional', 40 ),
			'step_of'          => array( 'Etapa X de Y', 40, 'Use {n} e {total}.' ),
		),
		'cookies'  => array(
			'_title'                => 'Banner de cookies',
			'title'                 => array( 'Título', 80 ),
			'text'                  => array( 'Texto', 400 ),
			'accept_all'            => array( 'Aceitar todos', 40 ),
			'reject_optional'       => array( 'Recusar opcionais', 40 ),
			'customize'             => array( 'Personalizar preferências', 40 ),
			'preferences_title'     => array( 'Título da central de preferências', 80 ),
			'necessary'             => array( 'Cookies necessários', 60 ),
			'necessary_description' => array( 'Descrição — necessários', 300 ),
			'analytics'             => array( 'Cookies de análise', 60 ),
			'analytics_description' => array( 'Descrição — análise', 300 ),
			'marketing'             => array( 'Cookies de marketing', 60 ),
			'marketing_description' => array( 'Descrição — marketing', 300 ),
			'always_on'             => array( 'Selo "sempre ativos"', 40 ),
			'save'                  => array( 'Salvar preferências', 40 ),
			'policy_link'           => array( 'Texto do link da política', 60 ),
			'open_preferences'      => array( 'Link para reabrir preferências (rodapé)', 60 ),
		),
		'notfound' => array(
			'_title' => 'Página não encontrada (404)',
			'title'  => array( 'Título', 120 ),
			'text'   => array( 'Texto', 300 ),
			'cta'    => array( 'Botão', 40 ),
		),
		'error'    => array(
			'_title' => 'Erro temporário',
			'title'  => array( 'Título', 120 ),
			'text'   => array( 'Texto', 300 ),
			'retry'  => array( 'Botão', 40 ),
		),
		'content'  => array(
			'_title'         => 'Artigos e listagens',
			'reading_time'   => array( 'Tempo de leitura', 40, 'Use {n} para os minutos.' ),
			'by'             => array( 'Prefixo do autor', 20 ),
			'share_article'  => array( 'Compartilhar artigo', 40 ),
			'copy_link'      => array( 'Copiar link', 40 ),
			'link_copied'    => array( 'Link copiado', 40 ),
			'share_linkedin' => array( 'Compartilhar no LinkedIn', 60 ),
			'share_whatsapp' => array( 'Compartilhar no WhatsApp', 60 ),
			'share_email'    => array( 'Compartilhar por e-mail', 60 ),
			'filter_by'      => array( 'Nome do grupo de filtros', 60 ),
			'filter_by_sector' => array( 'Nome do grupo de filtros por setor (Cases)', 60 ),
			'results_count'  => array( 'Quantidade de resultados', 60, 'Use {n}.' ),
		),
		'platform' => array(
			'_title'            => 'Páginas de plataforma',
			'how_link'          => array( 'Botão "Ver como funciona" (hero)', 40 ),
			'how_title'         => array( 'Rótulo do bloco "Como funciona"', 40 ),
			'benefits_title'    => array( 'Rótulo do bloco "Benefícios e diferenciais"', 60 ),
			'interface_title'   => array( 'Rótulo do bloco "Interface"', 40 ),
			'connections_title' => array( 'Rótulo do bloco "Conexões no ecossistema"', 60 ),
			'connection_link'   => array( 'Link para a plataforma relacionada', 60, 'Use {platform} para o nome.' ),
			'independence'      => array( 'Nota de independência (bloco Conexões)', 300 ),
			'skip_animation'    => array( 'Link "Pular animação"', 40 ),
			'claim_badge'       => array( 'Selo interno "A validar" (só no preview)', 30 ),
		),
	);
}

function fck_global_schemas(): array {
	$ui_groups = array();
	foreach ( fck_ui_definition() as $group => $defs ) {
		$title  = $defs['_title'];
		$fields = array();
		unset( $defs['_title'] );
		foreach ( $defs as $name => $def ) {
			$extra    = isset( $def[2] ) ? array( 'help' => $def[2] ) : array();
			$fields[] = $def[1] > 160 ? fck_textarea( $name, $def[0], $def[1], $extra ) : fck_text( $name, $def[0], $def[1], $extra );
		}
		$ui_groups[] = array( 'id' => 'ui_' . $group, 'title' => $title, 'fields' => $fields );
	}

	return array(
		'site' => array(
			array(
				'id'          => 'brand',
				'title'       => 'Marca',
				'description' => 'Enquanto não houver logo oficial, o site exibe o nome em texto (wordmark neutro). Nunca use logo recriado.',
				'fields'      => array(
					fck_text( 'site_name', 'Nome do site', 80, array( 'required' => true ) ),
					fck_text( 'signature', 'Assinatura institucional', 120, array( 'help' => 'Usada no hero da Home quando o título alternativo da Home estiver vazio.' ) ),
					fck_image( 'logo_dark', 'Logo para fundo escuro (SVG oficial)', array( 'width' => 'half' ) ),
					fck_image( 'logo_light', 'Logo para fundo claro (SVG oficial)', array( 'width' => 'half' ) ),
					fck_image( 'logo_compact_dark', 'Logo compacto para fundo escuro (SVG oficial)', array( 'width' => 'half', 'help' => 'Versão curta usada no cabeçalho. Vazio = usa o logo completo.' ) ),
					fck_image( 'logo_compact_light', 'Logo compacto para fundo claro (SVG oficial)', array( 'width' => 'half' ) ),
				),
			),
			array(
				'id'          => 'menu',
				'title'       => 'Menus',
				'description' => 'Links para páginas acompanham mudanças de endereço. Itens que apontam para conteúdo não publicado não aparecem.',
				'fields'      => array(
					fck_f(
						'repeater',
						'main',
						'Menu principal',
						array(
							'max_rows'  => 8,
							'row_label' => 'Adicionar item',
							'fields'    => array(
								fck_link_field( 'link', 'Item' ),
								fck_f( 'repeater', 'children', 'Submenu', array( 'max_rows' => 10, 'row_label' => 'Adicionar subitem', 'fields' => array( fck_link_field( 'link', 'Subitem' ) ) ) ),
							),
						)
					),
					fck_link_field( 'cta', 'Botão do menu (ex.: Vamos conversar)' ),
					fck_f(
						'repeater',
						'footer_groups',
						'Rodapé — grupos de links',
						array(
							'max_rows'  => 6,
							'row_label' => 'Adicionar grupo',
							'fields'    => array(
								fck_text( 'title', 'Título do grupo', 60 ),
								fck_f( 'repeater', 'links', 'Links', array( 'max_rows' => 12, 'row_label' => 'Adicionar link', 'fields' => array( fck_link_field( 'link', 'Link' ) ) ) ),
							),
						)
					),
					fck_f( 'repeater', 'legal', 'Links legais (rodapé)', array( 'max_rows' => 4, 'row_label' => 'Adicionar link', 'fields' => array( fck_link_field( 'link', 'Link' ) ) ) ),
				),
			),
			array(
				'id'          => 'footer',
				'title'       => 'Rodapé',
				'description' => 'Os links do rodapé ficam no grupo "Menus".',
				'fields'      => array(
					fck_text( 'tagline', 'Assinatura', 120 ),
					fck_textarea( 'text', 'Texto', 300 ),
					fck_link_field( 'cta', 'Botão' ),
					fck_text( 'copyright', 'Direitos autorais', 120, array( 'help' => 'Use {ano} para o ano atual.' ) ),
				),
			),
			array(
				'id'          => 'contacts',
				'title'       => 'Contatos e redes',
				'description' => 'Somente canais oficiais. Campos vazios não aparecem no site.',
				'fields'      => array(
					fck_f( 'email', 'email', 'E-mail público', array( 'width' => 'half' ) ),
					fck_text( 'phone', 'Telefone', 30, array( 'width' => 'half' ) ),
					fck_textarea( 'address', 'Endereço (opcional)', 200 ),
					fck_f(
						'repeater',
						'socials',
						'Redes sociais',
						array(
							'max_rows'  => 8,
							'row_label' => 'Adicionar rede',
							'fields'    => array(
								fck_text( 'label', 'Nome exibido', 40, array( 'required' => true ) ),
								fck_f( 'url', 'url', 'URL (https://)', array( 'required' => true ) ),
							),
						)
					),
				),
			),
			array(
				'id'     => 'seo_defaults',
				'title'  => 'SEO padrão',
				'fields' => array(
					fck_text( 'default_title', 'Título padrão', 70 ),
					fck_text( 'title_template', 'Modelo de título', 40, array( 'help' => 'Use %s para o título da página (ex.: %s | Full Connect Key). Vazio = só o título.' ) ),
					fck_textarea( 'default_description', 'Descrição padrão', 170 ),
					fck_image( 'default_image', 'Imagem de compartilhamento padrão (1200×630)' ),
				),
			),
			array(
				'id'          => 'cookies',
				'title'       => 'Cookies',
				'description' => 'Hoje o site usa apenas o essencial e não grava cookies opcionais. Decisão a validar com o jurídico.',
				'fields'      => array(
					fck_f(
						'select',
						'banner_mode',
						'Exibição do banner',
						array(
							'no_empty' => true,
							'options'  => array(
								'auto'   => 'Automático — só quando houver ferramentas opcionais aprovadas',
								'always' => 'Sempre exibir',
								'never'  => 'Nunca exibir',
							),
						)
					),
				),
			),
		),
		'ui'   => $ui_groups,
	);
}
