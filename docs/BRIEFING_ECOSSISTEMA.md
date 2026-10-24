# FULL CONNECT KEY — ADENDO AO BRIEFING v1.1
## SPRINT ECO — Refinamento de conteúdo e estrutura do Ecossistema Full

Versão 1.0 | 23/10/2026 | Complementa `docs/BRIEFING.md` (v1.1)

> Cópia do adendo enviado pelo cliente em 23/10/2026. Os códigos de cor, que vieram quebrados em linhas separadas na mensagem, foram reunidos na mesma linha; o texto não foi alterado.

**INSTRUÇÃO PARA CLAUDE CODE:** Leia este adendo inteiro. Salve-o em `docs/BRIEFING_ECOSSISTEMA.md` e adicione a referência em `CLAUDE.md`. Este documento substitui o conteúdo do briefing v1.1 sobre as plataformas e o Ecossistema (seções 6.1 "Ecossistema", 6.3 a 6.8 e 6.14 nos itens de plataformas), além da estrutura das páginas de produto (Sprint 07). Todas as demais regras do v1.1 continuam valendo: CMS-first, nada hardcoded, não inventar dados, Definition of Done (2.5), pontos de parada (2.4) e nenhuma publicação em produção sem autorização.

**Quando executar:**
- Se as Sprints 06 e 07 ainda não foram executadas: registre a Sprint ECO no SPRINT_STATUS.md logo antes da Sprint 06 e execute-a nessa posição. Depois, faça as Sprints 06 e 07 já com o conteúdo e a estrutura deste adendo.
- Se as Sprints 06 e/ou 07 já foram executadas: execute a Sprint ECO agora, como refinamento. Migre o conteúdo e a estrutura sem perder edições feitas no painel; ver "Migração" na seção E5.
- Nos dois casos, continue depois automaticamente para a próxima sprint pendente do plano.

## E0. Decisões já tomadas por este adendo

Função de cada plataforma (substitui os verbos do v1.1):

| Ordem | Plataforma | Verbo | Papel |
|---|---|---|---|
| 1 | FullMix | Planejar | orienta as decisões estratégicas de investimento |
| 2 | FullDash | Otimizar | acompanha e otimiza a execução dos investimentos em mídia |
| 3 | FullLeads | Converter | transforma campanhas e conteúdos em oportunidades comerciais qualificadas |
| 4 | FullMatch | Fidelizar | desenvolve o relacionamento, estimula recorrência e novas compras |
| 5 | FullHub | Organizar | sustenta a operação, conectando pessoas, demandas e entregas em todo o processo |

- A ordem segue a jornada de crescimento: planejar → otimizar → converter → fidelizar, com o FullHub como base operacional. A ordem é editável no CMS (platforms.order).
- O negócio do cliente continua no centro do Ecossistema. O novo título "Cinco soluções. Infinitas possibilidades de conexão." convive com o princípio do v1.1 "Seu negócio no centro". Ver E3.2.
- S&M continua sendo o método, não uma plataforma. A Full se apresenta como agência de Sales Marketing com infraestrutura tecnológica própria.
- Plataformas independentes: a comunicação nunca pode dar a entender que todas precisam ser contratadas juntas.
- Página de produto passa de 3 para 7 blocos (E2). Blocos sem conteúdo real são omitidos.
- Paleta das plataformas confirmada, igual ao v1.1: FullHub #0267C1, FullDash #F43156, FullLeads #09E85E, FullMix #FFDD21, FullMatch #FF7E70. Marca: #68CCF2, grafite #0E1113, preto profundo #05080A, branco #FFFFFF, papel #F5F6F4.

### E0.1 Decisões pendentes (não bloqueiam: usar o padrão e registrar em CONTENT_GAPS.md)

| # | Decisão | Padrão adotado até confirmação |
|---|---|---|
| D1 | Grafia dos nomes. O v1.1 usa "Full Hub / Full Dash / Full Leads"; o texto institucional novo usa "FullHub / FullDash / FullLeads". | Usar o texto novo: FullHub, FullDash, FullLeads, FullMix, FullMatch. O nome fica num único campo platforms.name, reutilizado em todo o site, incluindo SEO, CTAs e textos via token {platform}, para que a troca seja uma edição só. Confirmar pelos arquivos oficiais de logo. |
| D2 | Assinatura institucional. "Vire a chave dos seus resultados. Ative o modo Full." (texto novo) × "… Ative o modo Full no seu negócio." (v1.1, hero da Home) | Usar a versão nova no global siteSettings.signature. O hero da Home lê esse campo. |
| D3 | Funcionalidades a validar (ver E4): cada afirmação marcada VALIDAR precisa de confirmação do time de produto. | Aparece em desenvolvimento e staging com selo interno, nunca em produção sem validação (E4). |
| D4 | CTA de demonstração. As diretrizes citam "contato ou demonstração comercial". | Usar "Conversar sobre o {platform}", que leva a /contato?assunto=projeto&plataforma={slug}. Um CTA "Solicitar demonstração" só entra se a Full confirmar que oferece demonstração. |

## E1. Diretrizes de tom (aplicar em toda copy de plataforma)

- Clareza: explicar a tecnologia de forma acessível, sem diminuir o que ela faz.
- Personalidade: frases inteligentes, diretas e provocativas, sem clichê corporativo.
- Benefício antes da funcionalidade: primeiro o problema resolvido, depois a tecnologia.
- Autoridade sem promessa: mostrar capacidade estratégica e operacional, sem garantir resultados.
- Conexão: reforçar a relação entre as plataformas e o conceito "O que se conecta, se multiplica."

Hierarquia de densidade:
- Home: nome + verbo + uma linha.
- Página Ecossistema: card resumido (título + 2 frases).
- Página da plataforma: profundidade completa, em blocos curtos. Nenhum parágrafo com mais de cerca de 60 palavras, e nada de blocos longos de texto.

