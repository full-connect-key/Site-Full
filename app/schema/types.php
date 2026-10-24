<?php
declare(strict_types=1);
/**
 * Campos de cada tipo de conteúdo.
 */


function fck_type_schemas(): array {
	return array(
		'platform'    => array(
			array(
				'id'          => 'identity',
				'title'       => 'Identidade, Home e Ecossistema',
				'description' => 'O nome da plataforma é o título do registro. Nos textos, use {platform} para citar o próprio nome: ao renomear, tudo acompanha (site, CTAs e SEO).',
				'fields'      => array(
					fck_f( 'select', 'product', 'Produto (define a cor oficial e a interação)', array( 'options' => fck_product_keys(), 'required' => true, 'help' => 'A cor oficial vem dos tokens da marca e não é editável.' ) ),
					fck_text( 'verb', 'Verbo (ex.: Planejar)', 30, array( 'width' => 'half' ) ),
					fck_image( 'logo', 'Logo oficial para fundo escuro (SVG)', array( 'width' => 'half', 'help' => 'Sem logo oficial, o site exibe o nome em texto (wordmark neutro).' ) ),
					fck_image( 'logo_light', 'Logo oficial para fundo claro (SVG)', array( 'width' => 'half' ) ),
					fck_text( 'home_line', 'Linha na Home (nome + verbo + uma linha)', 160 ),
					fck_text( 'card_title', 'Card no Ecossistema — título', 120 ),
					fck_textarea( 'card_text', 'Card no Ecossistema — texto (2 frases)', 400 ),
					fck_textarea( 'connects', 'Card no Ecossistema — "Como se conecta"', 200 ),
					fck_image( 'eco_image', 'Imagem no Ecossistema (opcional)' ),
				),
			),
			array(
				'id'          => 'hero',
				'title'       => '1. Hero',
				'description' => 'A chamada acima do título é montada sozinha: nome · verbo.',
				'fields'      => array(
					fck_text( 'headline', 'Título principal', 120, array( 'required' => true ) ),
					fck_textarea( 'lead', 'Descrição objetiva (1–2 frases)', 300 ),
					fck_text( 'cta_label', 'Botão principal (leva ao contato com a plataforma marcada)', 60 ),
					fck_f( 'media', 'media', 'Imagem ou vídeo (opcional)' ),
					fck_f(
						'repeater',
						'alt_titles',
						'Títulos alternativos (só no painel, nunca publicados)',
						array(
							'max_rows'  => 8,
							'row_label' => 'Adicionar título alternativo',
							'help'      => 'Para testar copy: copie um deles para o "Título principal" quando quiser trocar.',
							'fields'    => array( fck_text( 'title', 'Título', 120 ) ),
						)
					),
				),
			),
			array(
				'id'     => 'problem',
				'title'  => '2. Problema',
				'fields' => array( fck_enabled(), fck_text( 'title', 'Título provocativo', 140 ), fck_textarea( 'text', 'Texto curto do problema', 400 ) ),
			),
			array(
				'id'          => 'how',
				'title'       => '3. Como funciona',
				'description' => 'Os passos conduzem a animação da página (rolagem). O visual é conceitual e sempre identificado como ilustrativo.',
				'fields'      => array(
					fck_enabled(),
					fck_f( 'checkbox', 'motion', 'Animação guiada pela rolagem', array( 'default' => true, 'help' => 'Desligado: os passos aparecem empilhados, com o visual completo.' ) ),
					fck_f( 'select', 'intensity', 'Intensidade da animação', array( 'options' => array( 'default' => 'Padrão', 'soft' => 'Suave' ), 'no_empty' => true ) ),
					fck_textarea( 'intro', 'Texto institucional', 500 ),
					fck_f(
						'repeater',
						'steps',
						'Passos (2 a 4)',
						array(
							'max_rows'  => 4,
							'row_label' => 'Adicionar passo',
							'fields'    => array_merge( array( fck_text( 'title', 'Título', 60, array( 'required' => true ) ), fck_textarea( 'text', 'Texto', 400 ) ), fck_claim_fields() ),
						)
					),
				),
			),
			array(
				'id'          => 'interaction',
				'title'       => '3a. Ajustes do visual',
				'description' => 'Somente rótulos conceituais (nunca números). Preencha só o que vale para este produto.',
				'fields'      => array(
					fck_textarea( 'illustrative_note', 'Aviso de conteúdo ilustrativo', 300, array( 'help' => 'Obrigatório no FullMix (fica junto ao simulador). Nas outras, rotula os visuais; vazio = selo "Ilustrativo".' ) ),
					fck_heading( 'FullMix' ),
					fck_textarea( 'simulator_intro', 'Introdução do simulador', 300 ),
					fck_text( 'total_label', 'Rótulo do total', 40, array( 'width' => 'half' ) ),
					fck_text( 'reset_label', 'Botão "restaurar"', 40, array( 'width' => 'half' ) ),
					fck_f(
						'repeater',
						'channels',
						'Canais (ilustrativos)',
						array(
							'max_rows'  => 8,
							'row_label' => 'Adicionar canal',
							'help'      => 'Nomes de canais para a experiência ilustrativa. Sem números de vendas.',
							'fields'    => array(
								fck_text( 'name', 'Canal', 40, array( 'required' => true ) ),
								fck_f( 'select', 'kind', 'Tipo', array( 'options' => array( 'online' => 'Online', 'offline' => 'Offline' ), 'no_empty' => true ) ),
								fck_f( 'number', 'share', 'Participação inicial (%)', array( 'min' => 0, 'maxValue' => 100, 'default' => 20 ) ),
							),
						)
					),
					fck_text( 'label_sales', 'Rótulo "vendas"', 40, array( 'width' => 'half' ) ),
					fck_text( 'label_contribution', 'Rótulo "contribuição estimada"', 60, array( 'width' => 'half' ) ),
					fck_text( 'label_base', 'Rótulo da base ("o que aconteceria sem campanhas")', 80 ),
					fck_textarea( 'flows', 'Fluxos de dados (um por linha)', 200 ),
					fck_heading( 'FullDash' ),
					fck_textarea( 'selector', 'Seletor que acompanha os passos (um por linha)', 200 ),
					fck_text( 'label_planned', 'Rótulo "planejado"', 30, array( 'width' => 'half' ) ),
					fck_text( 'label_actual', 'Rótulo "realizado"', 30, array( 'width' => 'half' ) ),
					fck_text( 'label_adjust', 'Rótulo "ajuste"', 30, array( 'width' => 'half' ) ),
					fck_text( 'label_goal', 'Rótulo "objetivo do negócio"', 40, array( 'width' => 'half' ) ),
					fck_textarea( 'words', 'Palavras soltas do bloco Problema (uma por linha)', 200 ),
					fck_heading( 'FullMatch' ),
					fck_f(
						'repeater',
						'clusters',
						'Perfis (agrupamentos)',
						array(
							'max_rows'  => 3,
							'row_label' => 'Adicionar perfil',
							'fields'    => array( fck_text( 'name', 'Nome', 40, array( 'required' => true ) ), fck_text( 'text', 'Descrição curta', 160 ) ),
						)
					),
					fck_heading( 'FullHub' ),
					fck_textarea( 'columns', 'Colunas do quadro que se organiza (uma por linha)', 200 ),
				),
			),
			array(
				'id'          => 'benefits',
				'title'       => '4. Benefícios e diferenciais',
				'description' => 'Até 6 itens. Itens "A validar" nunca vão para produção; quando saem, o grid se reorganiza sem buracos.',
				'fields'      => array(
					fck_enabled(),
					fck_text( 'manifesto', 'Frase-manifesto em destaque', 160 ),
					fck_f(
						'repeater',
						'items',
						'Benefícios',
						array(
							'max_rows'  => 6,
							'row_label' => 'Adicionar benefício',
							'fields'    => array_merge( array( fck_text( 'title', 'Título', 60, array( 'required' => true ) ), fck_textarea( 'text', 'Uma frase', 240 ) ), fck_claim_fields() ),
						)
					),
					fck_textarea( 'note', 'Nota do bloco (opcional)', 300 ),
					fck_f( 'select', 'note_claim_status', 'Status da nota', array( 'options' => fck_claim_statuses(), 'no_empty' => true, 'width' => 'half' ) ),
					fck_text( 'note_claim_note', 'O que precisa ser validado (só no painel)', 200, array( 'width' => 'half' ) ),
				),
			),
			array(
				'id'          => 'interface',
				'title'       => '5. Interface',
				'description' => 'Somente capturas ou vídeos REAIS da plataforma. Sem material real, o bloco não aparece. Nunca use mockup como se fosse a interface.',
				'fields'      => array(
					fck_enabled(),
					fck_f(
						'repeater',
						'items',
						'Capturas e vídeos',
						array(
							'max_rows'  => 8,
							'row_label' => 'Adicionar captura',
							'fields'    => array(
								fck_f( 'media', 'media', 'Imagem ou vídeo', array( 'required' => true ) ),
								fck_text( 'caption', 'Legenda', 160 ),
								fck_f( 'checkbox', 'is_real', 'Confirmo que é a interface real da plataforma', array( 'help' => 'Obrigatório para o item aparecer no site.' ) ),
							),
						)
					),
				),
			),
			array(
				'id'          => 'connections',
				'title'       => '6. Conexões no ecossistema',
				'description' => 'Como esta plataforma se complementa com as outras (2 a 3 relações). O site lembra que cada solução pode ser contratada sozinha.',
				'fields'      => array(
					fck_enabled(),
					fck_textarea( 'intro', 'Texto geral (opcional)', 300 ),
					fck_f(
						'repeater',
						'items',
						'Relações',
						array(
							'max_rows'  => 4,
							'row_label' => 'Adicionar relação',
							'fields'    => array_merge( array( fck_f( 'post', 'platform', 'Plataforma relacionada', array( 'post_type' => 'platform', 'required' => true ) ), fck_textarea( 'text', 'Como se complementam', 300 ) ), fck_claim_fields() ),
						)
					),
				),
			),
			array(
				'id'          => 'closing',
				'title'       => '7. CTA final',
				'description' => 'O botão leva ao contato com o assunto "projeto" e esta plataforma pré-preenchidos.',
				'fields'      => array( fck_text( 'title', 'Título', 140 ), fck_textarea( 'text', 'Apoio', 300 ), fck_text( 'cta_label', 'Botão', 60 ) ),
			),
			fck_seo_group(),
		),

		'case'        => array(
			array(
				'id'     => 'details',
				'title'  => 'Dados do case',
				'fields' => array(
					fck_f( 'post', 'client', 'Cliente', array( 'post_type' => 'client', 'width' => 'half' ) ),
					fck_f( 'post', 'sector', 'Setor', array( 'post_type' => 'sector', 'width' => 'half' ) ),
					fck_text( 'category', 'Categoria / tipo de projeto', 60, array( 'help' => 'Ex.: Branding, Campanha, Lançamento.' ) ),
					fck_text( 'headline', 'Headline conceitual', 160 ),
					fck_textarea( 'summary', 'Resumo', 400 ),
					fck_image( 'kv', 'Key visual (KV)', array( 'help' => 'Imagem principal: listagem, Home e topo do case.' ) ),
					fck_f( 'select', 'featured', 'Destaque na Home', array( 'options' => array( '1' => '1 — grande', '2' => '2', '3' => '3' ) ) ),
				),
			),
			array(
				'id'     => 'narrative',
				'title'  => 'Narrativa',
				'fields' => array(
					fck_textarea( 'challenge', 'O desafio', 2000, array( 'rows' => 6 ) ),
					fck_textarea( 'idea', 'A ideia', 2000, array( 'rows' => 6 ) ),
					fck_textarea( 'approach', 'Nossa atuação', 2000, array( 'rows' => 6 ) ),
				),
			),
			array(
				'id'     => 'gallery',
				'title'  => 'Galeria',
				'fields' => array(
					fck_f(
						'repeater',
						'items',
						'Itens',
						array(
							'max_rows'  => 30,
							'row_label' => 'Adicionar imagem ou vídeo',
							'fields'    => array(
								fck_f( 'media', 'media', 'Imagem ou vídeo', array( 'required' => true ) ),
								fck_text( 'caption', 'Legenda', 200 ),
								fck_f(
									'select',
									'size',
									'Tamanho no grid',
									array(
										'no_empty' => true,
										'options'  => array( 'half' => 'Metade', 'full' => 'Largura total', 'third' => 'Um terço', 'two_thirds' => 'Dois terços' ),
									)
								),
							),
						)
					),
				),
			),
			array(
				'id'          => 'results',
				'title'       => 'Resultados (somente dados reais)',
				'description' => 'Vazio = bloco de resultados oculto. Nunca use números de exemplo.',
				'fields'      => array(
					fck_f(
						'repeater',
						'items',
						'Indicadores',
						array(
							'max_rows'  => 6,
							'row_label' => 'Adicionar indicador',
							'fields'    => array(
								fck_text( 'value', 'Valor', 20, array( 'required' => true ) ),
								fck_text( 'label', 'Rótulo', 80, array( 'required' => true ) ),
								fck_text( 'source', 'Fonte / observação', 160 ),
							),
						)
					),
				),
			),
			array(
				'id'     => 'next',
				'title'  => 'Próximo case',
				'fields' => array(
					fck_f( 'post', 'case', 'Próximo case (manual)', array( 'post_type' => 'case', 'help' => 'Vazio = o próximo da listagem (volta ao primeiro no fim).' ) ),
				),
			),
			fck_seo_group(),
		),

		'article'     => array(
			array(
				'id'          => 'details',
				'title'       => 'Artigo',
				'description' => 'Leitura estimada é calculada automaticamente.',
				'fields'      => array(
					fck_text( 'subtitle', 'Subtítulo', 240 ),
					fck_textarea( 'excerpt', 'Resumo (listagens e compartilhamento)', 300 ),
					fck_image( 'cover', 'Capa', array( 'width' => 'half' ) ),
					fck_f( 'post', 'category', 'Categoria', array( 'post_type' => 'category', 'width' => 'half' ) ),
					fck_text( 'date', 'Data de publicação (aaaa-mm-dd)', 10, array( 'width' => 'half', 'help' => 'Vazio = data da primeira publicação.' ) ),
					fck_f( 'richtext', 'body', 'Corpo do artigo', array( 'help' => 'Títulos, listas, citações, links, imagens e vídeos (YouTube/Vimeo).' ) ),
					fck_f( 'post', 'author', 'Autor', array( 'post_type' => 'author' ) ),
					fck_f( 'checkbox', 'featured', 'Destaque (abre o Insights e a seção da Home)' ),
					fck_f( 'posts', 'related', 'Relacionados (manual, até 3)', array( 'post_type' => 'article', 'help' => 'Vazio = mais recentes da mesma categoria.' ) ),
				),
			),
			fck_seo_group(),
		),

		'press'       => array(
			array(
				'id'          => 'details',
				'title'       => 'Matéria',
				'description' => 'O título do registro é a manchete original. Somente publicações reais.',
				'fields'      => array(
					fck_text( 'outlet', 'Veículo', 80, array( 'required' => true ) ),
					fck_image( 'outlet_logo', 'Logo do veículo (somente com direito de uso confirmado na mídia)' ),
					fck_textarea( 'summary', 'Resumo próprio', 400 ),
					fck_text( 'date', 'Data da publicação (aaaa-mm-dd)', 10, array( 'required' => true, 'width' => 'half' ) ),
					fck_f( 'select', 'kind', 'Tipo', array( 'options' => fck_press_kinds(), 'required' => true, 'width' => 'half' ) ),
					fck_f( 'url', 'url', 'URL da matéria original', array( 'required' => true ) ),
					fck_image( 'cover', 'Capa (opcional)' ),
				),
			),
		),

		'sector'      => array(
			array(
				'id'          => 'details',
				'title'       => 'Setor',
				'description' => 'O nome é o título do registro. O slug é usado no filtro de cases (?setor=...).',
				'fields'      => array( fck_text( 'title', 'Frase de destaque', 120 ), fck_textarea( 'text', 'Texto', 400 ), fck_image( 'image', 'Imagem' ) ),
			),
		),

		'client'      => array(
			array(
				'id'          => 'details',
				'title'       => 'Cliente',
				'description' => 'O logo só aparece no site com autorização de exibição registrada.',
				'fields'      => array(
					fck_image( 'logo', 'Logo (SVG ou PNG transparente)' ),
					fck_f( 'checkbox', 'authorized', 'Autorização de exibição registrada' ),
					fck_text( 'authorization_note', 'Registro da autorização (interno)', 200, array( 'help' => 'Quem autorizou, quando e onde está o registro. Não aparece no site.' ) ),
					fck_f( 'post', 'sector', 'Setor', array( 'post_type' => 'sector' ) ),
				),
			),
		),

		'metric'      => array(
			array(
				'id'          => 'details',
				'title'       => 'Métrica institucional',
				'description' => 'Apenas números verificados. Sem métricas validadas, a faixa de indicadores não aparece.',
				'fields'      => array(
					fck_text( 'value', 'Valor', 20, array( 'required' => true ) ),
					fck_text( 'label', 'Rótulo', 80, array( 'required' => true ) ),
					fck_text( 'source', 'Fonte / validação', 200, array( 'required' => true ) ),
					fck_f( 'checkbox', 'validated', 'Número validado' ),
				),
			),
		),

		'partner'     => array(
			array(
				'id'          => 'details',
				'title'       => 'Sócio',
				'description' => 'O nome é o título. A seção da Home aparece só com os três sócios publicados.',
				'fields'      => array( fck_text( 'role', 'Cargo', 80 ), fck_textarea( 'bio', 'Bio', 700 ), fck_image( 'photo', 'Retrato vertical', array( 'required' => true ) ) ),
			),
		),

		'team'        => array(
			array(
				'id'     => 'details',
				'title'  => 'Pessoa do time',
				'fields' => array(
					fck_text( 'role', 'Cargo', 80 ),
					fck_image( 'photo', 'Foto', array( 'required' => true ) ),
					fck_text( 'connects', 'Frase "Conecta X a Y."', 100, array( 'help' => 'Ex.: Conecta dados a decisões.' ) ),
				),
			),
		),

		'testimonial' => array(
			array(
				'id'          => 'details',
				'title'       => 'Depoimento',
				'description' => 'O título do registro é o nome da pessoa. Só depoimentos reais com autorização registrada aparecem.',
				'fields'      => array(
					fck_textarea( 'quote', 'Depoimento', 600, array( 'required' => true ) ),
					fck_text( 'role', 'Cargo', 80, array( 'width' => 'half' ) ),
					fck_text( 'company', 'Empresa', 80, array( 'width' => 'half' ) ),
					fck_image( 'photo', 'Foto (opcional)' ),
					fck_f( 'checkbox', 'authorized', 'Autorização registrada' ),
					fck_text( 'authorization_note', 'Registro da autorização (interno)', 200 ),
				),
			),
		),

		'category'    => array(
			array(
				'id'          => 'details',
				'title'       => 'Categoria',
				'description' => 'O nome é o título. O slug é usado no filtro (?categoria=...).',
				'fields'      => array(),
			),
		),

		'author'      => array(
			array(
				'id'     => 'details',
				'title'  => 'Autor',
				'fields' => array( fck_text( 'role', 'Cargo', 80 ), fck_image( 'photo', 'Foto' ) ),
			),
		),
	);
}
