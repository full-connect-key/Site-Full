<?php
/**
 * Campos de cada tipo de conteúdo.
 */

defined( 'ABSPATH' ) || exit;

function fck_post_type_schemas(): array {
	return array(
		'fck_platform'    => array(
			array(
				'id'     => 'identity',
				'title'  => 'Identidade e resumos',
				'fields' => array(
					fck_f( 'select', 'product', 'Produto (define a cor oficial e a interação)', array( 'options' => fck_product_keys(), 'required' => true, 'help' => 'A cor oficial vem dos tokens da marca e não é editável.' ) ),
					fck_image( 'logo', 'Logo oficial (SVG)', array( 'help' => 'Sem logo oficial, o site exibe o nome em texto (wordmark neutro).' ) ),
					fck_text( 'signature', 'Assinatura', 80 ),
					fck_textarea( 'summary', 'Resumo (Home)', 200 ),
					fck_text( 'eco_headline', 'Frase no Ecossistema', 120 ),
					fck_textarea( 'eco_text', 'Texto no Ecossistema', 400 ),
					fck_image( 'eco_image', 'Imagem no Ecossistema (opcional)' ),
				),
			),
			array(
				'id'     => 'hero',
				'title'  => 'Hero',
				'fields' => array(
					fck_text( 'headline', 'Título', 120, array( 'required' => true ) ),
					fck_textarea( 'lead', 'Texto de apoio', 700 ),
					fck_f( 'media', 'media', 'Imagem ou vídeo (opcional)' ),
				),
			),
			array(
				'id'          => 'solution',
				'title'       => 'Solução',
				'description' => 'Cada item vira uma etapa da interação da página (a ordem aqui é a ordem no site).',
				'fields'      => array(
					fck_text( 'title', 'Título', 120 ),
					fck_textarea( 'intro', 'Introdução', 400 ),
					fck_f(
						'repeater',
						'items',
						'Itens',
						array(
							'max_rows'  => 6,
							'row_label' => 'Adicionar item',
							'fields'    => array(
								fck_text( 'label', 'Nome curto', 40, array( 'required' => true ) ),
								fck_text( 'headline', 'Frase de destaque', 160 ),
								fck_textarea( 'text', 'Texto', 500 ),
							),
						)
					),
					fck_textarea( 'complement', 'Texto complementar (opcional)', 400 ),
				),
			),
			array(
				'id'          => 'interaction',
				'title'       => 'Ajustes da interação',
				'description' => 'Somente parâmetros seguros; o comportamento está no código. Preencha só o que vale para este produto.',
				'fields'      => array(
					fck_textarea( 'illustrative_note', 'Aviso de conteúdo ilustrativo', 300, array( 'help' => 'Obrigatório no FullMix (fica junto ao simulador). No Full Dash, rotula os gráficos.' ) ),
					fck_heading( 'FullMix — simulador' ),
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
					fck_heading( 'FullMatch — perfis' ),
					fck_text( 'offer_label', 'Rótulo "Tipo de oferta"', 40, array( 'width' => 'half' ) ),
					fck_text( 'cycle_label', 'Rótulo "Ciclo de recompra"', 40, array( 'width' => 'half' ) ),
					fck_f(
						'repeater',
						'profiles',
						'Detalhes por perfil',
						array(
							'max_rows'  => 6,
							'row_label' => 'Adicionar perfil',
							'help'      => 'Um por item da solução, na mesma ordem. Conceitual, sem números.',
							'fields'    => array( fck_text( 'offer', 'Tipo de oferta', 120 ), fck_text( 'cycle', 'Ciclo de recompra', 120 ) ),
						)
					),
				),
			),
			array(
				'id'     => 'cta',
				'title'  => 'Chamada final (CTA)',
				'fields' => array( fck_text( 'title', 'Título', 120 ), fck_textarea( 'text', 'Texto', 300 ), fck_link_field( 'cta', 'Botão' ) ),
			),
			fck_seo_group(),
		),

		'fck_case'        => array(
			array(
				'id'     => 'details',
				'title'  => 'Dados do case',
				'fields' => array(
					fck_f( 'post', 'client', 'Cliente', array( 'post_type' => 'fck_client', 'width' => 'half' ) ),
					fck_f( 'post', 'sector', 'Setor', array( 'post_type' => 'fck_sector', 'width' => 'half' ) ),
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
					fck_f( 'post', 'case', 'Próximo case (manual)', array( 'post_type' => 'fck_case', 'help' => 'Vazio = o próximo da listagem (volta ao primeiro no fim).' ) ),
				),
			),
			fck_seo_group(),
		),

		'fck_article'     => array(
			array(
				'id'          => 'details',
				'title'       => 'Dados do artigo',
				'description' => 'Capa = imagem destacada. Resumo = campo "Resumo". Categoria no painel lateral. Leitura estimada é calculada automaticamente.',
				'fields'      => array(
					fck_text( 'subtitle', 'Subtítulo', 240 ),
					fck_f( 'post', 'author', 'Autor', array( 'post_type' => 'fck_author' ) ),
					fck_f( 'checkbox', 'featured', 'Destaque (abre o Insights e a seção da Home)' ),
					fck_f( 'posts', 'related', 'Relacionados (manual, até 3)', array( 'post_type' => 'fck_article', 'help' => 'Vazio = mais recentes da mesma categoria.' ) ),
				),
			),
			fck_seo_group(),
		),

		'fck_press'       => array(
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

		'fck_sector'      => array(
			array(
				'id'          => 'details',
				'title'       => 'Setor',
				'description' => 'O nome é o título do registro. O slug é usado no filtro de cases (?setor=...).',
				'fields'      => array( fck_text( 'title', 'Frase de destaque', 120 ), fck_textarea( 'text', 'Texto', 400 ), fck_image( 'image', 'Imagem' ) ),
			),
		),

		'fck_client'      => array(
			array(
				'id'          => 'details',
				'title'       => 'Cliente',
				'description' => 'O logo só aparece no site com autorização de exibição registrada.',
				'fields'      => array(
					fck_image( 'logo', 'Logo (SVG ou PNG transparente)' ),
					fck_f( 'checkbox', 'authorized', 'Autorização de exibição registrada' ),
					fck_text( 'authorization_note', 'Registro da autorização (interno)', 200, array( 'help' => 'Quem autorizou, quando e onde está o registro. Não aparece no site.' ) ),
					fck_f( 'post', 'sector', 'Setor', array( 'post_type' => 'fck_sector' ) ),
				),
			),
		),

		'fck_metric'      => array(
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

		'fck_partner'     => array(
			array(
				'id'          => 'details',
				'title'       => 'Sócio',
				'description' => 'O nome é o título. A seção da Home aparece só com os três sócios publicados.',
				'fields'      => array( fck_text( 'role', 'Cargo', 80 ), fck_textarea( 'bio', 'Bio', 700 ), fck_image( 'photo', 'Retrato vertical', array( 'required' => true ) ) ),
			),
		),

		'fck_team'        => array(
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

		'fck_testimonial' => array(
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

		'fck_author'      => array(
			array(
				'id'     => 'details',
				'title'  => 'Autor',
				'fields' => array( fck_text( 'role', 'Cargo', 80 ), fck_image( 'photo', 'Foto' ) ),
			),
		),
	);
}