Cuidados obrigatórios com as promessas (vale para seed, microcopy, SEO e alt texts):
- FullMix: impacto incremental é estimativa estatística, nunca medição causal exata.
- FullDash: a IA auxilia o controle do investimento. Não garantir ausência de sobras ou excessos.
- FullLeads: não prometer que contatos qualificados vão virar vendas.
- FullMatch: respeitar a LGPD e as preferências de comunicação dos consumidores. Personalização depende de base legal e consentimento quando aplicável.
- FullHub: só funcionalidades reais ou previstas, distinguindo demonstração conceitual de recurso disponível.
- Proibido usar números, percentuais, prazos, nomes de clientes ou depoimentos que não estejam no CMS validados.

## E2. Nova estrutura da página de plataforma (substitui "3 seções cada" do v1.1)

Template único PlatformPage com 7 blocos tipados, em ordem fixa. Cada bloco pode ser ocultado no CMS, e blocos sem conteúdo são omitidos automaticamente:

| # | Bloco | Conteúdo | Regra |
|---|---|---|---|
| 1 | Hero | eyebrow (nome + verbo), título principal, descrição objetiva (1–2 frases), CTA primário + secundário "Ver como funciona" (âncora) | cor da plataforma; background interativo leve |
| 2 | Problema | título provocativo + texto curto do problema | aproveita os heros provocativos do v1.1 (E3) |
| 3 | Como funciona | texto institucional em 2–3 passos curtos + interação exclusiva da plataforma (a definida na Sprint 07 do v1.1) | visual conceitual sempre identificado como ilustrativo |
| 4 | Benefícios e diferenciais | 6 itens (título + 1 frase), com frase-manifesto em destaque | itens com status validar seguem a regra de E4 |
| 5 | Interface | capturas ou vídeos reais da plataforma, com legenda | omitido até existir material real (gap P1). Nunca usar mockup inventado como se fosse a interface real |
| 6 | Conexões no ecossistema | como esta plataforma se complementa com as outras (2 a 3 relações) + links | microcopy deixa claro que pode ser contratada sozinha |
| 7 | CTA final | chamada de encerramento + apoio + CTA de contato | leva ao contato com assunto e plataforma pré-preenchidos |

Campos novos em platforms: verb, cardTitle, cardText, homeLine, problemTitle, problemText, howItWorks[] (título, texto, ordem), manifesto, benefits[] (título, texto, claimStatus, claimNote), interfaceMedia[] (mídia, legenda, isRealInterface obrigatório = true para publicar), connections[] (plataforma relacionada, texto), closingTitle, closingText, ctaLabel, cardCtaLabel, altTitles[] (somente painel, para testes de copy, não publicado), seo.

### E2.1 Motion guiado por scroll e hover — FullDash, FullMix e FullMatch

**Problema a corrigir:** nessas três páginas as animações dependem de clique ou seleção. A pessoa precisa "descobrir" a interação para ver algo acontecer, e quem só rola a página vê telas estáticas. **Nova regra:** a narrativa visual toma forma sozinha conforme a pessoa rola a página, e o hover acrescenta uma camada de reação sobre o que já está animado. Clique, toque e teclado continuam disponíveis como controle opcional, nunca como requisito. Esta seção substitui as linhas "Interação:" de E3.3 (FullMix), E3.4 (FullDash) e E3.6 (FullMatch) e complementa a Sprint 07 do v1.1.

**Princípios (valem para as três páginas)**
1. Scroll como linha do tempo: cada bloco tem uma timeline GSAP ScrollTrigger em scrub, ligada à posição do scroll. Rolar para baixo constrói o visual e rolar para cima o desfaz. Nada depende de clique para aparecer.
2. Seções fixadas (pin) só no bloco 3 "Como funciona", com duração de cerca de 2 a 3 alturas de tela, indicador de progresso visível e possibilidade de pular a seção (link "Pular animação" acessível por teclado). Os outros blocos animam ao entrar na viewport, sem prender o scroll.
3. Seletores avançam sozinhos com o scroll: os seletores da Sprint 07 (Gestão/Otimização/Resultados; perfis Recorrente/Alto valor/Inativo; passos do FullMix) passam a refletir o progresso do scroll. Clicar num item salta a timeline até aquele ponto. Quando a pessoa interage manualmente, a automação daquele componente pausa até ela sair e voltar à seção.
4. Hover como segunda camada: o cursor provoca reações locais (atração magnética, destaque, rastro, tooltip conceitual, parallax sutil). O hover enriquece, mas nenhuma informação essencial depende dele.
5. Ambiente vivo: cada hero tem uma animação ambiente leve e contínua (partículas, linhas, pulsos) mesmo sem interação, que reage ao cursor no desktop.
6. Toque (mobile e tablet): o scroll conduz tudo. Hover vira toque (tap revela a mesma informação). O pin pode ser substituído por sequência vertical com animações de entrada, se o pin prejudicar a rolagem em telas pequenas. Testar em 375px.
7. Movimento reduzido (prefers-reduced-motion): sem scrub, pin ou ambiente. Mostrar o estado final de cada visual, estático e completo, com todos os textos. Seletores voltam a ser controle manual.
8. Nada de dado falso: gráficos, curvas e proporções são ilustrativos, sem números, eixos com valores ou percentuais que pareçam dados reais. Rótulos conceituais apenas ("planejado", "realizado", "contribuição estimada"). O aviso do FullMix continua obrigatório.
9. Performance: animar só transform, opacity, stroke-dashoffset e canvas. Pausar timelines e canvas fora da viewport e em aba oculta. Carregar o código de animação da página sob demanda. Meta de cerca de 60 fps em notebook médio; CLS ~0 (reservar espaço do visual antes de animar).
10. Uma biblioteca por propriedade: GSAP para scroll e timelines; Motion apenas para microinterações de hover que não disputem as mesmas propriedades.
11. Parâmetros seguros no CMS: por bloco, o editor pode ligar/desligar a animação, escolher intensidade (suave / padrão) e trocar textos e rótulos. A coreografia fica no código.

