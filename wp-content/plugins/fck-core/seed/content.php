<?php
/**
 * Conteúdo inicial (seed) — textos aprovados da seção 6 do briefing.
 * São valores iniciais EDITÁVEIS no painel. Pendências "[PENDENTE]" não
 * entram aqui: viram campos vazios e itens em docs/CONTENT_GAPS.md.
 *
 * Links: 'page:<chave>' aponta para uma página do seed; 'platform:<produto>'
 * para uma plataforma; 'url:/caminho' para um endereço fixo.
 */

defined( 'ABSPATH' ) || exit;

function fck_seed_content(): array {
	$talk = array( 'label' => 'Vamos conversar', 'to' => 'page:contact' );

	return array(
		// ------------------------------------------------------------------
		'site'       => array(
			'brand'        => array( 'site_name' => 'Full Connect Key' ),
			'header'       => array( 'cta' => $talk ),
			'footer'       => array(
				'tagline'   => 'O que se conecta, se multiplica.',
				'text'      => 'Estratégia, criatividade, tecnologia, marketing e vendas conectados para transformar oportunidades em resultados.',
				'cta'       => $talk,
				'copyright' => '© {ano} Full Connect Key. Todos os direitos reservados.',
			),
			'contacts'     => array(),
			'seo_defaults' => array(
				'default_title'       => 'Full Connect Key | Estratégia, Criatividade e Resultados',
				'title_template'      => '%s | Full Connect Key',
				'default_description' => 'Conectamos estratégia, criatividade, tecnologia, marketing e vendas para transformar desafios em oportunidades e resultados.',
			),
			'cookies'      => array( 'banner_mode' => 'auto' ),
		),

		// ------------------------------------------------------------------
		// Microtextos. Os marcados com (*) não constam do briefing: são
		// rótulos de interface criados no desenvolvimento — revisar.
		'ui'         => array(
			'a11y'     => array(
				'skip_to_content'       => 'Pular para o conteúdo', // (*)
				'home_link'             => 'Full Connect Key — página inicial', // (*)
				'main_nav'              => 'Navegação principal', // (*)
				'footer_nav'            => 'Navegação do rodapé', // (*)
				'open_menu'             => 'Abrir menu',
				'close_menu'            => 'Fechar menu',
				'open_submenu'          => 'Abrir submenu de {label}', // (*)
				'back_to_top'           => 'Voltar ao topo',
				'next_project'          => 'Próximo projeto',
				'previous_project'      => 'Projeto anterior',
				'next_article'          => 'Próximo artigo',
				'previous_article'      => 'Artigo anterior',
				'view_all_publications' => 'Ver todas as publicações',
				'pause'                 => 'Pausar movimento', // (*)
				'play'                  => 'Retomar movimento', // (*)
				'previous'              => 'Anterior', // (*)
				'next'                  => 'Próximo', // (*)
				'go_to_item'            => 'Ir para o item {n} de {total}', // (*)
				'external_link'         => '(abre em nova aba)', // (*)
				'loading'               => 'Carregando…', // (*)
				'breadcrumb'            => 'Trilha de navegação', // (*)
				'logos_region'          => 'Marcas que trabalham com a Full', // (*)
				'illustrative'          => 'Ilustrativo', // (*)
				'scroll_hint'           => 'Arraste ou use as setas para ver mais', // (*)
				'progress'              => '{n} de {total}', // (*)
			),
			'form'     => array(
				'required'         => 'Preencha este campo para continuar.',
				'invalid_email'    => 'Confira se o endereço de e-mail está correto.',
				'too_long'         => 'Use no máximo {max} caracteres.', // (*)
				'consent_required' => 'Confirme o consentimento para continuar.', // (*)
				'send_error'       => 'Não foi possível enviar sua mensagem agora. Tente novamente em instantes.',
				'rate_limited'     => 'Muitas tentativas em pouco tempo. Aguarde alguns minutos e tente novamente.', // (*)
				'sending'          => 'Estamos enviando sua mensagem...',
				'error_summary'    => 'Revise os campos destacados.', // (*)
				'optional'         => '(opcional)', // (*)
				'step_of'          => 'Etapa {n} de {total}', // (*)
			),
			'cookies'  => array(
				'title'                 => 'Sua experiência também importa.',
				'text'                  => 'Utilizamos cookies necessários para o funcionamento do site e, com sua autorização, outros cookies para analisar a navegação e aprimorar sua experiência. Saiba mais em nossa Política de Cookies.',
				'accept_all'            => 'Aceitar todos',
				'reject_optional'       => 'Recusar opcionais',
				'customize'             => 'Personalizar preferências',
				'preferences_title'     => 'Personalizar preferências', // (*)
				'necessary'             => 'Cookies necessários',
				'necessary_description' => '', // pendente: texto jurídico
				'analytics'             => 'Cookies de análise',
				'analytics_description' => '', // pendente: texto jurídico
				'marketing'             => 'Cookies de marketing',
				'marketing_description' => '', // pendente: texto jurídico
				'always_on'             => 'Sempre ativos', // (*)
				'save'                  => 'Salvar preferências',
				'policy_link'           => 'Política de Cookies',
				'open_preferences'      => 'Preferências de cookies', // (*)
			),
			'notfound' => array(
				'title' => 'Parece que essa conexão se perdeu.',
				'text'  => 'A página que você procura não está disponível. Mas ainda existem muitas possibilidades para explorar.',
				'cta'   => 'Voltar para a Home',
			),
			'error'    => array(
				'title' => 'Essa conexão precisa de um instante.',
				'text'  => 'Encontramos uma dificuldade para carregar o conteúdo. Tente novamente.',
				'retry' => 'Tentar novamente',
			),
			'content'  => array(
				'reading_time'   => '{n} min de leitura', // (*)
				'by'             => 'Por', // (*)
				'share_article'  => 'Compartilhar artigo',
				'copy_link'      => 'Copiar link', // (*)
				'link_copied'    => 'Link copiado.', // (*)
				'share_linkedin' => 'LinkedIn', // (*)
				'share_whatsapp' => 'WhatsApp', // (*)
				'share_email'    => 'E-mail', // (*)
				'filter_by'      => 'Filtrar por categoria', // (*)
				'results_count'  => '{n} resultados', // (*)
			),
		),

		// ------------------------------------------------------------------
		'pages'      => array(
			'home'      => array(
				'title'    => 'Home',
				'slug'     => 'home',
				'template' => 'home',
				'status'   => 'publish',
				'meta'     => array(
					'hero'         => array(
						'headline'     => 'O que se conecta, se multiplica.',
						'headline_alt' => 'Vire a chave dos seus resultados. Ative o modo Full no seu negócio.',
						'lead'         => 'Conectamos estratégia, criatividade, tecnologia, marketing e vendas para transformar desafios em oportunidades e oportunidades em resultados.',
						'cta'          => array( 'label' => 'Descubra a Full', 'to' => 'page:sm' ),
					),
					'logos'        => array( 'enabled' => true ),
					'essence'      => array(
						'enabled' => true,
						'title'   => 'Conexões que fazem negócios evoluírem.',
						'intro'   => 'Acreditamos que grandes resultados não acontecem de maneira isolada. Eles surgem quando ideias, pessoas, estratégias e tecnologias trabalham em sintonia. É assim que enxergamos cada negócio: um universo de possibilidades que ganha força quando suas conexões são compreendidas, aprimoradas e potencializadas.',
						'pillars' => array(
							array( 'name' => 'Conecta', 'text' => 'Aproximamos marcas, pessoas, ideias e oportunidades. Porque entender as relações é o primeiro passo para transformá-las.' ),
							array( 'name' => 'Organiza', 'text' => 'Integramos conhecimento, processos e estratégias para que cada ação tenha propósito e cada decisão tenha direção.' ),
							array( 'name' => 'Multiplica', 'text' => 'Testamos, aprendemos e aprimoramos continuamente para transformar boas conexões em resultados cada vez mais relevantes.' ),
						),
					),
					'ecosystem'    => array(
						'enabled' => true,
						'title'   => 'Quem se conecta, evolui.',
						'text'    => 'Um ecossistema de soluções que integra operação, mídia, oportunidades comerciais, inteligência de investimento e relacionamento com clientes. Tudo conectado às necessidades do seu negócio.',
						'cta'     => array( 'label' => 'Explore o Ecossistema Full', 'to' => 'page:ecosystem' ),
					),
					'sectors'      => array(
						'enabled' => true,
						'title'   => 'Cada mercado tem seus desafios. A gente se conecta a eles.',
						'intro'   => 'Conhecimento de mercado, repertório criativo e inteligência estratégica para entender diferentes jornadas de consumo e construir soluções relevantes.',
					),
					'cases'        => array(
						'enabled' => true,
						'title'   => 'Ideias que se transformam em resultados.',
						'text'    => 'Cada desafio exige um olhar diferente. Conheça projetos em que criatividade e estratégia se conectaram para construir novas possibilidades.',
						'cta'     => array( 'label' => 'Explore nossos cases', 'to' => 'page:cases' ),
					),
					'partners'     => array(
						'enabled' => true,
						'title'   => 'Visões diferentes. Um propósito conectado.',
						'text'    => 'À frente da Full, diferentes experiências e perspectivas se encontram para construir uma visão integrada de comunicação, marketing e negócios.',
					),
					'testimonials' => array(
						'enabled' => true,
						'title'   => 'Conexões que falam por si.',
						'text'    => 'Mais do que projetos, construímos relações. E são as pessoas que vivem essas experiências que melhor podem contar suas histórias.',
					),
					'team'         => array(
						'enabled' => true,
						'title'   => 'Por trás de cada conexão, tem gente que faz acontecer.',
						'text'    => 'Diferentes talentos, repertórios e perspectivas. Um time que transforma colaboração em ideias, experiências e resultados.',
					),
					'insights'     => array(
						'enabled' => true,
						'title'   => 'Ideias provocam. Conexões transformam.',
						'text'    => 'Perspectivas sobre marcas, comportamento, criatividade, tecnologia e negócios. Conteúdos para ampliar repertórios e descobrir novas possibilidades.',
						'cta'     => array( 'label' => 'Explore nossos insights', 'to' => 'page:insights' ),
					),
					'footer_cta'   => array(
						'enabled' => true,
						'title'   => 'Grandes resultados começam com uma conexão.',
						'text'    => 'Seu próximo desafio pode ser o ponto de partida para algo extraordinário. Vamos descobrir juntos?',
						'cta'     => $talk,
					),
					'seo'          => array(
						'title'       => 'Full Connect Key | Estratégia, Criatividade e Resultados',
						'description' => 'Conectamos estratégia, criatividade, tecnologia, marketing e vendas para transformar desafios em oportunidades e resultados.',
					),
				),
			),

			'sm'        => array(
				'title'    => 'S&M',
				'slug'     => 'sm',
				'template' => 'sm',
				'status'   => 'publish',
				'order'    => 1,
				'meta'     => array(
					'hero'   => array(
						'headline' => 'Negócios evoluem. Pessoas mudam. Conexões transformam.',
						'lead'     => 'S&M é o nosso método de compreender negócios, identificar oportunidades e construir estratégias que conectam produtos e serviços aos desejos e interesses das pessoas. Integramos marketing e vendas para aprimorar cada ponto de contato entre marcas e consumidores, transformando conhecimento em ações e ações em resultados.',
					),
					'method' => array(
						'title' => 'Entender. Conectar. Amplificar.',
						'intro' => 'Cada negócio possui seus próprios desafios, diferenciais e oportunidades. Por isso, nosso trabalho começa com um olhar atento à realidade de cada cliente e evolui por meio de estratégias integradas, testes e aprimoramento contínuo.',
						'steps' => array(
							array( 'name' => 'Entender', 'headline' => 'Antes de criar conexões, precisamos compreender o que as movimenta.', 'text' => 'Investigamos objetivos, particularidades, diferenciais e desafios de cada negócio. Observamos o mercado, o comportamento das pessoas e os pontos de contato que influenciam suas decisões.' ),
							array( 'name' => 'Conectar', 'headline' => 'Estratégias ganham força quando fazem sentido para as pessoas.', 'text' => 'Transformamos conhecimento em estratégias que integram comunicação, marketing e vendas. Conectamos marcas aos interesses de seus públicos, criando experiências e oportunidades relevantes ao longo da jornada.' ),
							array( 'name' => 'Amplificar', 'headline' => 'O que funciona pode evoluir ainda mais.', 'text' => 'Acompanhamos resultados, testamos possibilidades e aprimoramos continuamente as conexões construídas. Identificamos o que gera valor e direcionamos esforços para ampliar seu impacto.' ),
						),
					),
					'cta'    => array(
						'enabled' => true,
						'title'   => 'Esta é a chave.',
						'text'    => 'Entender as mudanças, conectar oportunidades e aprimorar resultados. Uma maneira integrada de pensar e atuar para que marcas e consumidores evoluam juntos.',
						'cta'     => array( 'label' => 'Vamos conectar seu negócio?', 'to' => 'page:contact' ),
					),
					'seo'    => array(
						'title'       => 'Método S&M | Full Connect Key',
						'description' => 'Conheça o método S&M da Full Connect Key, que integra conhecimento, estratégia, marketing e vendas para aprimorar conexões e resultados.',
					),
				),
			),

			'ecosystem' => array(
				'title'    => 'Ecossistema Full',
				'slug'     => 'ecossistema',
				'template' => 'ecosystem',
				'status'   => 'publish',
				'order'    => 2,
				'meta'     => array(
					'hero'      => array(
						'headline'     => 'Seu negócio no centro. Todas as possibilidades ao redor.',
						'headline_alt' => 'Um ecossistema. Múltiplas possibilidades.',
						'lead'         => 'Cada negócio tem seus próprios desafios. O Ecossistema Full conecta soluções de gestão, performance, geração de oportunidades, inteligência de marketing e relacionamento para responder a diferentes necessidades. O ponto de partida é sempre o mesmo: o seu negócio.',
					),
					'solutions' => array(
						'title'        => 'Soluções que se conectam ao que o seu negócio precisa.',
						'intro'        => 'Com o método S&M orientando nossa atuação, integramos plataformas que organizam processos, otimizam investimentos, conectam oportunidades comerciais e ajudam a ampliar resultados.',
						'center_label' => 'Seu negócio',
						'link_label'   => '', // não consta do briefing — usa o nome da plataforma
					),
					'cta'       => array(
						'enabled' => true,
						'title'   => 'Qual conexão pode transformar o seu negócio?',
						'text'    => 'As melhores soluções começam com a compreensão dos desafios certos. Vamos descobrir quais possibilidades fazem sentido para você.',
						'cta'     => $talk,
					),
					'seo'       => array(
						'title'       => 'Ecossistema Full | Soluções Conectadas para Negócios',
						'description' => 'Conheça as plataformas Full Hub, Full Dash, Full Leads, FullMix e FullMatch e descubra soluções para diferentes desafios do seu negócio.',
					),
				),
			),

			'cases'     => array(
				'title'    => 'Cases',
				'slug'     => 'cases',
				'template' => 'cases',
				'status'   => 'publish',
				'order'    => 3,
				'meta'     => array(
					'hero'    => array(
						'headline' => 'Ideias se conectam. Resultados aparecem.',
						'lead'     => 'Conheça projetos em que estratégia, criatividade e inteligência se conectam para transformar desafios em oportunidades.',
						'eyebrow'  => 'Explore nossos projetos',
					),
					'listing' => array(
						'filter_all' => 'Todos',
						'view_label' => 'Ver projeto',
						'load_more'  => 'Carregar mais projetos',
						'empty'      => 'Ainda não há projetos publicados nesta categoria no momento.',
						'page_size'  => 9,
					),
					'case'    => array(
						'challenge_label' => 'O desafio',
						'idea_label'      => 'A ideia',
						'approach_label'  => 'Nossa atuação',
						'gallery_title'   => 'Uma ideia. Diferentes pontos de conexão.',
						'results_title'   => 'Quando estratégia e criatividade se conectam, o impacto aparece.',
						'next_title'      => 'Continue explorando nossas conexões.',
						'next_label'      => 'Conhecer próximo projeto',
					),
					'cta'     => array(
						'enabled' => true,
						'title'   => 'O próximo case pode ser o seu.',
						'text'    => 'Toda transformação começa com um desafio. Qual é o próximo que vamos enfrentar juntos?',
						'cta'     => $talk,
					),
					'seo'     => array(
						'title'       => 'Cases | Projetos Full Connect Key',
						'description' => 'Conheça projetos que conectam estratégia, criatividade e inteligência para transformar desafios em oportunidades.',
					),
				),
			),

			'insights'  => array(
				'title'    => 'Insights Full',
				'slug'     => 'insights',
				'template' => 'insights',
				'status'   => 'publish',
				'order'    => 4,
				'meta'     => array(
					'hero'    => array(
						'headline' => 'Ideias provocam. Conexões transformam.',
						'lead'     => 'Perspectivas sobre marcas, comportamento, criatividade, tecnologia e negócios. Conteúdos para ampliar repertórios e descobrir novas possibilidades.',
						'eyebrow'  => 'Explore novas perspectivas',
					),
					'listing' => array(
						'featured_label' => 'Em destaque',
						'others_label'   => 'Outras perspectivas',
						'read_label'     => 'Ler artigo',
						'grid_title'     => 'O que movimenta suas ideias?',
						'grid_text'      => 'Diferentes olhares sobre os assuntos que transformam a maneira como marcas e pessoas se conectam.',
						'filter_all'     => 'Todos',
						'continue_label' => 'Continuar lendo',
						'load_more'      => 'Carregar mais artigos',
						'empty'          => 'Novas perspectivas estão a caminho. Explore outros conteúdos enquanto isso.',
						'page_size'      => 9,
					),
					'article' => array(
						'related_title' => 'Continue explorando novas perspectivas.',
						'related_text'  => 'Outras ideias e análises para ampliar seu olhar sobre marcas, comunicação e negócios.',
						'related_cta'   => array( 'label' => 'Ver mais insights', 'to' => 'page:insights' ),
					),
					'cta'     => array(
						'enabled' => true,
						'title'   => 'O próximo grande insight pode surgir de uma conversa.',
						'text'    => 'Compartilhe seus desafios e descubra novas possibilidades para o seu negócio.',
						'cta'     => $talk,
					),
					'seo'     => array(
						'title'       => 'Insights Full | Estratégia, Marcas e Negócios',
						'description' => 'Explore ideias e perspectivas sobre branding, comportamento, criatividade, tecnologia, marketing e negócios.',
					),
				),
			),

			'press'     => array(
				'title'    => 'Full na Mídia',
				'slug'     => 'full-na-midia',
				'template' => 'press',
				'status'   => 'publish',
				'order'    => 5,
				'meta'     => array(
					'hero'    => array(
						'headline' => 'Boas histórias ganham o mundo.',
						'lead'     => 'Notícias, entrevistas e publicações que ampliam as conversas sobre a Full Connect Key, nossas ideias e nossa atuação no mercado.',
						'eyebrow'  => 'Explore nossas publicações',
					),
					'archive' => array(
						'title'            => 'O que estão falando sobre a Full.',
						'text'             => 'Nossa presença em diferentes veículos, conversas e espaços de comunicação.',
						'read_original'    => 'Ler publicação original',
						'others_label'     => 'Outras publicações',
						'archive_title'    => 'Nossa presença na mídia',
						'access_label'     => 'Acessar matéria',
						'load_more'        => 'Ver mais publicações',
						'filter_all'       => 'Todas',
						'filter_interview' => 'Entrevistas',
						'filter_report'    => 'Reportagens',
						'filter_article'   => 'Artigos',
						'empty'            => 'Não há publicações disponíveis nesta categoria no momento.',
						'threshold'        => 6,
					),
					'cta'     => array(
						'enabled' => true,
						'title'   => 'Tem uma boa história para contar com a gente?',
						'text'    => 'Para entrevistas, pautas, parcerias editoriais e oportunidades de comunicação, entre em contato com a Full.',
						'cta'     => array( 'label' => 'Fale com a Full', 'to' => 'url:/contato/?assunto=imprensa' ),
					),
					'seo'     => array(
						'title'       => 'Full na Mídia | Notícias e Publicações',
						'description' => 'Confira entrevistas, notícias e publicações sobre a Full Connect Key e sua atuação no mercado.',
					),
				),
			),

			'contact'   => array(
				'title'    => 'Contato',
				'slug'     => 'contato',
				'template' => 'contact',
				'status'   => 'publish',
				'order'    => 6,
				'meta'     => array(
					'hero' => array(
						'headline' => 'Toda grande ideia começa com uma conexão.',
						'lead'     => 'Conte para a gente o que movimenta o seu negócio. Vamos descobrir juntos quais possibilidades podemos criar.',
						'eyebrow'  => 'Vamos nos conectar?',
					),
					'form' => array(
						'step1_title'         => 'O que te trouxe até aqui?',
						'step1_text'          => 'Escolha o assunto que melhor representa o início da nossa conversa.',
						'subjects'            => array(
							array( 'value' => 'projeto', 'title' => 'Quero desenvolver um projeto.', 'description' => 'Estratégia, comunicação, marketing ou soluções Full.' ),
							array( 'value' => 'imprensa', 'title' => 'Sou da imprensa.', 'description' => 'Entrevistas, pautas e oportunidades editoriais.' ),
							array( 'value' => 'parceria', 'title' => 'Quero propor uma parceria.', 'description' => 'Novas ideias e possibilidades de colaboração.' ),
							array( 'value' => 'outro', 'title' => 'Outro assunto.', 'description' => 'Vamos descobrir como podemos ajudar.' ),
						),
						'continue'            => 'Continuar',
						'step2_title'         => 'Agora, conta um pouco mais.',
						'step2_text'          => 'Queremos entender sua ideia e saber como podemos entrar em contato.',
						'name_label'          => 'Seu nome',
						'name_placeholder'    => 'Como podemos te chamar?',
						'email_label'         => 'Seu e-mail',
						'email_placeholder'   => 'voce@empresa.com.br',
						'company_label'       => 'Sua empresa (opcional)',
						'company_placeholder' => 'Nome da empresa',
						'message_label'       => 'Conte sua ideia',
						'message_placeholder' => 'Qual é o seu desafio ou objetivo?',
						'consent'             => 'Ao enviar, você concorda com o tratamento dos dados informados para que a Full Connect Key possa responder à sua solicitação, conforme nossa Política de Privacidade.',
						'consent_link'        => 'Política de Privacidade',
						'back'                => 'Voltar',
						'submit'              => 'Enviar mensagem',
						'success_title'       => 'Sua conexão com a Full começou.',
						'success_text'        => 'Recebemos sua mensagem. Nossa equipe entrará em contato pelos dados informados.',
						'success_cta'         => 'Voltar para a Home',
					),
					'seo'  => array(
						'title'       => 'Contato | Full Connect Key',
						'description' => 'Entre em contato com a Full Connect Key e descubra como conectar novas possibilidades ao seu negócio.',
					),
				),
			),

			// Páginas legais: estrutura pronta, em RASCUNHO até texto jurídico aprovado.
			'privacy'   => array(
				'title'    => 'Política de Privacidade',
				'slug'     => 'politica-de-privacidade',
				'template' => 'legal',
				'status'   => 'draft',
				'order'    => 90,
				'meta'     => array( 'legal' => array( 'title' => 'Política de Privacidade', 'updated_label' => 'Atualizado em' ) ),
			),
			'cookies'   => array(
				'title'    => 'Política de Cookies',
				'slug'     => 'politica-de-cookies',
				'template' => 'legal',
				'status'   => 'draft',
				'order'    => 91,
				'meta'     => array( 'legal' => array( 'title' => 'Política de Cookies', 'updated_label' => 'Atualizado em' ) ),
			),
		),

		// ------------------------------------------------------------------
		'platforms'  => array(
			'hub'   => array(
				'title' => 'Full Hub',
				'slug'  => 'full-hub',
				'order' => 1,
				'meta'  => array(
					'identity' => array(
						'product'      => 'hub',
						'signature'    => 'O jeito Full de atender.',
						'summary'      => 'Aprovações, solicitações, responsáveis e prazos conectados em um só lugar.',
						'eco_headline' => 'Aprovação por WhatsApp não é processo. É sorte.',
						'eco_text'     => 'Gestão integrada da operação, com solicitações, aprovações, responsáveis, prazos e status visíveis para a Full e seus clientes.',
					),
					'hero'     => array(
						'headline' => 'Aprovação por WhatsApp não é processo. É sorte.',
						'lead'     => 'Solicitações espalhadas, mensagens perdidas e prazos difíceis de acompanhar não precisam fazer parte da rotina. O Full Hub integra a gestão da operação entre a Full e seus clientes, reunindo demandas, aprovações, responsáveis e prazos em um só lugar.',
					),
					'solution' => array(
						'title' => 'Menos ruído. Mais fluxo.',
						'intro' => 'Quando todos acompanham as mesmas informações, o trabalho ganha organização, transparência e fluidez.',
						'items' => array(
							array( 'label' => 'Solicitações', 'headline' => 'Toda demanda começa no lugar certo.', 'text' => 'Solicitações organizadas em um ambiente compartilhado, facilitando o acompanhamento e a comunicação entre a equipe e o cliente.' ),
							array( 'label' => 'Responsáveis e prazos', 'headline' => 'Cada tarefa tem seu responsável. E seu momento de acontecer.', 'text' => 'Demandas com responsáveis, prazos e status definidos, trazendo mais clareza para a rotina de trabalho.' ),
							array( 'label' => 'Aprovações', 'headline' => 'Aprovar também faz parte do processo.', 'text' => 'Etapas de aprovação integradas ao fluxo de trabalho, reduzindo a dispersão de informações e facilitando o acompanhamento das entregas.' ),
							array( 'label' => 'Visibilidade', 'headline' => 'Todo mundo conectado ao andamento do projeto.', 'text' => 'Equipe e cliente acompanham as informações relevantes da operação em um ambiente organizado.' ),
						),
					),
					'cta'      => array(
						'title' => 'Menos mensagens perdidas. Mais negócios em movimento.',
						'text'  => 'Uma operação conectada cria espaço para o que realmente importa: fazer os projetos avançarem.',
						'cta'   => array( 'label' => 'Conversar sobre o Full Hub', 'to' => 'url:/contato/?assunto=projeto' ),
					),
					'seo'      => array(
						'title'       => 'Full Hub | Gestão Integrada de Demandas e Aprovações',
						'description' => 'Organize solicitações, responsáveis, prazos e aprovações em uma gestão integrada entre a Full e seus clientes.',
					),
				),
			),
			'dash'  => array(
				'title' => 'Full Dash',
				'slug'  => 'full-dash',
				'order' => 2,
				'meta'  => array(
					'identity'    => array(
						'product'      => 'dash',
						'signature'    => 'Verba vai para o que gera resultado.',
						'summary'      => 'Gestão de mídia, acompanhamento de metas e otimização contínua dos investimentos.',
						'eco_headline' => 'Verba vai para o que gera resultado.',
						'eco_text'     => 'Gestão e performance de mídia com otimização contínua, metas claras e acompanhamento de indicadores de negócio.',
					),
					'hero'        => array(
						'headline' => 'Sua mídia gera números. Mas gera negócios?',
						'lead'     => 'Impressões, cliques e alcance ajudam a acompanhar campanhas. Mas o que realmente importa é entender como o investimento contribui para os objetivos do negócio. O Full Dash conecta gestão de mídia, metas e otimização contínua para orientar decisões mais inteligentes.',
					),
					'solution'    => array(
						'title' => 'Mais clareza para decidir. Mais inteligência para investir.',
						'items' => array(
							array( 'label' => 'Gestão de mídia', 'headline' => 'Investimento com direção.', 'text' => 'Acompanhamos campanhas e investimentos de mídia de forma integrada, conectando a operação aos objetivos definidos para cada negócio.' ),
							array( 'label' => 'Otimização contínua', 'headline' => 'Resultado não se acompanha apenas no fim do mês.', 'text' => 'Analisamos a performance no dia a dia, identificando oportunidades de ajuste e direcionando esforços para melhorar os resultados.' ),
							array( 'label' => 'Resultados de negócio', 'headline' => 'Além dos indicadores de mídia.', 'text' => 'Dashboards e relatórios conectam metas, investimentos e resultados para apoiar decisões com uma visão mais próxima do negócio.' ),
						),
					),
					'interaction' => array(
						'illustrative_note' => '', // usa o selo "Ilustrativo" dos textos de interface
					),
					'cta'         => array(
						'title' => 'Não basta investir mais. É preciso investir melhor.',
						'text'  => 'Transforme o acompanhamento de mídia em decisões mais claras e investimentos mais inteligentes.',
						'cta'   => array( 'label' => 'Conversar sobre o Full Dash', 'to' => 'url:/contato/?assunto=projeto' ),
					),
					'seo'         => array(
						'title'       => 'Full Dash | Gestão e Otimização de Mídia',
						'description' => 'Gestão de mídia, acompanhamento de metas e otimização contínua para conectar investimentos a resultados de negócio.',
					),
				),
			),
			'leads' => array(
				'title' => 'Full Leads',
				'slug'  => 'full-leads',
				'order' => 3,
				'meta'  => array(
					'identity' => array(
						'product'      => 'leads',
						'signature'    => 'Oportunidade pronta para fechar.',
						'summary'      => 'Atração, captura e nutrição conectadas ao funil comercial.',
						'eco_headline' => 'Oportunidade pronta para fechar.',
						'eco_text'     => 'Estratégias de atração, captura e nutrição conectadas ao funil comercial para aproximar o time de vendas de oportunidades qualificadas.',
					),
					'hero'     => array(
						'headline' => 'Atrair é só o começo. O negócio é converter.',
						'lead'     => 'Gerar interesse é importante. Transformá-lo em oportunidade comercial é o que aproxima marketing e vendas. O Full Leads conecta atração, captura e nutrição ao funil comercial para desenvolver oportunidades mais preparadas para a atuação do time de vendas.',
					),
					'solution' => array(
						'title' => 'Cada conexão pode ser uma oportunidade.',
						'intro' => 'Da descoberta da marca à aproximação comercial, diferentes pontos de contato podem trabalhar juntos para transformar interesse em oportunidades.',
						'items' => array(
							array( 'label' => 'Atrair', 'headline' => 'Tudo começa despertando o interesse certo.', 'text' => 'Estratégias de atração aproximam o negócio de pessoas com potencial de se tornarem clientes.' ),
							array( 'label' => 'Capturar', 'headline' => 'Interesse precisa virar conexão.', 'text' => 'A captura transforma interações em contatos que podem ser desenvolvidos ao longo da jornada comercial.' ),
							array( 'label' => 'Nutrir', 'headline' => 'Relacionamentos precisam evoluir.', 'text' => 'A nutrição mantém o contato ativo e desenvolve oportunidades por meio de comunicações relevantes.' ),
							array( 'label' => 'Vendas', 'headline' => 'Oportunidades certas. No momento certo.', 'text' => 'A integração com o funil comercial aproxima marketing e vendas, ajudando o time comercial a trabalhar oportunidades mais qualificadas.' ),
						),
					),
					'cta'      => array(
						'title' => 'Não basta gerar contatos. É preciso gerar oportunidades.',
						'text'  => 'Conecte suas estratégias de atração ao processo comercial e aproxime sua equipe de vendas das oportunidades certas.',
						'cta'   => array( 'label' => 'Conversar sobre o Full Leads', 'to' => 'url:/contato/?assunto=projeto' ),
					),
					'seo'      => array(
						'title'       => 'Full Leads | Geração de Leads e Oportunidades Comerciais',
						'description' => 'Conecte atração, captura e nutrição ao funil comercial para desenvolver oportunidades mais qualificadas.',
					),
				),
			),
			'mix'   => array(
				'title' => 'FullMix',
				'slug'  => 'fullmix',
				'order' => 4,
				'meta'  => array(
					'identity'    => array(
						'product'      => 'mix',
						'signature'    => 'Saiba o que cada canal vende.',
						'summary'      => 'Inteligência para entender quanto cada canal contribui para as vendas.',
						'eco_headline' => 'Saiba o que cada canal vende.',
						'eco_text'     => 'Marketing Mix Modeling para analisar a contribuição dos canais online e offline para as vendas e orientar a distribuição dos investimentos.',
					),
					'hero'        => array(
						'headline' => 'Você sabe onde investe. Mas sabe o que realmente vende?',
						'lead'     => 'Cada canal de marketing desempenha um papel na construção dos resultados. Entender essa contribuição é essencial para tomar decisões mais inteligentes. O FullMix utiliza Marketing Mix Modeling para analisar quanto os canais online e offline contribuem para as vendas e orientar a composição do mix e a distribuição do orçamento.',
					),
					'solution'    => array(
						'title' => 'Cada canal tem seu papel. Descubra qual.',
						'intro' => 'Mais do que acompanhar investimentos isolados, é preciso compreender como os diferentes canais contribuem para os resultados do negócio.',
						'items' => array(
							array( 'label' => 'Mensurar', 'headline' => 'Entenda a contribuição de cada canal.', 'text' => 'A análise estatística de dados históricos ajuda a estimar como diferentes investimentos em marketing contribuem para as vendas.' ),
							array( 'label' => 'Comparar', 'headline' => 'Enxergue o mix como um todo.', 'text' => 'Compreenda a participação dos canais online e offline e identifique oportunidades de melhoria na composição dos investimentos.' ),
							array( 'label' => 'Orientar', 'headline' => 'Decida com mais inteligência.', 'text' => 'Utilize os resultados das análises para apoiar decisões sobre alocação de orçamento e planejamento de marketing.' ),
						),
					),
					'interaction' => array(
						'illustrative_note' => 'Esta experiência é ilustrativa. As proporções exibidas não representam previsões de vendas ou recomendações reais de investimento.',
						'simulator_intro'   => 'Explore diferentes distribuições de investimento e entenda por que conhecer a participação de cada canal é importante para tomar decisões.',
						'total_label'       => 'Total distribuído', // (*) rótulo de interface
						'reset_label'       => 'Restaurar distribuição', // (*) rótulo de interface
						// Canais genéricos para a experiência ilustrativa (revisar com a Full).
						'channels'          => array(
							array( 'name' => 'TV', 'kind' => 'offline', 'share' => 20 ),
							array( 'name' => 'Rádio', 'kind' => 'offline', 'share' => 10 ),
							array( 'name' => 'Mídia exterior', 'kind' => 'offline', 'share' => 10 ),
							array( 'name' => 'Busca paga', 'kind' => 'online', 'share' => 25 ),
							array( 'name' => 'Redes sociais', 'kind' => 'online', 'share' => 25 ),
							array( 'name' => 'Mídia programática', 'kind' => 'online', 'share' => 10 ),
						),
					),
					'cta'         => array(
						'title' => 'Seu próximo investimento merece mais inteligência.',
						'text'  => 'Entenda a contribuição dos canais e transforme informação em decisões mais estratégicas para o seu negócio.',
						'cta'   => array( 'label' => 'Conversar sobre o FullMix', 'to' => 'url:/contato/?assunto=projeto' ),
					),
					'seo'         => array(
						'title'       => 'FullMix | Marketing Mix Modeling',
						'description' => 'Entenda a contribuição dos canais online e offline para as vendas e tome decisões mais inteligentes sobre investimentos em marketing.',
					),
				),
			),
			'match' => array(
				'title' => 'FullMatch',
				'slug'  => 'fullmatch',
				'order' => 5,
				'meta'  => array(
					'identity'    => array(
						'product'      => 'match',
						'signature'    => 'A oferta certa para quem já comprou.',
						'summary'      => 'Ofertas hiperpersonalizadas para estimular recorrência, ticket médio e reativação.',
						'eco_headline' => 'A oferta certa para quem já comprou.',
						'eco_text'     => 'Inteligência de comportamento de compra para personalizar ofertas, estimular a recorrência, aumentar o ticket médio e reativar clientes.',
					),
					'hero'        => array(
						'headline' => 'A primeira compra não precisa ser a última.',
						'lead'     => 'Cada compra revela interesses, preferências e oportunidades de relacionamento. O FullMatch analisa o comportamento de compra para desenvolver ofertas hiperpersonalizadas, estimular a recorrência, aumentar o ticket médio e reativar clientes que deixaram de comprar.',
					),
					'solution'    => array(
						'title'      => 'Cada cliente é único. A próxima oferta também deveria ser.',
						'intro'      => 'Relacionamentos mais relevantes começam quando as marcas compreendem o comportamento de seus consumidores e utilizam esse conhecimento para criar novas oportunidades.',
						'items'      => array(
							array( 'label' => 'Recorrente', 'headline' => 'Quem já compra pode encontrar novos motivos para voltar.', 'text' => 'O histórico de compras ajuda a identificar interesses e oportunidades para estimular novas compras.' ),
							array( 'label' => 'Alto valor', 'headline' => 'Mais relevância em cada nova oferta.', 'text' => 'O comportamento de compra pode orientar recomendações complementares e oportunidades para aumentar o valor das compras.' ),
							array( 'label' => 'Inativo', 'headline' => 'Uma conexão pode ser retomada.', 'text' => 'Identificar consumidores que deixaram de comprar permite desenvolver ações de reativação mais relevantes.' ),
						),
						'complement' => 'Não se trata de oferecer a mesma promoção para todo mundo. Trata-se de compreender diferentes comportamentos para criar ofertas mais relevantes.',
					),
					'interaction' => array(
						// Rótulos e detalhes por perfil não constam do briefing: ficam vazios (pendência).
					),
					'cta'         => array(
						'title' => 'Quem já comprou pode ter muito mais para viver com sua marca.',
						'text'  => 'Transforme informações sobre comportamento de compra em oportunidades de recorrência, reativação e crescimento.',
						'cta'   => array( 'label' => 'Conversar sobre o FullMatch', 'to' => 'url:/contato/?assunto=projeto' ),
					),
					'seo'         => array(
						'title'       => 'FullMatch | Personalização e Fidelização de Clientes',
						'description' => 'Utilize o comportamento de compra para criar ofertas hiperpersonalizadas, estimular recorrência e reativar clientes.',
					),
				),
			),
		),

		// ------------------------------------------------------------------
		'sectors'    => array(
			'varejo'      => array( 'title' => 'Varejo', 'order' => 1, 'meta' => array( 'details' => array( 'title' => 'Marcas presentes em cada escolha.', 'text' => 'Do posicionamento à experiência de compra, conectamos comunicação, pontos de contato e estratégias comerciais para aproximar marcas e consumidores.' ) ) ),
			'imobiliario' => array( 'title' => 'Imobiliário', 'order' => 2, 'meta' => array( 'details' => array( 'title' => 'Conexões que transformam espaços em desejos.', 'text' => 'Da construção de uma marca imobiliária à geração de oportunidades comerciais, criamos estratégias que valorizam empreendimentos e acompanham diferentes momentos da jornada de compra.' ) ) ),
			'feiras-e-eventos' => array( 'title' => 'Feiras e Eventos', 'order' => 3, 'meta' => array( 'details' => array( 'title' => 'Experiências que continuam além do evento.', 'text' => 'Conectamos estratégia, comunicação e experiência para atrair públicos, fortalecer marcas e ampliar as oportunidades de negócio antes, durante e depois de cada evento.' ) ) ),
		),

		// Logos oficiais (arquivos em seed/logos/): "escuro" = texto branco,
		// para fundo escuro; "claro" = texto preto, para fundo claro.
		// Só preenchem o campo se ele estiver vazio.
		'logos'      => array(
			'full-escuro'          => array( 'file' => 'full-connect-key-escuro.svg', 'alt' => 'Full Connect Key', 'target' => 'site:brand:logo_dark' ),
			'full-claro'           => array( 'file' => 'full-connect-key-claro.svg', 'alt' => 'Full Connect Key', 'target' => 'site:brand:logo_light' ),
			'full-compacto-claro'  => array( 'file' => 'full-compacto-claro.svg', 'alt' => 'Full Connect Key', 'target' => 'site:brand:logo_compact_light' ),
			'hub-escuro'           => array( 'file' => 'full-hub-escuro.svg', 'alt' => 'Full Hub', 'target' => 'platform:hub:logo' ),
			'hub-claro'            => array( 'file' => 'full-hub-claro.svg', 'alt' => 'Full Hub', 'target' => 'platform:hub:logo_light' ),
			'dash-escuro'          => array( 'file' => 'full-dash-escuro.svg', 'alt' => 'Full Dash', 'target' => 'platform:dash:logo' ),
			'dash-claro'           => array( 'file' => 'full-dash-claro.svg', 'alt' => 'Full Dash', 'target' => 'platform:dash:logo_light' ),
			'leads-escuro'         => array( 'file' => 'full-leads-escuro.svg', 'alt' => 'Full Leads', 'target' => 'platform:leads:logo' ),
			'leads-claro'          => array( 'file' => 'full-leads-claro.svg', 'alt' => 'Full Leads', 'target' => 'platform:leads:logo_light' ),
			'mix-escuro'           => array( 'file' => 'fullmix-escuro.svg', 'alt' => 'FullMix', 'target' => 'platform:mix:logo' ),
			'mix-claro'            => array( 'file' => 'fullmix-claro.svg', 'alt' => 'FullMix', 'target' => 'platform:mix:logo_light' ),
			'match-escuro'         => array( 'file' => 'fullmatch-escuro.svg', 'alt' => 'FullMatch', 'target' => 'platform:match:logo' ),
			'match-claro'          => array( 'file' => 'fullmatch-claro.svg', 'alt' => 'FullMatch', 'target' => 'platform:match:logo_light' ),
		),

		'categories' => array(
			'estrategia'    => 'Estratégia',
			'branding'      => 'Branding',
			'tecnologia'    => 'Tecnologia',
			'comportamento' => 'Comportamento',
			'negocios'      => 'Negócios',
		),

		// ------------------------------------------------------------------
		// Menus (Aparência › Menus). Itens apontando para páginas em rascunho
		// não aparecem no site até a publicação.
		'menus'      => array(
			'primary' => array(
				'name'  => 'Menu principal',
				'items' => array(
					array( 'label' => 'S&M', 'to' => 'page:sm' ),
					array(
						'label'    => 'Ecossistema Full',
						'to'       => 'page:ecosystem',
						'children' => array(
							array( 'label' => 'Visão geral', 'to' => 'page:ecosystem' ),
							array( 'label' => 'Full Hub', 'to' => 'platform:hub' ),
							array( 'label' => 'Full Dash', 'to' => 'platform:dash' ),
							array( 'label' => 'Full Leads', 'to' => 'platform:leads' ),
							array( 'label' => 'FullMix', 'to' => 'platform:mix' ),
							array( 'label' => 'FullMatch', 'to' => 'platform:match' ),
						),
					),
					array( 'label' => 'Cases', 'to' => 'page:cases' ),
					array( 'label' => 'Insights Full', 'to' => 'page:insights' ),
					array( 'label' => 'Full na Mídia', 'to' => 'page:press' ),
				),
			),
			'footer'  => array(
				'name'  => 'Rodapé',
				'items' => array(
					array(
						'label'    => 'Full Connect Key', // título do grupo (não clicável)
						'to'       => 'group',
						'children' => array(
							array( 'label' => 'S&M', 'to' => 'page:sm' ),
							array( 'label' => 'Cases', 'to' => 'page:cases' ),
							array( 'label' => 'Insights Full', 'to' => 'page:insights' ),
							array( 'label' => 'Full na Mídia', 'to' => 'page:press' ),
							array( 'label' => 'Vamos conversar', 'to' => 'page:contact' ),
						),
					),
					array(
						'label'    => 'Ecossistema Full',
						'to'       => 'group',
						'children' => array(
							array( 'label' => 'Visão geral', 'to' => 'page:ecosystem' ),
							array( 'label' => 'Full Hub', 'to' => 'platform:hub' ),
							array( 'label' => 'Full Dash', 'to' => 'platform:dash' ),
							array( 'label' => 'Full Leads', 'to' => 'platform:leads' ),
							array( 'label' => 'FullMix', 'to' => 'platform:mix' ),
							array( 'label' => 'FullMatch', 'to' => 'platform:match' ),
						),
					),
				),
			),
			'legal'   => array(
				'name'  => 'Links legais',
				'items' => array(
					array( 'label' => 'Política de Privacidade', 'to' => 'page:privacy' ),
					array( 'label' => 'Política de Cookies', 'to' => 'page:cookies' ),
				),
			),
		),
	);
}
