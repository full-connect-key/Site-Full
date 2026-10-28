<?php
declare(strict_types=1);
/**
 * Conteúdo inicial (seed) — textos aprovados da seção 6 do briefing.
 * São valores iniciais EDITÁVEIS no painel. Pendências "[PENDENTE]" não
 * entram aqui: viram campos vazios e itens em docs/CONTENT_GAPS.md.
 *
 * Links: 'page:<chave>' aponta para uma página do seed; 'platform:<produto>'
 * para uma plataforma; 'url:/caminho' para um endereço fixo.
 */


require_once __DIR__ . '/platforms.php';

function fck_seed_content(): array {
	$talk = array( 'label' => 'Vamos conversar', 'to' => 'page:contact' );

	return array(
		// ------------------------------------------------------------------
		'site'       => array(
			'brand'        => array(
				'site_name' => 'Full Connect Key',
				'signature' => 'Vire a chave dos seus resultados. Ative o modo Full.', // D2 (adendo do Ecossistema)
			),
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
				'deploy_label'          => 'Versão {commit} · {data}', // (*) identificação do deploy no rodapé
			),
			// Páginas de plataforma (rótulos dos blocos, do adendo docs/BRIEFING_ECOSSISTEMA.md).
			'platform' => array(
				'how_link'          => 'Ver como funciona',
				'how_title'         => 'Como funciona',
				'benefits_title'    => 'Benefícios e diferenciais',
				'interface_title'   => 'Interface',
				'connections_title' => 'Conexões no ecossistema',
				'connection_link'   => 'Explore o {platform}',
				'independence'      => 'Cada solução pode ser contratada de forma independente. A maior força aparece quando elas trabalham conectadas.',
				'skip_animation'    => 'Pular animação',
				'claim_badge'       => 'A validar',
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
				'filter_by_sector' => 'Filtrar por setor', // (*)
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
						'headline'     => 'O que se conecta, multiplica.',
						'headline_alt' => '', // vazio = usa a assinatura global (Marca › Assinatura), D2
						'lead'         => 'Conectamos estratégia, criatividade, tecnologia, marketing e vendas para transformar desafios em oportunidades e oportunidades em resultados.',
						'cta'          => array( 'label' => 'Descubra a Full', 'to' => 'page:sm' ),
					),
					'logos'        => array( 'enabled' => true ),
					'credentials'  => array(
						'enabled' => true,
						'eyebrow' => 'Credenciais', // (*)
						'title'   => 'Credenciada pelas plataformas que movem o mercado.', // (*) sugestão do desenvolvimento, a aprovar
					),
					'essence'      => array(
						'enabled' => true,
						'title'   => 'Conexões que fazem negócios evoluírem.',
						'intro'   => "Não acreditamos em marketing e vendas trabalhando em caminhos separados.\n\nNa Full, estratégia, criatividade, tecnologia e inteligência comercial fazem parte de um mesmo processo. Nosso método Sales Marketing conecta o que sua marca precisa comunicar ao que seu negócio precisa conquistar.",
						'pillars' => array(
							array( 'name' => 'Conecta', 'text' => 'Entendemos os desafios do negócio e aproximamos marcas, pessoas e oportunidades por meio de estratégias relevantes.' ),
							array( 'name' => 'Organiza', 'text' => 'Integramos planejamento, equipes, processos e tecnologia para transformar estratégia em execução.' ),
							array( 'name' => 'Multiplica', 'text' => 'Acompanhamos resultados, identificamos oportunidades e evoluímos continuamente cada ação para impulsionar o crescimento.' ),
						),
					),
					'ecosystem'    => array(
						'enabled' => true,
						'title'   => 'Tecnologia própria. Possibilidades reais.',
						'text'    => 'Cinco plataformas que conectam inteligência, operação, performance e relacionamento. Soluções desenvolvidas para desafios diferentes, com um mesmo propósito: fazer seu negócio crescer.',
						'cta'     => array( 'label' => 'Conheça o ecossistema Full', 'to' => 'page:ecosystem' ),
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
						'title'   => 'Experiências diferentes. Uma visão de futuro.',
						'text'    => "Grandes ideias surgem do encontro de diferentes perspectivas.\n\nÀ frente da Full Connect Key, três profissionais que unem experiência em comunicação, marketing e negócios para transformar desafios em estratégias e estratégias em oportunidades de crescimento.",
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
						'text'    => 'Toda transformação começa com uma boa conversa. Conte para a gente quais são os desafios do seu negócio e descubra como podemos conectar estratégia, criatividade e tecnologia para ir além.',
						'cta'     => array( 'label' => 'Vamos conversar?', 'to' => 'page:contact' ),
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
						'headline'     => 'Cinco soluções. Infinitas possibilidades de conexão.',
						'headline_alt' => 'Seu negócio no centro. Todas as possibilidades ao redor.',
						'lead'         => 'Resultados consistentes não acontecem de maneira isolada. Eles surgem quando estratégia, tecnologia, pessoas e processos trabalham em conjunto. Por isso, a Full Connect Key desenvolveu um ecossistema de plataformas que conecta diferentes etapas do crescimento de uma marca.',
					),
					'solutions' => array(
						'title'          => 'Cada plataforma resolve um desafio específico. Juntas, transformam informação em decisão, decisão em ação e ação em resultados.',
						'intro'          => 'Da inteligência que orienta investimentos à gestão que organiza operações. Da mídia que evolui diariamente à geração de oportunidades comerciais. Do primeiro contato à construção de relacionamentos duradouros.',
						'center_label'   => 'Seu negócio',
						'link_label'     => 'Explore o {platform}',
						'connects_label' => 'Como se conecta',
						'independence'   => 'Cada solução pode ser contratada de forma independente. A maior força aparece quando elas trabalham conectadas.',
						'method_note'    => 'Com o método S&M orientando nossa atuação, cada plataforma se conecta ao que o seu negócio precisa.',
					),
					'cta'       => array(
						'enabled' => true,
						'title'   => 'O que se conecta, se multiplica.',
						'text'    => 'Inteligência para planejar. Tecnologia para executar. Estratégia para crescer.',
						'cta'     => $talk,
					),
					'seo'       => array(
						'title'       => 'Ecossistema Full | Soluções Conectadas para Negócios',
						'description' => 'Conheça as plataformas FullMix, FullDash, FullLeads, FullMatch e FullHub e descubra soluções para diferentes desafios do seu negócio.',
					),
				),
			),

			'cases'     => array(
				'title'    => 'Cases',
				'slug'     => 'cases',
				'template' => 'cases',
				'status'   => 'draft', // oculta a pedido do cliente (28/10/2026)
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
				'status'   => 'draft', // oculta a pedido do cliente (28/10/2026)
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
				'status'   => 'draft', // oculta a pedido do cliente (28/10/2026)
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
		// Plataformas: app/seed/platforms.php (adendo docs/BRIEFING_ECOSSISTEMA.md).
		'platforms'  => fck_seed_platforms(),

		// ------------------------------------------------------------------
		'sectors'    => array(
			'varejo'      => array( 'title' => 'Varejo', 'order' => 1, 'meta' => array( 'details' => array( 'title' => 'Marcas presentes em cada escolha.', 'text' => 'Da construção de marca à experiência no ponto de venda. Conectamos comunicação, campanhas e estratégias comerciais para aproximar marcas de consumidores.' ) ) ),
			'imobiliario' => array( 'title' => 'Imobiliário', 'order' => 2, 'meta' => array( 'details' => array( 'title' => 'Conexões que transformam espaços em desejos.', 'text' => 'Do posicionamento do empreendimento à geração de oportunidades. Criamos estratégias para acompanhar cada etapa da jornada de compra.' ) ) ),
			'feiras-e-eventos' => array( 'title' => 'Feiras e Eventos', 'order' => 3, 'meta' => array( 'details' => array( 'title' => 'Experiências que continuam além do evento.', 'text' => 'Da campanha de divulgação à experiência presencial. Conectamos marcas, públicos e oportunidades em projetos que vão além do evento.' ) ) ),
		),

		// Logos oficiais (arquivos em seed/logos/): "escuro" = texto branco,
		// para fundo escuro; "claro" = texto preto, para fundo claro.
		// Só preenchem o campo se ele estiver vazio.
		'logos'      => array(
			'full-escuro'          => array( 'file' => 'full-connect-key-escuro.svg', 'alt' => 'Full Connect Key', 'target' => 'site:brand:logo_dark' ),
			'full-claro'           => array( 'file' => 'full-connect-key-claro.svg', 'alt' => 'Full Connect Key', 'target' => 'site:brand:logo_light' ),
			'full-compacto-escuro' => array( 'file' => 'full-compacto-escuro.svg', 'alt' => 'Full Connect Key', 'target' => 'site:brand:logo_compact_dark' ),
			'full-compacto-claro'  => array( 'file' => 'full-compacto-claro.svg', 'alt' => 'Full Connect Key', 'target' => 'site:brand:logo_compact_light' ),
			'hub-escuro'           => array( 'file' => 'full-hub-escuro.svg', 'alt' => 'FullHub', 'target' => 'platform:hub:logo' ),
			'hub-claro'            => array( 'file' => 'full-hub-claro.svg', 'alt' => 'FullHub', 'target' => 'platform:hub:logo_light' ),
			'dash-escuro'          => array( 'file' => 'full-dash-escuro.svg', 'alt' => 'FullDash', 'target' => 'platform:dash:logo' ),
			'dash-claro'           => array( 'file' => 'full-dash-claro.svg', 'alt' => 'FullDash', 'target' => 'platform:dash:logo_light' ),
			'leads-escuro'         => array( 'file' => 'full-leads-escuro.svg', 'alt' => 'FullLeads', 'target' => 'platform:leads:logo' ),
			'leads-claro'          => array( 'file' => 'full-leads-claro.svg', 'alt' => 'FullLeads', 'target' => 'platform:leads:logo_light' ),
			'mix-escuro'           => array( 'file' => 'fullmix-escuro.svg', 'alt' => 'FullMix', 'target' => 'platform:mix:logo' ),
			'mix-claro'            => array( 'file' => 'fullmix-claro.svg', 'alt' => 'FullMix', 'target' => 'platform:mix:logo_light' ),
			'match-escuro'         => array( 'file' => 'fullmatch-escuro.svg', 'alt' => 'FullMatch', 'target' => 'platform:match:logo' ),
			'match-claro'          => array( 'file' => 'fullmatch-claro.svg', 'alt' => 'FullMatch', 'target' => 'platform:match:logo_light' ),
		),

		// Sócios (nomes informados pela Full em 23/10/2026). Entram como rascunho:
		// cargo, bio e retrato ainda pendentes — a seção só aparece com os três
		// publicados e com retrato.
		'partners'   => array(
			// Cargo, bio e retrato enviados pelo cliente (fotos em app/seed/people/, convertidas dos RAW originais).
			'rita-frare'     => array( 'title' => 'Rita Frare', 'photo' => array( 'file' => 'people/rita-frare.jpg', 'alt' => 'Retrato de Rita Frare', 'focal' => '50% 30%' ), 'order' => 1, 'meta' => array( 'details' => array( 'role' => 'Sócia e CFO', 'bio' => 'Mais de 25 anos liderando marketing, comercial e finanças. MBA em Gestão e Marketing Global (USP). Atendeu Unilever, P&G, ABF, Petrobras e SURA.' ) ) ),
			'emerson-aguiar' => array( 'title' => 'Emerson Aguiar', 'photo' => array( 'file' => 'people/emerson-aguiar.jpg', 'alt' => 'Retrato de Emerson Aguiar', 'focal' => '50% 30%' ), 'order' => 2, 'meta' => array( 'details' => array( 'role' => 'Sócio e CEO', 'bio' => 'Engenheiro, 10 anos em gestão comercial na distribuição industrial. Na publicidade desde 2004, com Casas Bahia, Burger King e Hershey’s. Fundou a Full em 2009.' ) ) ),
			'felipe-pela'    => array( 'title' => 'Felipe Pelá', 'photo' => array( 'file' => 'people/felipe-pela.jpg', 'alt' => 'Retrato de Felipe Pelá', 'focal' => '50% 30%' ), 'order' => 3, 'meta' => array( 'details' => array( 'role' => 'Sócio e CTO', 'bio' => 'Cientista da computação, fazendo sites desde 1999. Mais de 15 anos em marketing digital e centenas de campanhas em varejo, eventos, feiras e imobiliário.' ) ) ),
		),

		// Clientes da faixa de logos (logos brancos enviados pela Full em 24/10/2026,
		// em app/seed/clients/). Autorização de exibição de todos confirmada pela Full
		// na mesma data. Ordem = ordem de exibição.
		'clients'    => array(
			'sura' => array( 'title' => 'SURA', 'logo' => 'clients/sura.png', 'order' => 1 ),
			'shell-select' => array( 'title' => 'Shell Select', 'logo' => 'clients/shell-select.png', 'order' => 2 ),
			'oxxo' => array( 'title' => 'OXXO', 'logo' => 'clients/oxxo.png', 'order' => 4 ),
			'mundo-verde' => array( 'title' => 'Mundo Verde', 'logo' => 'clients/mundo-verde.png', 'order' => 5 ),
			'luxo-design' => array( 'title' => 'Luxo & Design', 'logo' => 'clients/luxo-design.png', 'order' => 6 ),
			'intermodal' => array( 'title' => 'Intermodal South America', 'logo' => 'clients/intermodal.png', 'order' => 7 ),
			'fispal-tecnologia' => array( 'title' => 'Fispal Tecnologia', 'logo' => 'clients/fispal-tecnologia.png', 'order' => 8 ),
			'tecnocarne' => array( 'title' => 'Tecnocarne', 'logo' => 'clients/tecnocarne.png', 'order' => 9 ),
			'dinner-in-the-sky' => array( 'title' => 'Dinner in the Sky', 'logo' => 'clients/dinner-in-the-sky.png', 'order' => 10 ),
			'avima' => array( 'title' => 'AVIMA Chocolate Orgânico', 'logo' => 'clients/avima.png', 'order' => 12 ),
			'ambev' => array( 'title' => 'Ambev', 'logo' => 'clients/ambev.png', 'order' => 13 ),
			'abf-franchising-expo' => array( 'title' => 'ABF Franchising Expo', 'logo' => 'clients/abf-franchising-expo.png', 'order' => 14 ),
			'4real' => array( 'title' => '4REAL', 'logo' => 'clients/4real.png', 'order' => 15 ),
			'ribeiro-caram' => array( 'title' => 'Ribeiro Caram', 'logo' => 'clients/ribeiro-caram.png', 'order' => 16 ),
			'qblz' => array( 'title' => 'QBLZ!', 'logo' => 'clients/qblz.png', 'order' => 17 ),
			'plastico-brasil' => array( 'title' => 'Plástico Brasil', 'logo' => 'clients/plastico-brasil.png', 'order' => 18 ),
			'catarinense-implantes' => array( 'title' => 'Catarinense Implantes', 'logo' => 'clients/catarinense-implantes.png', 'order' => 19 ),
			'nt-expo' => array( 'title' => 'NT Expo', 'logo' => 'clients/nt-expo.png', 'order' => 20 ),
		),

		// Time (enviado pela Full em 24/10/2026; foto em app/seed/people/, convertida do RAW
		// com a mesma luz e enquadramento dos sócios). Ordem = ordem de exibição.
		'team'       => array(
			'gabriela-oliveira' => array( 'title' => 'Gabriela Oliveira', 'photo' => array( 'file' => 'people/gabriela-oliveira.jpg', 'alt' => 'Retrato de Gabriela Oliveira', 'focal' => '50% 30%' ), 'order' => 1, 'meta' => array( 'details' => array( 'role' => 'Gerente de Projetos', 'connects' => 'Conecta estratégia a execução.' ) ) ),
			'victor-favaro'     => array( 'title' => 'Victor Fávaro', 'photo' => array( 'file' => 'people/victor-favaro.jpg', 'alt' => 'Retrato de Victor Fávaro', 'focal' => '50% 30%' ), 'order' => 2, 'meta' => array( 'details' => array( 'role' => 'Diretor de Arte', 'connects' => 'Conecta conceitos a soluções criativas.' ) ) ),
		),

		// Credenciais da agência (logos enviados pela Full em 26/10/2026, em app/seed/credentials/).
		'credentials' => array(
			'google-ads' => array( 'title' => 'Google Ads', 'logo' => 'credentials/google-ads.png', 'order' => 1 ),
			'meta'       => array( 'title' => 'Meta', 'logo' => 'credentials/meta.png', 'order' => 2 ),
			'rd-station' => array( 'title' => 'RD Station', 'logo' => 'credentials/rd-station.png', 'order' => 3 ),
			'spotify'    => array( 'title' => 'Spotify', 'logo' => 'credentials/spotify.png', 'order' => 4 ),
			'tiktok-ads' => array( 'title' => 'TikTok Ads', 'logo' => 'credentials/tiktok-ads.png', 'order' => 5 ),
		),

		'categories' => array(
			'estrategia'    => 'Estratégia',
			'branding'      => 'Branding',
			'tecnologia'    => 'Tecnologia',
			'comportamento' => 'Comportamento',
			'negocios'      => 'Negócios',
		),

		// ------------------------------------------------------------------
		// Menus (Configurações do site › Menus). Itens apontando para páginas
		// em rascunho não aparecem no site até a publicação.
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
							array( 'label' => 'FullMix', 'to' => 'platform:mix' ),
							array( 'label' => 'FullDash', 'to' => 'platform:dash' ),
							array( 'label' => 'FullLeads', 'to' => 'platform:leads' ),
							array( 'label' => 'FullMatch', 'to' => 'platform:match' ),
							array( 'label' => 'FullHub', 'to' => 'platform:hub' ),
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
							array( 'label' => 'FullMix', 'to' => 'platform:mix' ),
							array( 'label' => 'FullDash', 'to' => 'platform:dash' ),
							array( 'label' => 'FullLeads', 'to' => 'platform:leads' ),
							array( 'label' => 'FullMatch', 'to' => 'platform:match' ),
							array( 'label' => 'FullHub', 'to' => 'platform:hub' ),
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