**FullDash — Otimizar** (linguagem visual: linhas de ritmo, pulsos de dados, ajuste de rota)
- Hero: linhas de "ritmo de investimento" desenham-se ao carregar e seguem pulsando como sinais em movimento. Pontos de dados reagem ao cursor (aproximam-se e acendem), formando uma trilha atrás dele.
- Problema ("Sua mídia gera números. Mas gera negócios?"): as palavras impressões, cliques e alcance flutuam soltas. Ao rolar, elas se alinham e convergem para uma única linha rotulada "objetivo do negócio". Hover sobre cada palavra a faz vibrar e mostra uma frase curta do CMS.
- Como funciona (pin, 3 etapas ligadas ao scroll):
  1. Planejar e aprovar: uma linha tracejada "planejado" desenha-se de ponta a ponta; blocos de orçamento, plano e POs encaixam-se abaixo dela.
  2. Acompanhar o ritmo: uma linha sólida "realizado" avança acompanhando o scroll, se afasta da planejada (área de desvio aparece em cor de produto) e um pulso marca o ponto de atenção.
  3. Otimizar: setas de realocação movem blocos entre campanhas e a linha "realizado" volta a se alinhar ao plano.
  - O seletor Gestão de mídia / Otimização contínua / Resultados de negócio acompanha essas três etapas automaticamente.
  - Hover no gráfico: crosshair segue o cursor com tooltip conceitual ("planejado", "realizado", "ajuste"), sem valores.
- Benefícios: cards entram em sequência; cada um tem um micro-visual que anima ao entrar (barra que cresce, pulso, linha que se alinha). Hover inclina levemente o card e reativa o micro-visual.
- Conexões: linhas desenham-se no scroll ligando o nó FullDash ao FullMix e ao FullLeads; hover num nó destaca a relação e o texto correspondente.
- CTA final: a linha "realizado" atravessa a seção e termina no botão, que pulsa uma vez ao entrar na tela.

**FullMix — Planejar** (linguagem visual: blocos de canais, decomposição, curvas)
- Hero: blocos geométricos de canais genéricos (ex.: TV, rádio, busca, social, OOH, e-mail; rótulos editáveis no CMS) flutuam em camadas. O cursor exerce atração magnética e os blocos próximos se iluminam na cor do FullMix.
- Problema ("Você sabe onde investe. Mas sabe o que realmente vende?"): os blocos estão espalhados. Ao rolar, empilham-se numa barra única "vendas", que então se divide em uma base ("o que aconteceria sem campanhas") e segmentos de "contribuição estimada" por canal. Proporções ilustrativas, sem percentuais. Hover num segmento destaca o canal e mostra o rótulo conceitual.
- Como funciona (pin, 3 etapas ligadas ao scroll):
  1. Conectar dados: fluxos entram pelas laterais (online, offline, sazonalidade, preço, promoções, mercado) e convergem para o centro.
  2. Estimar a contribuição: os fluxos se transformam em curvas de resposta que se desenham progressivamente, com uma faixa de incerteza translúcida (reforça a ideia de estimativa).
  3. Simular e planejar: o simulador entra em demonstração automática: os sliders se movem sozinhos conforme o scroll, redistribuindo o orçamento entre canais, e as barras respondem. Quando a pessoa pega um slider (mouse, toque ou teclado), a demonstração para e ela assume o controle. O aviso ilustrativo fica sempre visível junto ao simulador, inclusive durante a demonstração.
  - Os passos do seletor acompanham o scroll; clicar salta para a etapa.
- Benefícios: entram em sequência; cada um com micro-visual (curva que se desenha, blocos que se encaixam). Hover reativa.
- Conexões: linha do FullMix ao FullDash desenha-se no scroll ("planejamento → execução"). Hover destaca.
- CTA final: os blocos de canais reaparecem e se encaixam formando uma base sob o título.

**FullMatch — Fidelizar** (linguagem visual: constelação de consumidores, órbitas, ofertas)
- Hero: constelação de pontos (consumidores) em movimento lento. O cursor acende os pontos próximos e desenha conexões finas entre eles e pequenos cartões de oferta, como se encontrasse "matches".
- Problema ("A primeira compra não precisa ser a última."): ao rolar, um mesmo cartão de oferta se replica igual para todos os pontos (genérico); em seguida os cartões mudam de forma e cor, cada um se adaptando ao seu ponto (personalizado). Alguns pontos se apagam, representando quem parou de comprar.
- Como funciona (pin, 3 etapas ligadas ao scroll):
  1. Entender: os pontos deixam rastros curtos (histórico, frequência, canal) e começam a se agrupar.
  2. Personalizar: formam três agrupamentos, Recorrente, Alto valor e Inativo. O seletor de perfis acompanha o scroll e destaca o grupo da vez com seu texto do CMS.
  3. Ativar: um ciclo de recompra desenha-se como órbita em torno do grupo Recorrente; ofertas complementares orbitam o Alto valor; os pontos Inativos voltam a acender um a um (reativação).
  - Hover sobre um agrupamento mostra a descrição do perfil e intensifica sua órbita; clicar fixa o perfil.
- Benefícios: entram em sequência com micro-visuais (órbita, ponto que reacende, cartão que se adapta). Hover reativa.
- Conexões: linha do FullLeads ao FullMatch desenha-se no scroll ("oportunidade → relacionamento").
- CTA final: os três grupos se fundem numa única órbita que contorna o título.

**Consistência com FullHub e FullLeads**
Aplicar os mesmos princípios 1 a 11 às páginas FullHub e FullLeads: as interações já previstas (caos que se organiza em fluxo; rede de pontos que converge em oportunidades) também devem acontecer pelo scroll, com hover como camada extra. Assim as cinco páginas ficam no mesmo nível de movimento.

## E3. CONTEÚDO REFINADO — SEED EDITÁVEL DO CMS

Base: texto institucional entregue pela Full em 23/10/2026. Refinamentos mínimos, só para seguir E1. Cada alteração está na tabela E3.8. Itens marcados [VALIDAR: …] são funcionalidades que dependem de confirmação. A marcação não aparece no site; ela vira claimStatus = validar + claimNote.

### E3.1 Home — seção Ecossistema (substitui os resumos do v1.1 em 6.1)
- Título: "Quem se conecta, evolui." (mantido do v1.1)
- Apoio: "Cinco plataformas proprietárias, cada uma resolvendo um desafio específico. Juntas, conectam diferentes etapas do crescimento do seu negócio."
- Itens (nome — verbo — linha):
  - FullMix — Planejar. Inteligência estratégica para descobrir onde e como investir.
  - FullDash — Otimizar. Gestão e performance para fazer cada investimento em mídia render melhor.
  - FullLeads — Converter. Atração, nutrição e qualificação de oportunidades para o time comercial.
  - FullMatch — Fidelizar. Inteligência de relacionamento para personalizar ofertas, estimular novas compras e reativar clientes.
  - FullHub — Organizar. Gestão integrada para conectar agência, clientes, equipes e entregas em um só lugar.
