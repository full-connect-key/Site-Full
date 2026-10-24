# Decisões de arquitetura (ADRs)

## ADR-001 — Stack: PHP puro + MySQL + painel próprio (substitui Next.js/Payload e WordPress)
- **Contexto.** O briefing previa Next.js + Payload + PostgreSQL. A hospedagem definida pela Full é a **Hostinger compartilhada**, que roda só PHP, HTML, CSS e JavaScript, sem Node.js. Uma tentativa intermediária com WordPress (tema + plugin) falhou no **deploy por Git** da Hostinger, porque o repositório não continha o núcleo do WordPress.
- **Decisão (aprovada pelo usuário em 23/10/2026).** Site em **PHP 8.1+ sem framework**, com **MySQL** da Hostinger e **painel próprio** em `/admin`. O repositório inteiro é o `public_html`; o `.htaccess` bloqueia as pastas internas.
- **Alternativas.** WordPress (falhou no deploy por Git e depende do núcleo fora do repositório); Laravel + Filament (exige Composer/SSH e build); site estático (sem painel para a equipe); SQLite (descartado: o usuário escolheu MySQL).
- **Consequências.** Deploy por Git direto, sem build. O CMS (rascunho, preview, versões, papéis, mídia, redirects) foi implementado no projeto e é mantido aqui. A configuração do servidor (`app/config.php`) é criada uma vez, manualmente.

## ADR-002 — Modelo de conteúdo em uma tabela `entries` com JSON
- Cada registro (página, plataforma, case, artigo, matéria, pessoa, configuração global) guarda os campos estruturados em JSON: `draft` (cópia de trabalho) e `published` (o que o site exibe). Os campos são definidos em `app/schema/` e validados no servidor.
- **Rascunho invisível ao público**: o site lê só `published`. **Preview autenticado**: `?preview=1` com sessão do painel lê `draft`.
- **Histórico**: tabela `revisions`, com as 50 últimas por registro. Restaurar gera um rascunho; publicar continua sendo um passo explícito.
- Vantagem: um esquema de banco simples e estável, sem migrations a cada campo novo.

## ADR-003 — Endereços editáveis e redirects 301 automáticos
- As páginas têm slug editável. Plataformas, cases e artigos ficam sob o slug da página com o modelo correspondente (Ecossistema, Cases, Insights).
- Ao publicar uma mudança de slug, um redirect **301** do endereço antigo é criado. Se mudar a página-base, entra um redirect de **prefixo**. Redirects manuais, como os do site atual, ficam em Painel › Redirecionamentos.

## ADR-004 — Tipografia: Google Sans (OFL)
- Definida pela Full. A **Google Sans** (Google Fonts, licença OFL 1.1) fica hospedada em `assets/fonts/`, sem requisições ao Google, o que é melhor para LGPD e desempenho. Substituiu as fontes provisórias.

## ADR-005 — Animações em JavaScript puro (sem GSAP/Motion)
- **Contexto.** O briefing citava GSAP + ScrollTrigger e Motion (ecossistema React/Node).
- **Decisão.** Módulos JS nativos (`assets/js/modules/`), carregados sob demanda por `data-module`: IntersectionObserver para entradas, `position: sticky` + progresso de rolagem para a narrativa do S&M, canvas 2D para o fundo de conexões. Todos respeitam `prefers-reduced-motion`.
- **Consequências.** Nada de dependências nem build, e menos JS. Se uma narrativa futura exigir, o GSAP (licença gratuita) pode ser vendorizado em `assets/js/vendor/`.

## ADR-006 — Segurança
- PDO com consultas parametrizadas; HTML de texto rico com lista de permissões (`html_sanitize`); CSRF em todos os POSTs do painel; sessão `HttpOnly`/`SameSite=Lax`/`Secure`.
- Login com bloqueio após 5 erros e limite por IP; senhas com `password_hash` e mínimo de 12 caracteres.
- Upload validado por conteúdo (`finfo`): SVG só para administradores, recusado se tiver scripts ou eventos. `uploads/` não executa PHP.
- CSP com nonce no site, HSTS em produção, `noindex` em staging e no painel.

## ADR-007 — Cookies
- Nenhuma ferramenta de análise ou marketing foi aprovada, então o site **não grava cookies opcionais**. A escolha de consentimento fica em `localStorage`.
- Banner com modo **automático** (aparece só quando houver ferramentas opcionais registradas). **Decisão a validar com o jurídico**: exibir ou não o banner quando só há o essencial.

