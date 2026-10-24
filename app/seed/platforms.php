<?php
/**
 * Seed das cinco plataformas — conteúdo de docs/BRIEFING_ECOSSISTEMA.md (E3).
 *
 * - Ordem e verbos de E0: FullMix (Planejar), FullDash (Otimizar),
 *   FullLeads (Converter), FullMatch (Fidelizar), FullHub (Organizar).
 * - O nome da própria plataforma é trocado por {platform} em todos os textos
 *   (fck_seed_platforms()), para que renomear seja uma edição só (D1).
 * - Itens marcados [VALIDAR] no adendo entram com claim_status = validar e
 *   a dúvida em claim_note (só no painel). Ver docs/CLAIMS_REGISTER.md.
 * - '@platform:chave' é resolvido pelo seed para o ID da plataforma.
 */

function fck_seed_platforms(): array {
	$ok   = static fn( string $title, string $text ): array => array( 'title' => $title, 'text' => $text, 'claim_status' => 'confirmado', 'claim_note' => '' );
	$check = static fn( string $title, string $text, string $note ): array => array( 'title' => $title, 'text' => $text, 'claim_status' => 'validar', 'claim_note' => $note );
	$link = static fn( string $key, string $text, string $status = 'confirmado', string $note = '' ): array => array( 'platform' => '@platform:' . $key, 'text' => $text, 'claim_status' => $status, 'claim_note' => $note );
	$alts = static fn( string ...$t ): array => array_map( fn( $x ) => array( 'title' => $x ), $t );
	$cta  = static fn( string $name ): string => 'Conversar sobre o ' . $name;

	$platforms = array(
		'mix'   => array(
			'title' => 'FullMix',
			'slug'  => 'fullmix',
			'order' => 1,
			'meta'  => array(
				'identity'    => array(
					'product'    => 'mix',
					'verb'       => 'Planejar',
					'home_line'  => 'Descubra onde investir com inteligência estratégica.',
					'card_title' => 'Inteligência que direciona resultados.',
					'card_text'  => 'Conecte dados, entenda o impacto dos seus investimentos e descubra onde a próxima decisão pode gerar mais resultado. Com Marketing Mix Modeling e simulação de cenários, o FullMix transforma informação em estratégia.',
					'connects'   => 'Orienta as decisões estratégicas de investimento.',
				),
				'hero'        => array(
					'headline'   => 'Inteligência para investir melhor. Estratégia para multiplicar resultados.',
					'lead'       => 'Investir em marketing é importante. Saber onde investir faz toda a diferença.',
					'cta_label'  => $cta( 'FullMix' ),
					'alt_titles' => $alts( 'O próximo resultado começa na decisão certa.', 'Seu investimento merece mais que um palpite.', 'Dados que se conectam. Investimentos que se multiplicam.', 'O fim do achismo. O começo da inteligência.' ),
				),
				'problem'     => array(
					'enabled' => true,
					'title'   => 'Você sabe onde investe. Mas sabe o que realmente vende?',
					'text'    => 'Cada canal desempenha um papel nos resultados. Sem entender essa contribuição, a distribuição do orçamento depende mais de hábito do que de evidência.',
				),
				'how'         => array(
					'enabled' => true,
					'motion'  => true,
					'intro'   => 'O FullMix é a plataforma de inteligência estratégica da Full Connect Key que estima o impacto dos investimentos em marketing e mostra quanto cada canal contribui para os resultados do seu negócio.',
					'steps'   => array(
						$ok( 'Conectar dados', 'Por meio do Marketing Mix Modeling (MMM), reúne dados de campanhas online e offline, vendas e fatores externos, como sazonalidade, preços, promoções e condições de mercado.' ),
						$ok( 'Estimar a contribuição', 'Analisa a performance com dados agregados, sem depender de cookies ou do rastreamento individual de consumidores.' ),
						$ok( 'Simular e planejar', 'Mais do que analisar o passado, permite simular cenários e planejar os próximos investimentos com mais embasamento.' ),
					),
				),
				'interaction' => array(
					'illustrative_note'  => 'Esta experiência é ilustrativa. As proporções exibidas não representam previsões de vendas ou recomendações reais de investimento.',
					'simulator_intro'    => 'Explore diferentes distribuições de investimento e entenda por que conhecer a participação de cada canal é importante para tomar decisões.',
					'total_label'        => 'Total distribuído', // (*) rótulo de interface
					'reset_label'        => 'Restaurar distribuição', // (*) rótulo de interface
					'channels'           => array(
						array( 'name' => 'TV', 'kind' => 'offline', 'share' => 20 ),
						array( 'name' => 'Rádio', 'kind' => 'offline', 'share' => 10 ),
						array( 'name' => 'Mídia exterior', 'kind' => 'offline', 'share' => 10 ),
						array( 'name' => 'Busca paga', 'kind' => 'online', 'share' => 25 ),
						array( 'name' => 'Redes sociais', 'kind' => 'online', 'share' => 25 ),
						array( 'name' => 'Mídia programática', 'kind' => 'online', 'share' => 10 ),
					),
					'label_sales'        => 'vendas',
					'label_base'         => 'o que aconteceria sem campanhas',
					'label_contribution' => 'contribuição estimada',
					'flows'              => "online\noffline\nsazonalidade\npreço\npromoções\nmercado",
				),
				'benefits'    => array(
					'enabled'   => true,
					'manifesto' => 'Menos achismo. Mais inteligência. Melhores decisões.',
					'items'     => array(
						$ok( 'Contribuição incremental', 'Estima quanto cada canal contribui para as vendas, separando o efeito associado ao marketing do que aconteceria independentemente das campanhas.' ),
						$ok( 'Visão integrada', 'Analisa investimentos online e offline em um mesmo modelo.' ),
						$ok( 'Inteligência sem cookies', 'Trabalha com dados agregados, sem depender do rastreamento individual.' ),
						$ok( 'Planejamento estratégico', 'Simula cenários para orientar a distribuição do orçamento.' ),
						$ok( 'Otimização de investimentos', 'Aponta oportunidades para direcionar recursos aos canais com maior potencial de retorno.' ),
						$ok( 'Análise contextual', 'Considera fatores externos que podem influenciar o desempenho comercial.' ),
					),
					'note'              => 'Os resultados do Marketing Mix Modeling são estimativas estatísticas, cuja precisão depende da qualidade e do histórico dos dados disponíveis.',
					'note_claim_status' => 'confirmado',
				),
				'interface'   => array( 'enabled' => true, 'items' => array() ),
				'connections' => array(
					'enabled' => true,
					'items'   => array(
						$link( 'dash', 'O que o FullMix orienta no planejamento, o FullDash acompanha e otimiza na execução da mídia.' ),
						$link( 'leads', 'Os dados de oportunidades geradas ajudam a enriquecer a leitura da contribuição de cada canal.', 'validar', 'Integração de dados entre FullLeads e FullMix.' ),
					),
				),
				'closing'     => array(
					'title'     => 'Cada investimento tem um potencial. Descubra onde ele se multiplica.',
					'text'      => 'Transforme dados em estratégia e estratégia em resultados.',
					'cta_label' => $cta( 'FullMix' ),
				),
				'seo'         => array(
					'title'       => 'FullMix | Marketing Mix Modeling e Planejamento de Investimentos',
					'description' => 'Estime a contribuição dos canais online e offline para as vendas, simule cenários e planeje investimentos em marketing com mais inteligência.',
				),
			),
		),

		'dash'  => array(
			'title' => 'FullDash',
			'slug'  => 'fulldash',
			'order' => 2,
			'meta'  => array(
				'identity'    => array(
					'product'    => 'dash',
					'verb'       => 'Otimizar',
					'home_line'  => 'Controle seus investimentos e melhore a performance da mídia diariamente.',
					'card_title' => 'Inteligência que acompanha. Performance que evolui.',
					'card_text'  => 'Conecte planejamento, investimento e resultados em uma gestão de mídia mais inteligente. Com acompanhamento diário e otimização contínua, o FullDash ajuda a direcionar cada investimento para os resultados que importam.',
					'connects'   => 'Acompanha e otimiza a execução dos investimentos em mídia.',
				),
				'hero'        => array(
					'headline'   => 'Sua mídia em movimento. Seus resultados em evolução.',
					'lead'       => 'Investir em mídia é importante. Garantir que cada investimento trabalhe a favor dos seus resultados é essencial.',
					'cta_label'  => $cta( 'FullDash' ),
					'alt_titles' => $alts( 'Sua mídia não para. Sua inteligência também não.', 'Cada real conta. Cada decisão multiplica.', 'Performance não se acompanha uma vez por mês.', 'Do planejamento à performance. Tudo sob controle.' ),
				),
				'problem'     => array(
					'enabled' => true,
					'title'   => 'Sua mídia gera números. Mas gera negócios?',
					'text'    => 'Impressões, cliques e alcance ajudam a acompanhar campanhas. O que importa é entender como o investimento contribui para os objetivos do negócio, e corrigir o rumo antes do fim do mês.',
				),
				'how'         => array(
					'enabled' => true,
					'motion'  => true,
					'intro'   => 'O FullDash é a plataforma de inteligência e gestão de mídia da Full Connect Key que conecta planejamento, controle financeiro e performance em um único ambiente.',
					'steps'   => array(
						$check( 'Planejar e aprovar', 'Orçamento, plano de mídia e pedidos de inserção (POs) organizados no mesmo lugar.', 'Gestão de POs na plataforma.' ),
						$check( 'Acompanhar o ritmo', 'Acompanhamento diário do ritmo de investimento (pacing), com apoio de inteligência artificial, para manter a distribuição da verba alinhada ao planejamento.', 'Uso de IA no pacing.' ),
						$ok( 'Otimizar', 'Análise de performance e ajustes frequentes, com realocação de recursos entre campanhas dentro dos limites do plano aprovado.' ),
					),
				),
				'interaction' => array(
					'illustrative_note' => '', // usa o selo "Ilustrativo" dos textos de interface
					'selector'          => "Gestão de mídia\nOtimização contínua\nResultados de negócio",
					'label_planned'     => 'planejado',
					'label_actual'      => 'realizado',
					'label_adjust'      => 'ajuste',
					'label_goal'        => 'objetivo do negócio',
					'words'             => "impressões\ncliques\nalcance",
				),
				'benefits'    => array(
					'enabled'   => true,
					'manifesto' => 'Menos desperdício. Mais controle. Performance em constante evolução.',
					'items'     => array(
						$check( 'Gestão integrada de investimentos', 'Orçamento, plano de mídia e POs organizados em um único ambiente.', 'Gestão de POs na plataforma.' ),
						$check( 'Inteligência artificial aplicada', 'Apoio no acompanhamento diário do pacing para manter a verba alinhada ao plano.', 'Uso de IA no pacing.' ),
						$ok( 'Otimização contínua', 'Análise de performance e ajustes frequentes para aproveitar melhor cada oportunidade.' ),
						$ok( 'Investimentos mais inteligentes', 'Realocação estratégica entre campanhas, respeitando os limites do plano aprovado.' ),
						$ok( 'Transparência financeira', 'Visibilidade dos valores aprovados, investidos e dos resultados atribuídos às campanhas.' ),
						$check( 'Resultados conectados às metas', 'Dashboards que transformam indicadores em informação para decisão.', 'Frequência de atualização ("tempo real" só se for confirmado).' ),
					),
					'note'              => 'A inteligência artificial apoia o acompanhamento e as decisões da equipe; ela não elimina variações naturais de entrega e investimento.',
					'note_claim_status' => 'confirmado',
				),
				'interface'   => array( 'enabled' => true, 'items' => array() ),
				'connections' => array(
					'enabled' => true,
					'items'   => array(
						$link( 'mix', 'O FullMix orienta onde investir; o FullDash cuida de como esse investimento é executado no dia a dia.' ),
						$link( 'leads', 'Campanhas otimizadas alimentam a geração de oportunidades comerciais.' ),
					),
				),
				'closing'     => array(
					'title'     => 'Seu investimento não pode esperar até o fim do mês para dar resultado.',
					'text'      => 'Mais inteligência, agilidade e controle para transformar cada decisão de mídia em uma oportunidade de crescimento.',
					'cta_label' => $cta( 'FullDash' ),
				),
				'seo'         => array(
					'title'       => 'FullDash | Gestão de Mídia, Pacing e Performance',
					'description' => 'Conecte planejamento, controle financeiro e performance de mídia em um só ambiente, com acompanhamento diário e otimização contínua.',
				),
			),
		),

		'leads' => array(
			'title' => 'FullLeads',
			'slug'  => 'fullleads',
			'order' => 3,
			'meta'  => array(
				'identity'    => array(
					'product'    => 'leads',
					'verb'       => 'Converter',
					'home_line'  => 'Transforme contatos em oportunidades comerciais qualificadas.',
					'card_title' => 'Oportunidades que se conectam. Negócios que se multiplicam.',
					'card_text'  => 'Da atração à conversão, conectamos marketing e vendas para transformar contatos em oportunidades qualificadas, acompanhando cada etapa do funil e aproximando sua equipe comercial das oportunidades com mais potencial.',
					'connects'   => 'Transforma campanhas e conteúdos em oportunidades comerciais qualificadas.',
				),
				'hero'        => array(
					'headline'   => 'Mais do que gerar leads. Conectamos oportunidades a resultados.',
					'lead'       => 'Atrair pessoas é só o começo. Transformar interesse em negócio é o que realmente importa.',
					'cta_label'  => $cta( 'FullLeads' ),
					'alt_titles' => $alts( 'O lead certo. Na hora certa. Para a venda acontecer.', 'Transforme conexões em oportunidades. Oportunidades em negócios.', 'Seu marketing atrai. Nossa inteligência aproxima da venda.', 'Atrair é só o começo. O negócio é converter.' ),
				),
				'problem'     => array(
					'enabled' => true,
					'title'   => 'Não é sobre gerar contatos. É sobre gerar negócios.',
					'text'    => 'Contato sem contexto vira lista parada. Quando marketing e vendas não se conectam, boas oportunidades se perdem no caminho.',
				),
				'how'         => array(
					'enabled' => true,
					'motion'  => true,
					'intro'   => 'O FullLeads é a plataforma de geração e gestão de oportunidades da Full Connect Key que conecta marketing e vendas em uma jornada estratégica, do primeiro contato ao encaminhamento comercial.',
					'steps'   => array(
						$ok( 'Atrair', 'Campanhas e conteúdos direcionados para alcançar o público certo.' ),
						$ok( 'Capturar', 'Landing pages e formulários que transformam interesse em contatos.' ),
						$check( 'Nutrir e qualificar', 'Comunicação personalizada ao longo da jornada e identificação dos contatos com maior potencial.', 'Automação da nutrição e critério de qualificação (lead scoring).' ),
						$ok( 'Vender', 'Oportunidades organizadas no funil comercial, para que a equipe de vendas concentre esforços onde há mais potencial.' ),
					),
				),
				'benefits'    => array(
					'enabled'   => true,
					'manifesto' => 'Menos contatos sem potencial. Mais oportunidades qualificadas. Mais negócios.',
					'items'     => array(
						$ok( 'Atração estratégica', 'Campanhas e conteúdos direcionados para alcançar o público certo.' ),
						$ok( 'Captação inteligente', 'Landing pages e formulários que transformam interesse em contatos.' ),
						$ok( 'Nutrição contínua', 'Comunicação personalizada para desenvolver oportunidades ao longo da jornada de compra.' ),
						$check( 'Qualificação de leads', 'Identificação dos contatos com maior potencial de conversão.', 'Critério de qualificação (lead scoring).' ),
						$check( 'Marketing e vendas integrados', 'Oportunidades organizadas em um funil conectado, com visibilidade de cada etapa.', 'Integração com CRM — quais.' ),
						$ok( 'Acompanhamento da jornada', 'Do primeiro contato ao encaminhamento comercial e, quando integrado aos dados de vendas, até o fechamento.' ),
					),
				),
				'interface'   => array( 'enabled' => true, 'items' => array() ),
				'connections' => array(
					'enabled' => true,
					'items'   => array(
						$link( 'dash', 'A mídia otimizada pelo FullDash amplia o alcance das estratégias de atração.' ),
						$link( 'match', 'Quando a oportunidade vira cliente, o FullMatch continua o relacionamento.' ),
					),
				),
				'closing'     => array(
					'title'     => 'Sua próxima venda começa muito antes do primeiro contato comercial.',
					'text'      => 'Conecte sua marca às pessoas certas e transforme oportunidades em crescimento.',
					'cta_label' => $cta( 'FullLeads' ),
				),
				'seo'         => array(
					'title'       => 'FullLeads | Geração e Qualificação de Oportunidades Comerciais',
					'description' => 'Conecte atração, captação e nutrição ao funil comercial para desenvolver oportunidades mais qualificadas para o seu time de vendas.',
				),
			),
		),

		'match' => array(
			'title' => 'FullMatch',
			'slug'  => 'fullmatch',
			'order' => 4,
			'meta'  => array(
				'identity'    => array(
					'product'    => 'match',
					'verb'       => 'Fidelizar',
					'home_line'  => 'Personalize ofertas, estimule a recorrência e fortaleça o relacionamento com seus clientes.',
					'card_title' => 'Conexões personalizadas. Resultados multiplicados.',
					'card_text'  => 'Transforme dados de comportamento em ofertas relevantes para cada consumidor. O FullMatch ajuda a identificar momentos e canais mais adequados para estimular novas compras, aumentar a recorrência e reativar clientes.',
					'connects'   => 'Desenvolve o relacionamento com consumidores, estimulando recorrência e novas oportunidades de compra.',
				),
				'hero'        => array(
					'headline'   => 'Cada cliente é único. Sua estratégia de vendas também deveria ser.',
					'lead'       => 'Conhecer seu cliente é importante. Entender o que ele precisa, quando precisa e como prefere comprar faz toda a diferença.',
					'cta_label'  => $cta( 'FullMatch' ),
					'alt_titles' => $alts( 'Seu cliente não é igual a todos. Sua oferta também não deveria ser.', 'O match perfeito entre o que seu cliente quer e o que sua marca oferece.', 'A próxima compra começa com a conexão certa.', 'Mais relevância em cada oferta. Mais valor em cada relação.' ),
				),
				'problem'     => array(
					'enabled' => true,
					'title'   => 'A primeira compra não precisa ser a última.',
					'text'    => 'A mesma promoção para todo mundo ignora o que cada cliente já mostrou sobre seus interesses, e deixa de lado quem parou de comprar.',
				),
				'how'         => array(
					'enabled' => true,
					'motion'  => true,
					'intro'   => 'O FullMatch é a plataforma de inteligência de relacionamento da Full Connect Key que transforma dados de comportamento e histórico de compras em experiências e ofertas personalizadas.',
					'steps'   => array(
						$ok( 'Entender', 'Analisa hábitos de consumo, frequência de compra, canais de preferência e períodos de inatividade.' ),
						$ok( 'Personalizar', 'Identifica oportunidades para se conectar com cada consumidor de maneira mais relevante.' ),
						$ok( 'Ativar', 'Apoia comunicações mais assertivas para estimular novas compras, fortalecer relacionamentos e aumentar o valor de cada cliente para o negócio.' ),
					),
				),
				'interaction' => array(
					// Perfis conceituais (textos aprovados no v1.1).
					'clusters' => array(
						array( 'name' => 'Recorrente', 'text' => 'Quem já compra pode encontrar novos motivos para voltar.' ),
						array( 'name' => 'Alto valor', 'text' => 'Mais relevância em cada nova oferta.' ),
						array( 'name' => 'Inativo', 'text' => 'Uma conexão pode ser retomada.' ),
					),
				),
				'benefits'    => array(
					'enabled'   => true,
					'manifesto' => 'Menos ofertas genéricas. Mais relevância. Mais oportunidades de venda.',
					'items'     => array(
						$ok( 'Personalização inteligente', 'Ofertas e comunicações adaptadas ao perfil e ao comportamento de cada cliente.' ),
						$ok( 'Momento ideal', 'Ações orientadas pelo ciclo individual de compra, e não apenas pelo calendário promocional.' ),
						$check( 'Comunicação multicanal', 'Contato pelos canais de preferência de cada consumidor, respeitando o consentimento e as preferências de comunicação.', 'Canais suportados.' ),
						$ok( 'Recorrência de compras', 'Identificação de oportunidades para estimular novas compras e fortalecer a fidelização.' ),
						$ok( 'Reativação de clientes', 'Estratégias direcionadas para reconquistar consumidores que deixaram de comprar.' ),
						$ok( 'Resultados mensuráveis', 'Acompanhamento de indicadores como recorrência, ticket médio e reativação.' ),
					),
					'note'              => 'Dados tratados conforme a LGPD, com uso orientado pelas bases legais aplicáveis e pelas preferências de comunicação dos consumidores.',
					'note_claim_status' => 'validar',
					'note_claim_note'   => 'Revisar com o jurídico antes da publicação.',
				),
				'interface'   => array( 'enabled' => true, 'items' => array() ),
				'connections' => array(
					'enabled' => true,
					'items'   => array(
						$link( 'leads', 'O FullLeads gera a oportunidade; o FullMatch cuida do relacionamento depois da primeira compra.' ),
						$link( 'mix', 'Entender o que traz recorrência ajuda a planejar melhor os próximos investimentos.' ),
					),
				),
				'closing'     => array(
					'title'     => 'Relacionamentos mais relevantes geram negócios mais duradouros.',
					'text'      => 'Transforme o conhecimento sobre seus clientes em conexões que fazem sentido e resultados que se multiplicam.',
					'cta_label' => $cta( 'FullMatch' ),
				),
				'seo'         => array(
					'title'       => 'FullMatch | Personalização, Recorrência e Reativação de Clientes',
					'description' => 'Transforme dados de comportamento de compra em ofertas personalizadas para estimular recorrência, aumentar o ticket médio e reativar clientes.',
				),
			),
		),

		'hub'   => array(
			'title' => 'FullHub',
			'slug'  => 'fullhub',
			'order' => 5,
			'meta'  => array(
				'identity'    => array(
					'product'    => 'hub',
					'verb'       => 'Organizar',
					'home_line'  => 'Conecte clientes, equipes e entregas em uma gestão integrada.',
					'card_title' => 'Sua operação inteira, em um só lugar.',
					'card_text'  => 'Centralize solicitações, acompanhe entregas e aprove materiais em uma plataforma que conecta você à nossa operação, com mais transparência, agilidade e organização.',
					'connects'   => 'Sustenta a organização operacional, conectando pessoas, demandas e entregas ao longo de todo o processo.',
				),
				'hero'        => array(
					'headline'   => 'Tudo conectado. Tudo organizado. Tudo em um só lugar.',
					'lead'       => 'Uma operação eficiente começa quando todas as informações estão no lugar certo.',
					'cta_label'  => $cta( 'FullHub' ),
					'alt_titles' => $alts( 'Sua operação conectada. Sua rotina simplificada.', 'Chega de procurar. Comece a acompanhar.', 'Tudo acontece. Nada se perde.', 'Menos processos espalhados. Mais resultados conectados.' ),
				),
				'problem'     => array(
					'enabled' => true,
					'title'   => 'Aprovação por WhatsApp não é processo. É sorte.',
					'text'    => 'Solicitações espalhadas, mensagens perdidas e prazos difíceis de acompanhar não precisam fazer parte da rotina.',
				),
				'how'         => array(
					'enabled' => true,
					'motion'  => true,
					'intro'   => 'O FullHub é a plataforma de gestão integrada da Full Connect Key que conecta clientes, equipes e demandas em um único ambiente digital.',
					'steps'   => array(
						$check( 'Do lado do cliente', 'Você solicita novos projetos, acompanha o andamento das entregas, aprova materiais, consulta cronogramas e acessa os arquivos da sua marca.', 'Cronogramas e biblioteca de arquivos disponíveis.' ),
						$ok( 'Do lado da Full', 'Nossa equipe gerencia responsáveis, prazos e processos dentro do mesmo sistema.' ),
					),
				),
				'interaction' => array(
					// Colunas do quadro que se organiza (nomes aprovados no v1.1).
					'columns' => "Solicitações\nResponsáveis e prazos\nAprovações\nVisibilidade",
				),
				'benefits'    => array(
					'enabled'   => true,
					'manifesto' => 'Menos mensagens. Menos retrabalho. Mais tempo para o que realmente importa.',
					'items'     => array(
						$ok( 'Gestão centralizada', 'Solicitações, entregas, aprovações e cronogramas reunidos em um único ambiente.' ),
						$check( 'Transparência no andamento', 'Acompanhamento de cada demanda sem depender de atualizações por mensagens.', 'Usar "em tempo real" só se confirmado.' ),
						$check( 'Autonomia para o cliente', 'Acesso a arquivos, materiais da marca, entregas finais e histórico de projetos.', 'Biblioteca de arquivos e histórico de projetos disponíveis.' ),
						$ok( 'Aprovações simplificadas', 'Avaliação de materiais e solicitação de ajustes diretamente pela plataforma.' ),
						$ok( 'Operação integrada', 'Clientes e equipes conectados, com responsabilidades e prazos organizados.' ),
						$check( 'Histórico centralizado', 'Informações e versões registradas para facilitar consultas e reduzir retrabalho.', 'Versionamento de materiais.' ),
					),
				),
				'interface'   => array( 'enabled' => true, 'items' => array() ),
				'connections' => array(
					'enabled' => true,
					'intro'   => 'O FullHub é a base operacional do ecossistema: as demandas que nascem nas outras plataformas ganham responsáveis, prazos e aprovações em um só lugar.',
					'items'   => array(),
				),
				'closing'     => array(
					'title'     => 'Sua marca tem muita coisa acontecendo. A gestão não precisa ser complicada.',
					'text'      => 'Mais controle, transparência e praticidade para uma operação que não para.',
					'cta_label' => $cta( 'FullHub' ),
				),
				'seo'         => array(
					'title'       => 'FullHub | Gestão Integrada de Demandas, Entregas e Aprovações',
					'description' => 'Centralize solicitações, entregas, aprovações e cronogramas em uma gestão integrada entre a Full Connect Key e seus clientes.',
				),
			),
		),
	);

	// Nome próprio → {platform} em todos os textos da plataforma (D1).
	foreach ( $platforms as $key => $p ) {
		$platforms[ $key ]['meta'] = fck_seed_tokenize( $p['meta'], $p['title'] );
	}
	return $platforms;
}

/** Troca o nome da plataforma pelo token {platform} em todas as strings. */
function fck_seed_tokenize( array $values, string $name ): array {
	foreach ( $values as $k => $v ) {
		if ( is_array( $v ) ) {
			$values[ $k ] = fck_seed_tokenize( $v, $name );
		} elseif ( is_string( $v ) && ! str_starts_with( $v, '@' ) ) {
			$values[ $k ] = str_replace( $name, '{platform}', $v );
		}
	}
	return $values;
}