- CTA: "Conheça o ecossistema Full"

### E3.2 Página Ecossistema (substitui 6.3 do v1.1)

**Hero**
- H1: "Cinco soluções. Infinitas possibilidades de conexão."
- Subtítulo (rótulo do diagrama com o cliente no centro): "Seu negócio no centro. Todas as possibilidades ao redor."
- Texto: "Resultados consistentes não acontecem de maneira isolada. Eles surgem quando estratégia, tecnologia, pessoas e processos trabalham em conjunto. Por isso, a Full Connect Key desenvolveu um ecossistema de plataformas que conecta diferentes etapas do crescimento de uma marca."

**Jornada** (texto de transição antes da navegação interativa)
- "Da inteligência que orienta investimentos à gestão que organiza operações. Da mídia que evolui diariamente à geração de oportunidades comerciais. Do primeiro contato à construção de relacionamentos duradouros."
- Destaque: "Cada plataforma resolve um desafio específico. Juntas, transformam informação em decisão, decisão em ação e ação em resultados."

**Navegação interativa entre as cinco soluções** (mecânica da Sprint 06 do v1.1; cada item mostra verbo, card e "Como se conecta")

| Plataforma | Card (título) | Card (texto) | Como se conecta |
|---|---|---|---|
| FullMix | Inteligência que direciona resultados. | Conecte dados, entenda o impacto dos seus investimentos e descubra onde a próxima decisão pode gerar mais resultado. Com Marketing Mix Modeling e simulação de cenários, o FullMix transforma informação em estratégia. | Orienta as decisões estratégicas de investimento. |
| FullDash | Inteligência que acompanha. Performance que evolui. | Conecte planejamento, investimento e resultados em uma gestão de mídia mais inteligente. Com acompanhamento diário e otimização contínua, o FullDash ajuda a direcionar cada investimento para os resultados que importam. | Acompanha e otimiza a execução dos investimentos em mídia. |
| FullLeads | Oportunidades que se conectam. Negócios que se multiplicam. | Da atração à conversão, conectamos marketing e vendas para transformar contatos em oportunidades qualificadas, acompanhando cada etapa do funil e aproximando sua equipe comercial das oportunidades com mais potencial. | Transforma campanhas e conteúdos em oportunidades comerciais qualificadas. |
| FullMatch | Conexões personalizadas. Resultados multiplicados. | Transforme dados de comportamento em ofertas relevantes para cada consumidor. O FullMatch ajuda a identificar momentos e canais mais adequados para estimular novas compras, aumentar a recorrência e reativar clientes. | Desenvolve o relacionamento com consumidores, estimulando recorrência e novas oportunidades de compra. |
| FullHub | Sua operação inteira, em um só lugar. | Centralize solicitações, acompanhe entregas e aprove materiais em uma plataforma que conecta você à nossa operação, com mais transparência, agilidade e organização. | Sustenta a organização operacional, conectando pessoas, demandas e entregas ao longo de todo o processo. |

CTA de cada card: "Explore o {platform}".

Nota de independência (microcopy abaixo da navegação):
- "Cada solução pode ser contratada de forma independente. A maior força aparece quando elas trabalham conectadas."

Método (linha discreta, mantém S&M como método transversal):
- "Com o método S&M orientando nossa atuação, cada plataforma se conecta ao que o seu negócio precisa."

**CTA institucional**
- Título: "O que se conecta, se multiplica."
- Apoio: "Inteligência para planejar. Tecnologia para executar. Estratégia para crescer."
- Botão: "Vamos conversar"

### E3.3 FullMix — Planejar
Cor: #FFDD21 (texto em claro #7A6400) · Rota: /ecossistema/fullmix

- **Hero**
  - Eyebrow: "FullMix · Planejar"
  - Título: "Inteligência para investir melhor. Estratégia para multiplicar resultados."
  - Descrição: "Investir em marketing é importante. Saber onde investir faz toda a diferença."
  - CTA: "Conversar sobre o FullMix"
- **Problema**
  - Título: "Você sabe onde investe. Mas sabe o que realmente vende?"
  - Texto: "Cada canal desempenha um papel nos resultados. Sem entender essa contribuição, a distribuição do orçamento depende mais de hábito do que de evidência."
- **Como funciona**
  - "O FullMix é a plataforma de inteligência estratégica da Full Connect Key que estima o impacto dos investimentos em marketing e mostra quanto cada canal contribui para os resultados do seu negócio."
  - Passo 1 · Conectar dados: "Por meio do Marketing Mix Modeling (MMM), reúne dados de campanhas online e offline, vendas e fatores externos, como sazonalidade, preços, promoções e condições de mercado."
  - Passo 2 · Estimar a contribuição: "Analisa a performance com dados agregados, sem depender de cookies ou do rastreamento individual de consumidores."
  - Passo 3 · Simular e planejar: "Mais do que analisar o passado, permite simular cenários e planejar os próximos investimentos com mais embasamento."
  - Interação: simulador ilustrativo (v1.1, Sprint 07) com o aviso obrigatório sempre visível: "Esta experiência é ilustrativa. As proporções exibidas não representam previsões de vendas ou recomendações reais de investimento."
- **Benefícios** — manifesto: "Menos achismo. Mais inteligência. Melhores decisões."
  - Contribuição incremental: estima quanto cada canal contribui para as vendas, separando o efeito associado ao marketing do que aconteceria independentemente das campanhas.
  - Visão integrada: analisa investimentos online e offline em um mesmo modelo.
  - Inteligência sem cookies: trabalha com dados agregados, sem depender do rastreamento individual.
  - Planejamento estratégico: simula cenários para orientar a distribuição do orçamento.
  - Otimização de investimentos: aponta oportunidades para direcionar recursos aos canais com maior potencial de retorno.
  - Análise contextual: considera fatores externos que podem influenciar o desempenho comercial.
  - Nota de rodapé do bloco: "Os resultados do Marketing Mix Modeling são estimativas estatísticas, cuja precisão depende da qualidade e do histórico dos dados disponíveis."