## ADR-008 — Retenção de mensagens do formulário
- Padrão sugerido: **12 meses**, configurável em Painel › Formulário. A limpeza roda no login de administradores. **A validar com o jurídico.**

## ADR-009 — Canais do simulador FullMix
- O briefing não define os canais. O seed traz nomes genéricos (TV, Rádio, Mídia exterior, Busca paga, Redes sociais, Mídia programática), usados só na experiência **ilustrativa**, que tem aviso obrigatório. Editáveis no painel; **revisar com a Full**.

## ADR-010 — Botões em vidro (glassmorphism)
- Todos os botões (primário, secundário, terciário, ícone, filtros, abas de seleção, compartilhar, menu) são cápsulas (`border-radius: 9999px`) de vidro translúcido: `backdrop-filter: blur(12px)`, borda fina semitransparente, reflexo no topo e sombra discreta.
- A cor vem de `--btn-color`: ciano da marca por padrão e cor oficial do produto dentro de `[data-product]`. Ela aparece só no contorno e num leve tom do vidro.
- **Revisão (24/10/2026, pedido do cliente):** sem preenchimento colorido nem brilho. Todos os botões são vidro transparente. O primário se diferencia por um contorno na cor da área e um leve tom (12%), com o texto na cor do tema; o reflexo interno ficou mais discreto.
- Sem suporte a desfoque, o vidro fica mais denso. Com movimento reduzido, não há compressão ao clicar nem deslocamento da seta.
- Só tokens existentes: nenhuma cor nova.

## ADR-011 — Sprint ECO: decisões D1–D4 e implementação do motion
- **D1, grafia:** FullHub, FullDash, FullLeads, FullMix, FullMatch. O nome é o título do registro, e `{platform}` é trocado em todos os textos da plataforma (`platform_tokens()` em `app/lib/content.php`). Novos slugs `/ecossistema/fullhub|fulldash|fullleads`. Os antigos redirecionam com 301, criado automaticamente na publicação. *A confirmar pelos logos oficiais.*
- **D2, assinatura:** "Vire a chave dos seus resultados. Ative o modo Full." em Marca › Assinatura. O hero da Home usa esse texto quando o título alternativo da Home está vazio.
- **D3, claims:** *A validar* aparece fora de produção (com selo no preview) e é omitido em produção ou com `FCK_CLAIMS_STRICT=1`. Não há etapa de build: a trava de lançamento é `php scripts/fck.php claims`, que sai com erro, mais a lista de pendências no painel.
- **D4, CTA:** "Conversar sobre o {platform}" → `/contato?assunto=projeto&plataforma={slug}`. O formulário guarda a plataforma de origem. Não há CTA de demonstração.
- **Motion sem GSAP nem Motion:** o adendo cita GSAP ScrollTrigger e primitives `src/animations/use*`. Como o site é PHP + JS puro, sem build e na hospedagem compartilhada (ADR-001 e ADR-005), as primitives são módulos em `assets/js/modules/`:
  - `scene.js`: linha do tempo e pin por `position: sticky`, passos, seletor sincronizado e `data-at`;
  - `progress.js`: progresso de entrada das seções;
  - `mix-sim.js`: demonstração automática que pausa ao pegar um slider;
  - o canvas `connections.js`, já existente, faz o ambiente do hero que reage ao cursor.

  O "scrub" é feito por variáveis CSS (`--p` e `--sp`) ligadas à rolagem, animando só transform, opacity, stroke-dashoffset e clip-path. Tudo pausa fora da tela e respeita `prefers-reduced-motion`.
- **Migração não destrutiva:**
  - `Seeder::migrateEco()` roda uma vez, no próximo login de um administrador depois do deploy;
  - um campo vazio, ou ainda igual ao seed v1.1 (`app/seed/legacy-eco.php`), recebe o texto novo; um campo editado é mantido e listado nas pendências;
  - o estado anterior vira uma versão no histórico, e registros publicados sem alterações pendentes são republicados.

  Testado com `npm run test:migration`.

