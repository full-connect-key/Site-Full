<?php
declare(strict_types=1);
/**
 * Campos das páginas, por modelo. Cada grupo vira um meta box no editor.
 */


function fck_template_schemas(): array {
	$hero = static fn( bool $alt = false, bool $eyebrow = false ): array => array(
		'id'     => 'hero',
		'title'  => 'Hero',
		'fields' => array_values(
			array_filter(
				array(
					fck_text( 'headline', 'Título principal', 120, array( 'required' => true ) ),
					$alt ? fck_text( 'headline_alt', 'Título alternativo', 120, array( 'help' => 'Opcional. Alterna ou complementa o título principal.' ) ) : null,
					fck_textarea( 'lead', 'Texto de apoio', 700 ),
					$eyebrow ? fck_text( 'eyebrow', 'Chamada (ex.: Explore nossos projetos)', 60 ) : null,
				)
			)
		),
	);

	return array(
		// -----------------------------------------------------------------
		'home'      => array(
			array(
				'id'          => 'hero',
				'title'       => '1. Hero',
				'description' => 'Os dois títulos se alternam verticalmente. Ambos ficam acessíveis a leitores de tela.',
				'fields'      => array(
					fck_text( 'headline', 'Título principal', 120, array( 'required' => true ) ),
					fck_text( 'headline_alt', 'Título alternativo', 120 ),
					fck_textarea( 'lead', 'Texto de apoio', 400 ),
					fck_link_field(),
				),
			),
			array(
				'id'          => 'logos',
				'title'       => '2. Faixa de logos',
				'description' => 'Sem título. Mostra só clientes com autorização de exibição e logo cadastrados (menu Clientes). Sem logos autorizados, a faixa não aparece.',
				'fields'      => array( fck_enabled() ),
			),
			array(
				'id'          => 'credentials',
				'title'       => '2a. Faixa de credenciais',
				'description' => 'Faixa contínua com as plataformas em que a agência é credenciada (menu Credenciais). Sem credenciais publicadas, a faixa não aparece.',
				'fields'      => array( fck_enabled(), fck_text( 'eyebrow', 'Chamada acima do título', 40 ), fck_text( 'title', 'Título', 120 ) ),
			),
			array(
				'id'     => 'essence',
				'title'  => '3. Nossa Essência',
				'fields' => array(
					fck_enabled(),
					fck_text( 'title', 'Título', 120 ),
					fck_textarea( 'intro', 'Introdução', 700 ),
					fck_f(
						'repeater',
						'pillars',
						'Colunas',
						array(
							'max_rows'  => 3,
							'row_label' => 'Adicionar coluna',
							'fields'    => array(
								fck_text( 'name', 'Nome (ex.: Conecta)', 30, array( 'required' => true ) ),
								fck_textarea( 'text', 'Texto', 300 ),
							),
						)
					),
				),
			),
			array(
				'id'          => 'ecosystem',
				'title'       => '4. Ecossistema',
				'description' => 'Nome, verbo e linha de cada plataforma são editados no menu Plataformas (ordem = campo Ordem).',
				'fields'      => array( fck_enabled(), fck_text( 'title', 'Título', 120 ), fck_textarea( 'text', 'Texto de apoio', 500 ), fck_link_field() ),
			),
			array(
				'id'          => 'sectors',
				'title'       => '5. Setores',
				'description' => 'Os setores são editados no menu Setores.',
				'fields'      => array( fck_enabled(), fck_text( 'title', 'Título', 120 ), fck_textarea( 'intro', 'Introdução (aparece sem setor selecionado)', 400 ) ),
			),
			array(
				'id'          => 'cases',
				'title'       => '6. Cases',
				'description' => 'Mostra os cases marcados como destaque na Home (posições 1 a 3). Indicadores só aparecem com métricas reais validadas.',
				'fields'      => array( fck_enabled(), fck_text( 'title', 'Título', 120 ), fck_textarea( 'text', 'Texto de apoio', 500 ), fck_link_field() ),
			),
			array(
				'id'          => 'partners',
				'title'       => '7. Os Sócios',
				'description' => 'Aparece somente com os três sócios cadastrados (menu Sócios).',
				'fields'      => array( fck_enabled(), fck_text( 'title', 'Título', 120 ), fck_textarea( 'text', 'Texto de apoio', 500 ) ),
			),
			array(
				'id'          => 'testimonials',
				'title'       => '8. Depoimentos',
				'description' => 'Aparece somente com depoimentos autorizados (menu Depoimentos).',
				'fields'      => array( fck_enabled(), fck_text( 'title', 'Título', 120 ), fck_textarea( 'text', 'Texto de apoio', 500 ) ),
			),
			array(
				'id'          => 'team',
				'title'       => '9. O Time',
				'description' => 'Aparece somente com pessoas cadastradas (menu Time).',
				'fields'      => array( fck_enabled(), fck_text( 'title', 'Título', 120 ), fck_textarea( 'text', 'Texto de apoio', 500 ) ),
			),
			array(
				'id'          => 'insights',
				'title'       => '10. Insights',
				'description' => 'Aparece somente com artigos publicados.',
				'fields'      => array( fck_enabled(), fck_text( 'title', 'Título', 120 ), fck_textarea( 'text', 'Texto de apoio', 500 ), fck_link_field() ),
			),
			array(
				'id'     => 'footer_cta',
				'title'  => '11. CTA final',
				'fields' => array( fck_enabled(), fck_text( 'title', 'Título', 120 ), fck_textarea( 'text', 'Texto', 300 ), fck_link_field() ),
			),
			fck_seo_group(),
		),

		// -----------------------------------------------------------------
		'sm'        => array(
			$hero(),
			array(
				'id'          => 'method',
				'title'       => 'Narrativa do método',
				'description' => 'Movimentos do método (ex.: Entender, Conectar, Amplificar). Não são fases numeradas de um processo formal.',
				'fields'      => array(
					fck_text( 'title', 'Título', 120 ),
					fck_textarea( 'intro', 'Introdução', 600 ),
					fck_f(
						'repeater',
						'steps',
						'Movimentos',
						array(
							'max_rows'  => 5,
							'row_label' => 'Adicionar movimento',
							'fields'    => array(
								fck_text( 'name', 'Nome', 30, array( 'required' => true ) ),
								fck_text( 'headline', 'Frase de destaque', 160 ),
								fck_textarea( 'text', 'Texto', 600 ),
							),
						)
					),
				),
			),
			fck_cta_group(),
			fck_seo_group(),
		),

		// -----------------------------------------------------------------
		'ecosystem' => array(
			$hero( true ),
			array(
				'id'     => 'solutions',
				'title'  => 'Soluções (navegação interativa)',
				'fields' => array(
					fck_text( 'title', 'Destaque (título da seção)', 200 ),
					fck_textarea( 'intro', 'Jornada (texto de transição)', 600 ),
					fck_text( 'center_label', 'Rótulo do centro do diagrama', 30, array( 'help' => 'Ex.: Seu negócio.' ) ),
					fck_text( 'link_label', 'Link de cada card', 40, array( 'help' => 'Use {platform} para o nome da plataforma. Ex.: Explore o {platform}' ) ),
					fck_text( 'connects_label', 'Rótulo "Como se conecta"', 40 ),
					fck_textarea( 'independence', 'Nota de independência (abaixo da navegação)', 300 ),
					fck_textarea( 'method_note', 'Linha do método S&M', 300 ),
					fck_f( 'posts', 'platforms', 'Plataformas (ordem)', array( 'post_type' => 'platform', 'help' => 'Vazio = todas as plataformas publicadas, na ordem do menu Plataformas.' ) ),
				),
			),
			fck_cta_group(),
			fck_seo_group(),
		),

		// -----------------------------------------------------------------
		'cases'     => array(
			$hero( false, true ),
			array(
				'id'     => 'listing',
				'title'  => 'Listagem de cases',
				'fields' => array(
					fck_text( 'filter_all', 'Filtro "Todos"', 30 ),
					fck_text( 'view_label', 'Botão "Ver projeto"', 30 ),
					fck_text( 'load_more', 'Botão "Carregar mais"', 40 ),
					fck_textarea( 'empty', 'Texto quando não há cases', 200 ),
					fck_f( 'number', 'page_size', 'Cases por carregamento', array( 'default' => 9, 'min' => 3, 'maxValue' => 24 ) ),
				),
			),
			array(
				'id'          => 'case',
				'title'       => 'Textos da página de cada case',
				'description' => 'Valem para todos os cases.',
				'fields'      => array(
					fck_text( 'challenge_label', 'Título "O desafio"', 40, array( 'width' => 'half' ) ),
					fck_text( 'idea_label', 'Título "A ideia"', 40, array( 'width' => 'half' ) ),
					fck_text( 'approach_label', 'Título "Nossa atuação"', 40, array( 'width' => 'half' ) ),
					fck_text( 'gallery_title', 'Título da galeria', 120 ),
					fck_text( 'results_title', 'Título dos resultados', 160 ),
					fck_text( 'next_title', 'Título "próximo case"', 120 ),
					fck_text( 'next_label', 'Link "Conhecer próximo projeto"', 40 ),
				),
			),
			fck_cta_group(),
			fck_seo_group(),
		),

		// -----------------------------------------------------------------
		'insights'  => array(
			$hero( false, true ),
			array(
				'id'     => 'listing',
				'title'  => 'Destaque e listagem',
				'fields' => array(
					fck_text( 'featured_label', 'Rótulo "Em destaque"', 30 ),
					fck_text( 'others_label', 'Rótulo "Outras perspectivas"', 40 ),
					fck_text( 'read_label', 'Botão "Ler artigo"', 30 ),
					fck_text( 'grid_title', 'Título do grid', 120 ),
					fck_textarea( 'grid_text', 'Texto do grid', 300 ),
					fck_text( 'filter_all', 'Filtro "Todos"', 30 ),
					fck_text( 'continue_label', 'Link "Continuar lendo"', 30 ),
					fck_text( 'load_more', 'Botão "Carregar mais"', 40 ),
					fck_textarea( 'empty', 'Texto quando não há artigos', 200 ),
					fck_f( 'number', 'page_size', 'Artigos por carregamento', array( 'default' => 9, 'min' => 3, 'maxValue' => 24 ) ),
				),
			),
			array(
				'id'     => 'article',
				'title'  => 'Textos da página do artigo',
				'fields' => array(
					fck_text( 'related_title', 'Título dos relacionados', 120 ),
					fck_textarea( 'related_text', 'Texto dos relacionados', 300 ),
					fck_link_field( 'related_cta', 'Link "Ver mais insights"' ),
				),
			),
			fck_cta_group(),
			fck_seo_group(),
		),

		// -----------------------------------------------------------------
		'press'     => array(
			$hero( false, true ),
			array(
				'id'     => 'archive',
				'title'  => 'Matérias',
				'fields' => array(
					fck_text( 'title', 'Título', 120 ),
					fck_textarea( 'text', 'Texto', 300 ),
					fck_text( 'read_original', 'Botão "Ler publicação original"', 40 ),
					fck_text( 'others_label', 'Rótulo "Outras publicações"', 40 ),
					fck_text( 'archive_title', 'Título do arquivo ("Nossa presença na mídia")', 60 ),
					fck_text( 'access_label', 'Link "Acessar matéria"', 30 ),
					fck_text( 'load_more', 'Botão "Ver mais publicações"', 40 ),
					fck_text( 'filter_all', 'Filtro "Todas"', 30 ),
					fck_text( 'filter_interview', 'Filtro "Entrevistas"', 30 ),
					fck_text( 'filter_report', 'Filtro "Reportagens"', 30 ),
					fck_text( 'filter_article', 'Filtro "Artigos"', 30 ),
					fck_textarea( 'empty', 'Texto quando não há publicações', 200 ),
					fck_f( 'number', 'threshold', 'Mínimo de matérias para exibir arquivo e filtros', array( 'default' => 6, 'min' => 1, 'maxValue' => 50 ) ),
				),
			),
			fck_cta_group(),
			fck_seo_group(),
		),

		// -----------------------------------------------------------------
		'contact'   => array(
			$hero( false, true ),
			array(
				'id'     => 'form',
				'title'  => 'Formulário',
				'fields' => array(
					fck_heading( 'Etapa 1 — assunto' ),
					fck_text( 'step1_title', 'Título', 120 ),
					fck_textarea( 'step1_text', 'Texto', 300 ),
					fck_f(
						'repeater',
						'subjects',
						'Opções de assunto',
						array(
							'max_rows'  => 4,
							'row_label' => 'Adicionar opção',
							'fields'    => array(
								fck_f( 'select', 'value', 'Assunto', array( 'options' => fck_contact_subjects(), 'required' => true ) ),
								fck_text( 'title', 'Título da opção', 80, array( 'required' => true ) ),
								fck_text( 'description', 'Descrição', 120 ),
							),
						)
					),
					fck_text( 'continue', 'Botão "Continuar"', 30 ),
					fck_heading( 'Etapa 2 — dados' ),
					fck_text( 'step2_title', 'Título', 120 ),
					fck_textarea( 'step2_text', 'Texto', 300 ),
					fck_text( 'name_label', 'Rótulo — nome', 40, array( 'width' => 'half' ) ),
					fck_text( 'name_placeholder', 'Exemplo — nome', 60, array( 'width' => 'half' ) ),
					fck_text( 'email_label', 'Rótulo — e-mail', 40, array( 'width' => 'half' ) ),
					fck_text( 'email_placeholder', 'Exemplo — e-mail', 60, array( 'width' => 'half' ) ),
					fck_text( 'company_label', 'Rótulo — empresa', 40, array( 'width' => 'half' ) ),
					fck_text( 'company_placeholder', 'Exemplo — empresa', 60, array( 'width' => 'half' ) ),
					fck_text( 'message_label', 'Rótulo — mensagem', 40, array( 'width' => 'half' ) ),
					fck_text( 'message_placeholder', 'Exemplo — mensagem', 80, array( 'width' => 'half' ) ),
					fck_textarea( 'consent', 'Texto de consentimento', 400, array( 'help' => 'Revisão jurídica obrigatória antes de publicar.' ) ),
					fck_text( 'consent_link', 'Trecho do consentimento que vira link para a Política de Privacidade', 60 ),
					fck_text( 'back', 'Botão "Voltar"', 30, array( 'width' => 'half' ) ),
					fck_text( 'submit', 'Botão "Enviar"', 30, array( 'width' => 'half' ) ),
					fck_heading( 'Sucesso' ),
					fck_text( 'success_title', 'Título', 120 ),
					fck_textarea( 'success_text', 'Texto', 300 ),
					fck_text( 'success_cta', 'Botão "Voltar para a Home"', 40 ),
				),
			),
			fck_seo_group(),
		),

		// -----------------------------------------------------------------
		'legal'     => array(
			array(
				'id'          => 'legal',
				'title'       => 'Texto jurídico',
				'description' => 'Publicar somente após aprovação jurídica. Enquanto estiver em rascunho, os links do rodapé ficam ocultos.',
				'fields'      => array(
					fck_text( 'title', 'Título exibido', 120, array( 'required' => true ) ),
					fck_f( 'richtext', 'body', 'Texto', array( 'required' => true ) ),
					fck_text( 'updated_label', 'Rótulo "Atualizado em"', 40, array( 'width' => 'half' ) ),
					fck_text( 'updated_on', 'Data (dd/mm/aaaa)', 10, array( 'width' => 'half' ) ),
				),
			),
			fck_seo_group(),
		),
	);
}