- **Interface:** omitir até existir material real.
- **Conexões**
  - FullDash: "O que o FullMix orienta no planejamento, o FullDash acompanha e otimiza na execução da mídia."
  - FullLeads: "Os dados de oportunidades geradas ajudam a enriquecer a leitura da contribuição de cada canal." [VALIDAR: integração de dados entre FullLeads e FullMix]
- **CTA final**
  - Título: "Cada investimento tem um potencial. Descubra onde ele se multiplica."
  - Apoio: "Transforme dados em estratégia e estratégia em resultados."
  - Botão: "Conversar sobre o FullMix"

Títulos alternativos (somente painel): O próximo resultado começa na decisão certa. · Seu investimento merece mais que um palpite. · Dados que se conectam. Investimentos que se multiplicam. · O fim do achismo. O começo da inteligência.

SEO: "FullMix | Marketing Mix Modeling e Planejamento de Investimentos" — "Estime a contribuição dos canais online e offline para as vendas, simule cenários e planeje investimentos em marketing com mais inteligência."

### E3.4 FullDash — Otimizar
Cor: #F43156 (texto em escuro #FF6B85, em claro #C8183D) · Rota: /ecossistema/fulldash

- **Hero**
  - Eyebrow: "FullDash · Otimizar"
  - Título: "Sua mídia em movimento. Seus resultados em evolução."
  - Descrição: "Investir em mídia é importante. Garantir que cada investimento trabalhe a favor dos seus resultados é essencial."
  - CTA: "Conversar sobre o FullDash"
- **Problema**
  - Título: "Sua mídia gera números. Mas gera negócios?"
  - Texto: "Impressões, cliques e alcance ajudam a acompanhar campanhas. O que importa é entender como o investimento contribui para os objetivos do negócio, e corrigir o rumo antes do fim do mês."
- **Como funciona**
  - "O FullDash é a plataforma de inteligência e gestão de mídia da Full Connect Key que conecta planejamento, controle financeiro e performance em um único ambiente."
  - Passo 1 · Planejar e aprovar: "Orçamento, plano de mídia e pedidos de inserção (POs) organizados no mesmo lugar." [VALIDAR: gestão de POs na plataforma]
  - Passo 2 · Acompanhar o ritmo: "Acompanhamento diário do ritmo de investimento (pacing), com apoio de inteligência artificial, para manter a distribuição da verba alinhada ao planejamento." [VALIDAR: uso de IA no pacing]
  - Passo 3 · Otimizar: "Análise de performance e ajustes frequentes, com realocação de recursos entre campanhas dentro dos limites do plano aprovado."
  - Interação: seletor Gestão de mídia / Otimização contínua / Resultados de negócio (v1.1, Sprint 07), com gráficos rotulados como ilustrativos.
- **Benefícios** — manifesto: "Menos desperdício. Mais controle. Performance em constante evolução."
  - Gestão integrada de investimentos: orçamento, plano de mídia e POs organizados em um único ambiente. [VALIDAR]
  - Inteligência artificial aplicada: apoio no acompanhamento diário do pacing para manter a verba alinhada ao plano. [VALIDAR]
  - Otimização contínua: análise de performance e ajustes frequentes para aproveitar melhor cada oportunidade.
  - Investimentos mais inteligentes: realocação estratégica entre campanhas, respeitando os limites do plano aprovado.
  - Transparência financeira: visibilidade dos valores aprovados, investidos e dos resultados atribuídos às campanhas.
  - Resultados conectados às metas: dashboards que transformam indicadores em informação para decisão. [VALIDAR: frequência de atualização ("tempo real" só se for confirmado)]
  - Nota do bloco: "A inteligência artificial apoia o acompanhamento e as decisões da equipe; ela não elimina variações naturais de entrega e investimento."
- **Interface:** omitir até existir material real.
- **Conexões**
  - FullMix: "O FullMix orienta onde investir; o FullDash cuida de como esse investimento é executado no dia a dia."
  - FullLeads: "Campanhas otimizadas alimentam a geração de oportunidades comerciais."
- **CTA final**
  - Título: "Seu investimento não pode esperar até o fim do mês para dar resultado."
  - Apoio: "Mais inteligência, agilidade e controle para transformar cada decisão de mídia em uma oportunidade de crescimento."
  - Botão: "Conversar sobre o FullDash"

Títulos alternativos (somente painel): Sua mídia não para. Sua inteligência também não. · Cada real conta. Cada decisão multiplica. · Performance não se acompanha uma vez por mês. · Do planejamento à performance. Tudo sob controle.

SEO: "FullDash | Gestão de Mídia, Pacing e Performance" — "Conecte planejamento, controle financeiro e performance de mídia em um só ambiente, com acompanhamento diário e otimização contínua."

### E3.5 FullLeads — Converter
Cor: #09E85E (texto em claro #087A35) · Rota: /ecossistema/fullleads

- **Hero**
  - Eyebrow: "FullLeads · Converter"
  - Título: "Mais do que gerar leads. Conectamos oportunidades a resultados."
  - Descrição: "Atrair pessoas é só o começo. Transformar interesse em negócio é o que realmente importa."
  - CTA: "Conversar sobre o FullLeads"
- **Problema**
  - Título: "Não é sobre gerar contatos. É sobre gerar negócios."
  - Texto: "Contato sem contexto vira lista parada. Quando marketing e vendas não se conectam, boas oportunidades se perdem no caminho."
- **Como funciona**
  - "O FullLeads é a plataforma de geração e gestão de oportunidades da Full Connect Key que conecta marketing e vendas em uma jornada estratégica, do primeiro contato ao encaminhamento comercial."
  - Passo 1 · Atrair: campanhas e conteúdos direcionados para alcançar o público certo.
  - Passo 2 · Capturar: landing pages e formulários que transformam interesse em contatos.
  - Passo 3 · Nutrir e qualificar: comunicação personalizada ao longo da jornada e identificação dos contatos com maior potencial. [VALIDAR: automação da nutrição e critério de qualificação (lead scoring)]
  - Passo 4 · Vender: oportunidades organizadas no funil comercial, para que a equipe de vendas concentre esforços onde há mais potencial.
  - Interação: rede de pontos convergindo em oportunidades (v1.1, Sprint 07). Não usar funil clichê.