## ADR-012 — Retratos dos sócios
- **Origem:** os originais vieram em RAW Canon (.CR2). Não há conversor de RAW no ambiente, então usei a prévia JPEG em tamanho cheio que a câmera grava dentro do arquivo (5184×3456), com a rotação registrada pela câmera aplicada. Os originais RAW não vão para o repositório.
- **Luz e branco:** ganho linear por canal calculado a partir da parede de fundo, para que a parede fique com o mesmo cinza neutro claro nas três fotos (≈ 220/255), com compressão suave dos realces para não estourar os brancos.
- **Enquadramento:** recorte 2:3 com o topo da cabeça a ~11% da altura e a cabeça na mesma proporção do quadro nas três, detectado automaticamente pela transição cabelo/parede. Saída: 1400×2100 JPEG em `app/seed/people/`.
- **Seção Os Sócios:** layout B aprovado pelo cliente (protótipos em `docs/prototipos-socios/`): três colunas iguais 2:3 com cantos arredondados, nome e cargo sobre a foto; o selecionado (hover, foco ou toque) ganha cor e revela a bio. No celular ficam um abaixo do outro, com a bio visível. Como as colunas não mudam de largura, não sobram faixas nas laterais e as cabeças continuam alinhadas.
- **Publicação:** o seed importa os retratos (direito de uso confirmado, ponto focal 50% 30%) e publica os três uma única vez, quando cargo, bio e retrato estão completos. Se forem despublicados no painel depois, o seed não os republica.

## ADR-013 — Item "Sócios" no menu (revogado em 24/10/2026)
- Pedido do cliente (24/10/2026): "Sócios" no menu principal, depois de "Ecossistema Full", e no grupo do rodapé, depois de "S&M". Leva à seção Os Sócios da Home (`/#socios`). O briefing v1.1 não previa esse item.
- Em sites já instalados, o seed adiciona o item uma única vez. Ele pode ser renomeado, movido ou removido em Configurações › Menus, e não volta se for removido.

## ADR-014 — Hero da Home com o símbolo Full
- Proposta A aprovada (protótipos em `docs/prototipos-hero/`): o símbolo (espiral de pontos no ângulo áureo, com anéis vazados) é desenhado em canvas por `assets/js/modules/symbol.js`, com as cores tiradas dos tokens (`--white`, `--brand-cyan`, `--brand-cyan-dark`).
- Animação: o símbolo nasce do centro para fora, depois gira devagar e "respira". Perto do cursor, os pontos se afastam e acendem.
- Desempenho e acessibilidade: a animação pausa fora da tela e com a aba oculta. Com movimento reduzido, o símbolo aparece completo e parado. No desktop os pontos se dissolvem atrás do título (máscara); no celular o símbolo fica acima do texto, mais suave.
- O título continua alternando entre o título principal e o alternativo (ou a assinatura). Quando o texto alternativo tem duas frases ou mais, a última vira destaque menor, em azul e na linha de baixo. É regra de formatação, sem texto fixo no código: hoje, "Ative o modo Full.".
- Medição (1366×768): quadros p50 16,7 ms / p95 18,3 ms, nenhuma tarefa longa.

## ADR-015 — Faixa de logos de clientes
- Em 24/10/2026, a Full enviou 15 logos brancos (SURA, Shell Select, Paraíso Saudável, OXXO, Mundo Verde, Luxo & Design, Intermodal South America, Fispal Tecnologia, Tecnocarne, Dinner in the Sky, Brentwood, AVIMA, Ambev, ABF Franchising Expo e 4REAL) e confirmou a autorização de exibição de todos. A autorização fica registrada em cada cliente (Autorização registrada + Registro da autorização).
- Os arquivos ficam em `app/seed/clients/`, recortados e com 240 px de altura. O seed importa os logos com direito de uso confirmado e publica cada cliente uma única vez (`client:<slug>` em seed_migrations). Quem for despublicado no painel depois disso não volta.
- Tamanho: todos os logos ocupam a mesma área visual. A altura é proporcional a 1/√(proporção), então logos largos ficam mais baixos e logos compactos, mais altos.
- Layout (pedido do cliente, 24/10/2026): a faixa ocupa a largura toda da página, em contraponto ao conteúdo, que passou de 1080 para **1200 px** centralizados (token `--content`). A faixa ficou mais fina e os logos, maiores.
- **Sem pausa** (pedido do cliente): o botão de pausa e a pausa no hover foram removidos. Atenção: o critério WCAG 2.2.2 (Pausar, parar, ocultar) pede um meio de pausar movimento automático que dure mais de 5 s. A mitigação é que, com "reduzir movimento" ativo no sistema, a faixa aparece parada, em grade. Para voltar a atender o critério por completo, basta restaurar o botão.

## ADR-016 — Sem item "Sócios" no menu
- Pedido do cliente (24/10/2026): os sócios existem só como seção da Home, sem item próprio no menu. Esta decisão revoga o ADR-013.
- O item saiu do menu principal e do rodapé no seed. Em sites já instalados, o seed remove o item `/#socios` uma única vez (`menu-socios-removido-2026-10`). A seção Os Sócios da Home continua igual.
