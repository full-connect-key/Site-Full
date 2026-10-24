<?php
/**
 * Retrato do seed v1.1 das plataformas, da Home (Ecossistema) e da página
 * Ecossistema, antes da Sprint ECO. Usado só pela migração: um campo cujo
 * valor ainda é igual a este retrato nunca foi editado no painel e pode
 * receber o texto novo; um valor diferente é edição e é preservado.
 * Não editar.
 */

function fck_seed_legacy_eco(): array {
	return array (
  'platforms' => 
  array (
    'hub' => 
    array (
      'title' => 'Full Hub',
      'slug' => 'full-hub',
      'order' => 1,
      'meta' => 
      array (
        'identity' => 
        array (
          'product' => 'hub',
          'signature' => 'O jeito Full de atender.',
          'summary' => 'Aprovações, solicitações, responsáveis e prazos conectados em um só lugar.',
          'eco_headline' => 'Aprovação por WhatsApp não é processo. É sorte.',
          'eco_text' => 'Gestão integrada da operação, com solicitações, aprovações, responsáveis, prazos e status visíveis para a Full e seus clientes.',
        ),
        'hero' => 
        array (
          'headline' => 'Aprovação por WhatsApp não é processo. É sorte.',
          'lead' => 'Solicitações espalhadas, mensagens perdidas e prazos difíceis de acompanhar não precisam fazer parte da rotina. O Full Hub integra a gestão da operação entre a Full e seus clientes, reunindo demandas, aprovações, responsáveis e prazos em um só lugar.',
        ),
        'solution' => 
        array (
          'title' => 'Menos ruído. Mais fluxo.',
          'intro' => 'Quando todos acompanham as mesmas informações, o trabalho ganha organização, transparência e fluidez.',
          'items' => 
          array (
            0 => 
            array (
              'label' => 'Solicitações',
              'headline' => 'Toda demanda começa no lugar certo.',
              'text' => 'Solicitações organizadas em um ambiente compartilhado, facilitando o acompanhamento e a comunicação entre a equipe e o cliente.',
            ),
            1 => 
            array (
              'label' => 'Responsáveis e prazos',
              'headline' => 'Cada tarefa tem seu responsável. E seu momento de acontecer.',
              'text' => 'Demandas com responsáveis, prazos e status definidos, trazendo mais clareza para a rotina de trabalho.',
            ),
            2 => 
            array (
              'label' => 'Aprovações',
              'headline' => 'Aprovar também faz parte do processo.',
              'text' => 'Etapas de aprovação integradas ao fluxo de trabalho, reduzindo a dispersão de informações e facilitando o acompanhamento das entregas.',
            ),
            3 => 
            array (
              'label' => 'Visibilidade',
              'headline' => 'Todo mundo conectado ao andamento do projeto.',
              'text' => 'Equipe e cliente acompanham as informações relevantes da operação em um ambiente organizado.',
            ),
          ),
        ),
        'cta' => 
        array (
          'title' => 'Menos mensagens perdidas. Mais negócios em movimento.',
          'text' => 'Uma operação conectada cria espaço para o que realmente importa: fazer os projetos avançarem.',
          'cta' => 
          array (
            'label' => 'Conversar sobre o Full Hub',
            'to' => 'url:/contato/?assunto=projeto',
          ),
        ),
        'seo' => 
        array (
          'title' => 'Full Hub | Gestão Integrada de Demandas e Aprovações',
          'description' => 'Organize solicitações, responsáveis, prazos e aprovações em uma gestão integrada entre a Full e seus clientes.',
        ),
      ),
    ),
    'dash' => 
    array (
      'title' => 'Full Dash',
      'slug' => 'full-dash',
      'order' => 2,
      'meta' => 
      array (
        'identity' => 
        array (
          'product' => 'dash',
          'signature' => 'Verba vai para o que gera resultado.',
          'summary' => 'Gestão de mídia, acompanhamento de metas e otimização contínua dos investimentos.',
          'eco_headline' => 'Verba vai para o que gera resultado.',
          'eco_text' => 'Gestão e performance de mídia com otimização contínua, metas claras e acompanhamento de indicadores de negócio.',
        ),
        'hero' => 
        array (
          'headline' => 'Sua mídia gera números. Mas gera negócios?',
          'lead' => 'Impressões, cliques e alcance ajudam a acompanhar campanhas. Mas o que realmente importa é entender como o investimento contribui para os objetivos do negócio. O Full Dash conecta gestão de mídia, metas e otimização contínua para orientar decisões mais inteligentes.',
        ),
        'solution' => 
        array (
          'title' => 'Mais clareza para decidir. Mais inteligência para investir.',
          'items' => 
          array (
            0 => 
            array (
              'label' => 'Gestão de mídia',
              'headline' => 'Investimento com direção.',
              'text' => 'Acompanhamos campanhas e investimentos de mídia de forma integrada, conectando a operação aos objetivos definidos para cada negócio.',
            ),
            1 => 
            array (
              'label' => 'Otimização contínua',
              'headline' => 'Resultado não se acompanha apenas no fim do mês.',
              'text' => 'Analisamos a performance no dia a dia, identificando oportunidades de ajuste e direcionando esforços para melhorar os resultados.',
            ),
            2 => 
            array (
              'label' => 'Resultados de negócio',
              'headline' => 'Além dos indicadores de mídia.',
              'text' => 'Dashboards e relatórios conectam metas, investimentos e resultados para apoiar decisões com uma visão mais próxima do negócio.',
            ),
          ),
        ),
        'interaction' => 
        array (
          'illustrative_note' => '',
        ),
        'cta' => 
        array (
          'title' => 'Não basta investir mais. É preciso investir melhor.',
          'text' => 'Transforme o acompanhamento de mídia em decisões mais claras e investimentos mais inteligentes.',
          'cta' => 
          array (
            'label' => 'Conversar sobre o Full Dash',
            'to' => 'url:/contato/?assunto=projeto',
          ),
        ),
        'seo' => 
        array (
          'title' => 'Full Dash | Gestão e Otimização de Mídia',
          'description' => 'Gestão de mídia, acompanhamento de metas e otimização contínua para conectar investimentos a resultados de negócio.',
        ),
      ),
    ),
    'leads' => 
    array (
      'title' => 'Full Leads',
      'slug' => 'full-leads',
      'order' => 3,
      'meta' => 
      array (
        'identity' => 
        array (
          'product' => 'leads',
          'signature' => 'Oportunidade pronta para fechar.',
          'summary' => 'Atração, captura e nutrição conectadas ao funil comercial.',
          'eco_headline' => 'Oportunidade pronta para fechar.',
          'eco_text' => 'Estratégias de atração, captura e nutrição conectadas ao funil comercial para aproximar o time de vendas de oportunidades qualificadas.',
        ),
        'hero' => 
        array (
          'headline' => 'Atrair é só o começo. O negócio é converter.',
          'lead' => 'Gerar interesse é importante. Transformá-lo em oportunidade comercial é o que aproxima marketing e vendas. O Full Leads conecta atração, captura e nutrição ao funil comercial para desenvolver oportunidades mais preparadas para a atuação do time de vendas.',
        ),
        'solution' => 
        array (
          'title' => 'Cada conexão pode ser uma oportunidade.',
          'intro' => 'Da descoberta da marca à aproximação comercial, diferentes pontos de contato podem trabalhar juntos para transformar interesse em oportunidades.',
          'items' => 
          array (
            0 => 
            array (
              'label' => 'Atrair',
              'headline' => 'Tudo começa despertando o interesse certo.',
              'text' => 'Estratégias de atração aproximam o negócio de pessoas com potencial de se tornarem clientes.',
            ),
            1 => 
            array (
              'label' => 'Capturar',
              'headline' => 'Interesse precisa virar conexão.',
              'text' => 'A captura transforma interações em contatos que podem ser desenvolvidos ao longo da jornada comercial.',
            ),
            2 => 
            array (
              'label' => 'Nutrir',
              'headline' => 'Relacionamentos precisam evoluir.',
              'text' => 'A nutrição mantém o contato ativo e desenvolve oportunidades por meio de comunicações relevantes.',
            ),
            3 => 
            array (
              'label' => 'Vendas',
              'headline' => 'Oportunidades certas. No momento certo.',
              'text' => 'A integração com o funil comercial aproxima marketing e vendas, ajudando o time comercial a trabalhar oportunidades mais qualificadas.',
            ),
          ),
        ),
        'cta' => 
        array (
          'title' => 'Não basta gerar contatos. É preciso gerar oportunidades.',
          'text' => 'Conecte suas estratégias de atração ao processo comercial e aproxime sua equipe de vendas das oportunidades certas.',
          'cta' => 
          array (
            'label' => 'Conversar sobre o Full Leads',
            'to' => 'url:/contato/?assunto=projeto',
          ),
        ),
        'seo' => 
        array (
          'title' => 'Full Leads | Geração de Leads e Oportunidades Comerciais',
          'description' => 'Conecte atração, captura e nutrição ao funil comercial para desenvolver oportunidades mais qualificadas.',
        ),
      ),
    ),
    'mix' => 
    array (
      'title' => 'FullMix',
      'slug' => 'fullmix',
      'order' => 4,
      'meta' => 
      array (
        'identity' => 
        array (
          'product' => 'mix',
          'signature' => 'Saiba o que cada canal vende.',
          'summary' => 'Inteligência para entender quanto cada canal contribui para as vendas.',
          'eco_headline' => 'Saiba o que cada canal vende.',
          'eco_text' => 'Marketing Mix Modeling para analisar a contribuição dos canais online e offline para as vendas e orientar a distribuição dos investimentos.',
        ),
        'hero' => 
        array (
          'headline' => 'Você sabe onde investe. Mas sabe o que realmente vende?',
          'lead' => 'Cada canal de marketing desempenha um papel na construção dos resultados. Entender essa contribuição é essencial para tomar decisões mais inteligentes. O FullMix utiliza Marketing Mix Modeling para analisar quanto os canais online e offline contribuem para as vendas e orientar a composição do mix e a distribuição do orçamento.',
        ),
        'solution' => 
        array (
          'title' => 'Cada canal tem seu papel. Descubra qual.',
          'intro' => 'Mais do que acompanhar investimentos isolados, é preciso compreender como os diferentes canais contribuem para os resultados do negócio.',
          'items' => 
          array (
            0 => 
            array (
              'label' => 'Mensurar',
              'headline' => 'Entenda a contribuição de cada canal.',
              'text' => 'A análise estatística de dados históricos ajuda a estimar como diferentes investimentos em marketing contribuem para as vendas.',
            ),
            1 => 
            array (
              'label' => 'Comparar',
              'headline' => 'Enxergue o mix como um todo.',
              'text' => 'Compreenda a participação dos canais online e offline e identifique oportunidades de melhoria na composição dos investimentos.',
            ),
            2 => 
            array (
              'label' => 'Orientar',
              'headline' => 'Decida com mais inteligência.',
              'text' => 'Utilize os resultados das análises para apoiar decisões sobre alocação de orçamento e planejamento de marketing.',
            ),
          ),
        ),
        'interaction' => 
        array (
          'illustrative_note' => 'Esta experiência é ilustrativa. As proporções exibidas não representam previsões de vendas ou recomendações reais de investimento.',
          'simulator_intro' => 'Explore diferentes distribuições de investimento e entenda por que conhecer a participação de cada canal é importante para tomar decisões.',
          'total_label' => 'Total distribuído',
          'reset_label' => 'Restaurar distribuição',
          'channels' => 
          array (
            0 => 
            array (
              'name' => 'TV',
              'kind' => 'offline',
              'share' => 20,
            ),
            1 => 
            array (
              'name' => 'Rádio',
              'kind' => 'offline',
              'share' => 10,
            ),
            2 => 
            array (
              'name' => 'Mídia exterior',
              'kind' => 'offline',
              'share' => 10,
            ),
            3 => 
            array (
              'name' => 'Busca paga',
              'kind' => 'online',
              'share' => 25,
            ),
            4 => 
            array (
              'name' => 'Redes sociais',
              'kind' => 'online',
              'share' => 25,
            ),
            5 => 
            array (
              'name' => 'Mídia programática',
              'kind' => 'online',
              'share' => 10,
            ),
          ),
        ),
        'cta' => 
        array (
          'title' => 'Seu próximo investimento merece mais inteligência.',
          'text' => 'Entenda a contribuição dos canais e transforme informação em decisões mais estratégicas para o seu negócio.',
          'cta' => 
          array (
            'label' => 'Conversar sobre o FullMix',
            'to' => 'url:/contato/?assunto=projeto',
          ),
        ),
        'seo' => 
        array (
          'title' => 'FullMix | Marketing Mix Modeling',
          'description' => 'Entenda a contribuição dos canais online e offline para as vendas e tome decisões mais inteligentes sobre investimentos em marketing.',
        ),
      ),
    ),
    'match' => 
    array (
      'title' => 'FullMatch',
      'slug' => 'fullmatch',
      'order' => 5,
      'meta' => 
      array (
        'identity' => 
        array (
          'product' => 'match',
          'signature' => 'A oferta certa para quem já comprou.',
          'summary' => 'Ofertas hiperpersonalizadas para estimular recorrência, ticket médio e reativação.',
          'eco_headline' => 'A oferta certa para quem já comprou.',
          'eco_text' => 'Inteligência de comportamento de compra para personalizar ofertas, estimular a recorrência, aumentar o ticket médio e reativar clientes.',
        ),
        'hero' => 
        array (
          'headline' => 'A primeira compra não precisa ser a última.',
          'lead' => 'Cada compra revela interesses, preferências e oportunidades de relacionamento. O FullMatch analisa o comportamento de compra para desenvolver ofertas hiperpersonalizadas, estimular a recorrência, aumentar o ticket médio e reativar clientes que deixaram de comprar.',
        ),
        'solution' => 
        array (
          'title' => 'Cada cliente é único. A próxima oferta também deveria ser.',
          'intro' => 'Relacionamentos mais relevantes começam quando as marcas compreendem o comportamento de seus consumidores e utilizam esse conhecimento para criar novas oportunidades.',
          'items' => 
          array (
            0 => 
            array (
              'label' => 'Recorrente',
              'headline' => 'Quem já compra pode encontrar novos motivos para voltar.',
              'text' => 'O histórico de compras ajuda a identificar interesses e oportunidades para estimular novas compras.',
            ),
            1 => 
            array (
              'label' => 'Alto valor',
              'headline' => 'Mais relevância em cada nova oferta.',
              'text' => 'O comportamento de compra pode orientar recomendações complementares e oportunidades para aumentar o valor das compras.',
            ),
            2 => 
            array (
              'label' => 'Inativo',
              'headline' => 'Uma conexão pode ser retomada.',
              'text' => 'Identificar consumidores que deixaram de comprar permite desenvolver ações de reativação mais relevantes.',
            ),
          ),
          'complement' => 'Não se trata de oferecer a mesma promoção para todo mundo. Trata-se de compreender diferentes comportamentos para criar ofertas mais relevantes.',
        ),
        'interaction' => 
        array (
        ),
        'cta' => 
        array (
          'title' => 'Quem já comprou pode ter muito mais para viver com sua marca.',
          'text' => 'Transforme informações sobre comportamento de compra em oportunidades de recorrência, reativação e crescimento.',
          'cta' => 
          array (
            'label' => 'Conversar sobre o FullMatch',
            'to' => 'url:/contato/?assunto=projeto',
          ),
        ),
        'seo' => 
        array (
          'title' => 'FullMatch | Personalização e Fidelização de Clientes',
          'description' => 'Utilize o comportamento de compra para criar ofertas hiperpersonalizadas, estimular recorrência e reativar clientes.',
        ),
      ),
    ),
  ),
  'pages' => 
  array (
    'home' => 
    array (
      'hero' => 
      array (
        'headline_alt' => 'Vire a chave dos seus resultados. Ative o modo Full no seu negócio.',
      ),
      'ecosystem' => 
      array (
        'enabled' => true,
        'title' => 'Quem se conecta, evolui.',
        'text' => 'Um ecossistema de soluções que integra operação, mídia, oportunidades comerciais, inteligência de investimento e relacionamento com clientes. Tudo conectado às necessidades do seu negócio.',
        'cta' => 
        array (
          'label' => 'Explore o Ecossistema Full',
          'to' => 'page:ecosystem',
        ),
      ),
    ),
    'ecosystem' => 
    array (
      'hero' => 
      array (
        'headline' => 'Seu negócio no centro. Todas as possibilidades ao redor.',
        'headline_alt' => 'Um ecossistema. Múltiplas possibilidades.',
        'lead' => 'Cada negócio tem seus próprios desafios. O Ecossistema Full conecta soluções de gestão, performance, geração de oportunidades, inteligência de marketing e relacionamento para responder a diferentes necessidades. O ponto de partida é sempre o mesmo: o seu negócio.',
      ),
      'solutions' => 
      array (
        'title' => 'Soluções que se conectam ao que o seu negócio precisa.',
        'intro' => 'Com o método S&M orientando nossa atuação, integramos plataformas que organizam processos, otimizam investimentos, conectam oportunidades comerciais e ajudam a ampliar resultados.',
        'center_label' => 'Seu negócio',
        'link_label' => '',
      ),
      'cta' => 
      array (
        'enabled' => true,
        'title' => 'Qual conexão pode transformar o seu negócio?',
        'text' => 'As melhores soluções começam com a compreensão dos desafios certos. Vamos descobrir quais possibilidades fazem sentido para você.',
        'cta' => 
        array (
          'label' => 'Vamos conversar',
          'to' => 'page:contact',
        ),
      ),
      'seo' => 
      array (
        'title' => 'Ecossistema Full | Soluções Conectadas para Negócios',
        'description' => 'Conheça as plataformas Full Hub, Full Dash, Full Leads, FullMix e FullMatch e descubra soluções para diferentes desafios do seu negócio.',
      ),
    ),
  ),
);
}