- **Benefícios** — manifesto: "Menos contatos sem potencial. Mais oportunidades qualificadas. Mais negócios."
  - Atração estratégica: campanhas e conteúdos direcionados para alcançar o público certo.
  - Captação inteligente: landing pages e formulários que transformam interesse em contatos.
  - Nutrição contínua: comunicação personalizada para desenvolver oportunidades ao longo da jornada de compra.
  - Qualificação de leads: identificação dos contatos com maior potencial de conversão. [VALIDAR]
  - Marketing e vendas integrados: oportunidades organizadas em um funil conectado, com visibilidade de cada etapa. [VALIDAR: integração com CRM — quais]
  - Acompanhamento da jornada: do primeiro contato ao encaminhamento comercial e, quando integrado aos dados de vendas, até o fechamento.
- **Interface:** omitir até existir material real.
- **Conexões**
  - FullDash: "A mídia otimizada pelo FullDash amplia o alcance das estratégias de atração."
  - FullMatch: "Quando a oportunidade vira cliente, o FullMatch continua o relacionamento."
- **CTA final**
  - Título: "Sua próxima venda começa muito antes do primeiro contato comercial."
  - Apoio: "Conecte sua marca às pessoas certas e transforme oportunidades em crescimento."
  - Botão: "Conversar sobre o FullLeads"

Títulos alternativos (somente painel): O lead certo. Na hora certa. Para a venda acontecer. · Transforme conexões em oportunidades. Oportunidades em negócios. · Seu marketing atrai. Nossa inteligência aproxima da venda. · Atrair é só o começo. O negócio é converter.

SEO: "FullLeads | Geração e Qualificação de Oportunidades Comerciais" — "Conecte atração, captação e nutrição ao funil comercial para desenvolver oportunidades mais qualificadas para o seu time de vendas."

### E3.6 FullMatch — Fidelizar
Cor: #FF7E70 (texto em claro #B4402F) · Rota: /ecossistema/fullmatch

- **Hero**
  - Eyebrow: "FullMatch · Fidelizar"
  - Título: "Cada cliente é único. Sua estratégia de vendas também deveria ser."
  - Descrição: "Conhecer seu cliente é importante. Entender o que ele precisa, quando precisa e como prefere comprar faz toda a diferença."
  - CTA: "Conversar sobre o FullMatch"
- **Problema**
  - Título: "A primeira compra não precisa ser a última."
  - Texto: "A mesma promoção para todo mundo ignora o que cada cliente já mostrou sobre seus interesses, e deixa de lado quem parou de comprar."
- **Como funciona**
  - "O FullMatch é a plataforma de inteligência de relacionamento da Full Connect Key que transforma dados de comportamento e histórico de compras em experiências e ofertas personalizadas."
  - Passo 1 · Entender: analisa hábitos de consumo, frequência de compra, canais de preferência e períodos de inatividade.
  - Passo 2 · Personalizar: identifica oportunidades para se conectar com cada consumidor de maneira mais relevante.
  - Passo 3 · Ativar: apoia comunicações mais assertivas para estimular novas compras, fortalecer relacionamentos e aumentar o valor de cada cliente para o negócio.
  - Interação: seletor de perfis Recorrente / Alto valor / Inativo (v1.1, Sprint 07), conceitual.
- **Benefícios** — manifesto: "Menos ofertas genéricas. Mais relevância. Mais oportunidades de venda."
  - Personalização inteligente: ofertas e comunicações adaptadas ao perfil e ao comportamento de cada cliente.
  - Momento ideal: ações orientadas pelo ciclo individual de compra, e não apenas pelo calendário promocional.
  - Comunicação multicanal: contato pelos canais de preferência de cada consumidor, respeitando o consentimento e as preferências de comunicação. [VALIDAR: canais suportados]
  - Recorrência de compras: identificação de oportunidades para estimular novas compras e fortalecer a fidelização.
  - Reativação de clientes: estratégias direcionadas para reconquistar consumidores que deixaram de comprar.
  - Resultados mensuráveis: acompanhamento de indicadores como recorrência, ticket médio e reativação.
  - Nota do bloco: "Dados tratados conforme a LGPD, com uso orientado pelas bases legais aplicáveis e pelas preferências de comunicação dos consumidores." [VALIDAR com jurídico antes da publicação]
- **Interface:** omitir até existir material real.
- **Conexões**
  - FullLeads: "O FullLeads gera a oportunidade; o FullMatch cuida do relacionamento depois da primeira compra."
  - FullMix: "Entender o que traz recorrência ajuda a planejar melhor os próximos investimentos."
- **CTA final**
  - Título: "Relacionamentos mais relevantes geram negócios mais duradouros."
  - Apoio: "Transforme o conhecimento sobre seus clientes em conexões que fazem sentido e resultados que se multiplicam."
  - Botão: "Conversar sobre o FullMatch"

Títulos alternativos (somente painel): Seu cliente não é igual a todos. Sua oferta também não deveria ser. · O match perfeito entre o que seu cliente quer e o que sua marca oferece. · A próxima compra começa com a conexão certa. · Mais relevância em cada oferta. Mais valor em cada relação.

SEO: "FullMatch | Personalização, Recorrência e Reativação de Clientes" — "Transforme dados de comportamento de compra em ofertas personalizadas para estimular recorrência, aumentar o ticket médio e reativar clientes."

### E3.7 FullHub — Organizar
Cor: #0267C1 (texto em escuro #4C9BE8) · Rota: /ecossistema/fullhub

- **Hero**
  - Eyebrow: "FullHub · Organizar"
  - Título: "Tudo conectado. Tudo organizado. Tudo em um só lugar."
  - Descrição: "Uma operação eficiente começa quando todas as informações estão no lugar certo."
  - CTA: "Conversar sobre o FullHub"
- **Problema**
  - Título: "Aprovação por WhatsApp não é processo. É sorte."
  - Texto: "Solicitações espalhadas, mensagens perdidas e prazos difíceis de acompanhar não precisam fazer parte da rotina."
- **Como funciona**
  - "O FullHub é a plataforma de gestão integrada da Full Connect Key que conecta clientes, equipes e demandas em um único ambiente digital."
  - Do lado do cliente: "Você solicita novos projetos, acompanha o andamento das entregas, aprova materiais, consulta cronogramas e acessa os arquivos da sua marca." [VALIDAR: cronogramas e biblioteca de arquivos disponíveis]
  - Do lado da Full: "Nossa equipe gerencia responsáveis, prazos e processos dentro do mesmo sistema."
  - Interação: caos de mensagens que se organiza em fluxo (v1.1, Sprint 07).
- **Benefícios** — manifesto: "Menos mensagens. Menos retrabalho. Mais tempo para o que realmente importa."
  - Gestão centralizada: solicitações, entregas, aprovações e cronogramas reunidos em um único ambiente.
  - Transparência no andamento: acompanhamento de cada demanda sem depender de atualizações por mensagens. [VALIDAR: usar "em tempo real" só se confirmado]
  - Autonomia para o cliente: acesso a arquivos, materiais da marca, entregas finais e histórico de projetos. [VALIDAR]
  - Aprovações simplificadas: avaliação de materiais e solicitação de ajustes diretamente pela plataforma.
  - Operação integrada: clientes e equipes conectados, com responsabilidades e prazos organizados.
  - Histórico centralizado: informações e versões registradas para facilitar consultas e reduzir retrabalho. [VALIDAR: versionamento de materiais]
- **Interface:** omitir até existir material real.
- **Conexões**
  - "O FullHub é a base operacional do ecossistema: as demandas que nascem nas outras plataformas ganham responsáveis, prazos e aprovações em um só lugar."
- **CTA final**
  - Título: "Sua marca tem muita coisa acontecendo. A gestão não precisa ser complicada."
  - Apoio: "Mais controle, transparência e praticidade para uma operação que não para."
  - Botão: "Conversar sobre o FullHub"

Títulos alternativos (somente painel): Sua operação conectada. Sua rotina simplificada. · Chega de procurar. Comece a acompanhar. · Tudo acontece. Nada se perde. · Menos processos espalhados. Mais resultados conectados.

SEO: "FullHub | Gestão Integrada de Demandas, Entregas e Aprovações" — "Centralize solicitações, entregas, aprovações e cronogramas em uma gestão integrada entre a Full Connect Key e seus clientes."

### E3.8 Registro de refinamentos (o que mudou em relação ao texto entregue e por quê)

| Plataforma | Original | Refinado | Motivo |
|---|---|---|---|
| FullMix | "identifica quais canais realmente contribuem" | "estima… quanto cada canal contribui" | MMM é estimativa estatística (E1) |
| FullMix | Benefício "Resultados reais" | "Contribuição incremental" | "Reais" sugere medição causal exata |
| FullMix | "planejar… com mais segurança" | "com mais embasamento" | evita sugerir garantia |
| FullMix | — | nota sobre estimativas no bloco 4 | exigência das diretrizes |
| FullDash | "Com acompanhamento diário e inteligência artificial" (card) | IA mantida só na página, como apoio e marcada VALIDAR | IA auxilia, não garante; confirmar recurso |
| FullDash | "indicadores em tempo real" | "dashboards…" + VALIDAR | "tempo real" precisa de confirmação técnica |
| FullDash | — | nota sobre variações de entrega | diretriz: não garantir ausência de sobras/excessos |
| FullLeads | "quem está pronto para comprar" | "oportunidades com mais potencial" | não prometer conversão |
| FullLeads | "automatiza a nutrição" (card) | removido do card; VALIDAR na página | confirmar automação |
| FullMatch | "identifica o momento e o canal ideais" | "ajuda a identificar momentos e canais mais adequados" | evita promessa absoluta |
| FullMatch | "contato pelos canais de preferência" | + "respeitando o consentimento…" e nota LGPD | diretriz LGPD |
| FullHub | "acompanha tudo em tempo real" | "Transparência no andamento" + VALIDAR | confirmar recurso |
| FullHub | card cita "todos os arquivos da sua marca" | removido do card; VALIDAR na página | produto novo, recursos a confirmar |
| Todas | heros provocativos do v1.1 | viraram o bloco 2 "Problema" | preserva copy já aprovada e segue "benefício/problema antes da funcionalidade" |
| Ecossistema | só "Cinco soluções. Infinitas…" | + "Seu negócio no centro…" como subtítulo | mantém o princípio do v1.1 (cliente no centro) |
| Ecossistema | — | nota de independência | diretriz: não exigir contratação conjunta |

## E4. Registro de afirmações (claims) e regra de publicação

- Criar docs/CLAIMS_REGISTER.md. Uma linha por afirmação marcada [VALIDAR]: plataforma, bloco, texto exato, dúvida, responsável sugerido (produto/jurídico) e status (validar / confirmado / remover / reescrever).
- No CMS, cada benefício, passo e nota tem claimStatus (confirmado | validar) e claimNote (visível só no painel, com texto de ajuda explicando a regra).
- Regra de exibição:
  - Em desenvolvimento, preview e staging, itens validar aparecem com um selo interno discreto "A validar", visível só para usuários autenticados no preview.
  - O build de produção falha (ou o item é omitido, conforme flag CLAIMS_STRICT=true) se houver item publicado com claimStatus = validar. Isso entra no checklist de pré-lançamento da Sprint 13 como P0.
  - Quando o item é omitido, o layout se reorganiza sem buracos (5 benefícios em vez de 6, por exemplo).
- Nenhuma marcação [VALIDAR…] pode aparecer como texto no frontend. Adicionar teste automatizado para isso.
- Copy nova escrita pelo Claude Code (microcopy, alt text, aria-label) deve seguir E1 e ser registrada em docs/COPY_CHANGES.md. Claude Code não cria novas afirmações de funcionalidade, números ou integrações.

## E5. Execução da Sprint ECO

**Objetivo:** aplicar o conteúdo refinado e a nova estrutura de plataforma, mantendo tudo editável e sem promessas não validadas.

**Entregáveis:**
- docs/BRIEFING_ECOSSISTEMA.md (este adendo), docs/CLAIMS_REGISTER.md e docs/COPY_CHANGES.md. Atualizar CONTENT_MODEL.md, PROJECT_RULES.md (verbos, ordem, cuidados com promessas, independência das plataformas), DECISIONS.md (D1–D4) e CONTENT_GAPS.md.
- Modelo platforms ampliado com os campos de E2. Bloco PlatformPage com 7 blocos, e omissão automática de blocos vazios.
- Nome da plataforma num campo único, reutilizado por token {platform} em CTAs, SEO e microcopy (D1).
- Global siteSettings.signature (D2), lido pelo hero da Home e pelo rodapé onde couber.
- Seed idempotente com todo o conteúdo de E3 (Home, Ecossistema e as cinco plataformas), incluindo claimStatus, títulos alternativos (só no painel) e SEO.
- Seção Ecossistema da Home e página /ecossistema com o novo conteúdo, a nota de independência e o cliente no centro do diagrama.
- Rotas das plataformas alinhadas à grafia de D1 (/ecossistema/fullhub, /fulldash, /fullleads, /fullmix, /fullmatch), com redirects 301 a partir dos slugs do v1.1 (full-hub, full-dash, full-leads) se eles já existirem.
- CTA final de cada plataforma leva a /contato?assunto=projeto&plataforma={slug}. O formulário de contato registra a plataforma de origem (campo oculto e salvo em contactSubmissions).
- Bloco "Conexões": links entre plataformas, gerados a partir de connections[].
- Motion guiado por scroll e hover conforme E2.1 nas páginas FullDash, FullMix e FullMatch (prioridade), depois FullHub e FullLeads. Primitives reutilizáveis em src/animations/ (useScrollTimeline, usePinnedSteps, useCursorField, useAutoDemo), todas respeitando prefers-reduced-motion e pausando fora da viewport.
- Atualizar EDITOR_GUIDE.md com: como editar cada bloco da plataforma, como trocar o nome de uma plataforma, como validar um claim e como usar os títulos alternativos.

**Migração (se as Sprints 06/07 já tiverem sido executadas):**
- Migration não destrutiva: adicionar campos, preencher pelo seed só o que estiver vazio e preservar edições feitas no painel. Conteúdo antigo substituído vai para uma versão anterior (histórico), nunca é apagado.
- Os heros antigos (v1.1) migram para o bloco "Problema", conforme E3.
- Se houver conflito entre uma edição feita no painel e o seed novo, manter a edição, registrar em COPY_CHANGES.md e listar no relatório.

**Testes:**
- e2e: as cinco páginas de plataforma renderizam os 7 blocos com conteúdo, e o bloco 5 fica omitido sem buraco.
- e2e: trocar platforms.name no CMS atualiza card, página, CTA e title de SEO após publicar.
- e2e: CTA final pré-seleciona assunto e plataforma no contato.
- Teste: nenhuma ocorrência de [VALIDAR, [PENDENTE ou colchetes de placeholder no HTML renderizado.
- Teste: com CLAIMS_STRICT=true, o build falha (ou o item é omitido) se houver claim validar publicado.
- Teste: aviso ilustrativo do FullMix presente e visível junto ao simulador.
- Teste: redirects 301 dos slugs antigos.
- Seed rodado duas vezes não duplica nem sobrescreve edições.
- Motion sem clique (E2.1): teste Playwright que só rola a página (sem clicar nem passar o mouse) e verifica que cada etapa do bloco 3 de FullDash, FullMix e FullMatch é alcançada (atributo data-step muda de 1 para 3) e que os seletores acompanham.
- Motion: interação manual pausa a automação (pegar um slider do FullMix interrompe a demonstração); "Pular animação" funciona por teclado.
- Motion com prefers-reduced-motion: reduce emulado: sem pin nem scrub, estado final completo e todos os textos visíveis.
- Motion mobile (375px, toque emulado): rolagem fluida, sem travamento no pin, e o tap revela o que o hover revelaria.
- Performance das três páginas: trace do Chrome sem long tasks recorrentes durante o scroll; CLS ≈ 0; registrar resultados em QA_REPORT.md.
- axe sem violações sérias/críticas nas seis páginas (Ecossistema + 5); contraste das cores de produto em texto conforme v1.1 (1.2).
- typecheck, lint, build, check:colors.

**Aceite:**
- Todo o conteúdo do ecossistema vem do CMS e reflete este adendo.
- Ordem e verbos (Planejar, Otimizar, Converter, Fidelizar, Organizar) aplicados.
- Cliente no centro do Ecossistema; S&M continua como método, não como plataforma.
- Nenhuma promessa sem validação em produção; claims registrados.
- Nota de independência visível.
- Nas páginas FullDash, FullMix e FullMatch, quem apenas rola a página vê toda a narrativa visual se formar. O hover acrescenta reações, e nenhum conteúdo essencial depende de clique ou hover.
- Páginas de plataforma com 7 blocos, sem buracos quando faltar conteúdo.
- Documentação e SPRINT_STATUS.md atualizados, com ponto de retomada. Commit feito.

## E6. Pendências geradas por este adendo (adicionar a CONTENT_GAPS.md)
- **P0:** confirmar a grafia oficial dos nomes (D1) e a assinatura (D2); validar todos os itens do CLAIMS_REGISTER.md com o time de produto; revisar a nota de LGPD do FullMatch com o jurídico.
- **P1:** capturas ou vídeos reais da interface de cada plataforma (bloco 5); logos oficiais das cinco plataformas em SVG; confirmar se a Full oferece demonstração comercial (D4).
- **P2:** escolher entre o título principal e os títulos alternativos de cada plataforma (opcional; podem ser trocados pelo painel a qualquer momento).

**Comando de início deste adendo:** "Leia docs/BRIEFING_ECOSSISTEMA.md, registre a Sprint ECO no SPRINT_STATUS.md na posição indicada no topo do adendo, execute-a e siga automaticamente para a próxima sprint pendente do plano, observando as regras do briefing v1.1. Não peça novos prompts entre as sprints."
