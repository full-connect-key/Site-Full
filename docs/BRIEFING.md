# FULL CONNECT KEY — BRIEFING EXECUTÁVEL PARA CLAUDE CODE
**Versão 1.1 (revisada) | 14 sprints autônomas e sequenciais (00–13) | 23/10/2026**

> **INSTRUÇÃO INICIAL PARA CLAUDE CODE:** Leia este documento inteiro antes de escrever código. Ele é a fonte de verdade do projeto. Salve uma cópia dele no repositório em `docs/BRIEFING.md` (sem alterar a seção 6). Crie os arquivos de governança da seção 2.2, registre as 14 sprints em `docs/SPRINT_STATUS.md` e execute-as em ordem, **sem solicitar um novo prompt a cada sprint**. Trabalhe até concluir o escopo ou encontrar uma decisão realmente bloqueante (lista fechada na seção 2.4). Não publique em produção, não contrate serviços, não configure cobranças, não exclua dados existentes e não execute migrações destrutivas sem autorização expressa. Se faltarem credenciais ou conteúdo real, crie a estrutura, registre a pendência em `docs/CONTENT_GAPS.md` e avance no que for possível. **Não invente dados para preencher lacunas.** Ao final, entregue o relatório descrito na seção 7.

---

## 0. Princípios inegociáveis

- Marca: **Full Connect Key**. Conceito: **“O que se conecta, se multiplica.”** Linguagem editorial na estética, tecnológica na experiência, humana na comunicação. Site visualmente ousado, com interatividade de propósito — não um template de agência com cards genéricos.
- Referências de **características**, não de cópia: DPZ (tipografia), Bullet (narrativa), Ana Couto (portfólio assimétrico), Human Academy (interações e animações). Não reproduzir layouts, textos, imagens ou código desses sites.
- **O negócio do cliente é o centro do Ecossistema Full**, nunca a própria Full. **S&M é o método da agência, não uma plataforma** — nunca aparece como sexto produto.
- Não incluir página “Sobre a Full” nesta versão. Não adicionar cases à página S&M.
- **CMS-first:** todos os textos, imagens, vídeos, CTAs, alt texts, SEO, dados de pessoas e itens de listas são editáveis no painel. Nenhum conteúdo editorial hardcoded em JSX/TSX. Layout e lógica de interação ficam no código; ajustes de apresentação expostos no CMS são limitados e seguros (ex.: variante de tema, ordem, ativar/ocultar bloco).
- Os textos da seção 6 são **seed inicial editável**, não strings fixas de interface.
- **`[PENDENTE: ...]` nunca vai ao site publicado.** O campo fica vazio/rascunho no CMS e o bloco público correspondente é omitido até existir conteúdo verificado.
- Nunca inventar: números de resultados, depoimentos, nomes de sócios ou equipe, biografias, matérias de imprensa, clientes, logos de terceiros, contatos, endereços, funcionalidades reais das plataformas ou promessas de performance. Nada de lorem ipsum, fotos de banco de imagens apresentadas como equipe/clientes, nem métricas “de exemplo” em componentes públicos.
- Acessibilidade não é opcional: nenhuma informação essencial apenas no hover; tudo funciona com toque, teclado e leitor de tela; foco visível; respeitar `prefers-reduced-motion` (versão estática equivalente, não a remoção do conteúdo).
- O CMS oferece rascunho, preview autenticado, publicação, histórico de versões com restauração, permissões por papel, ordenação de coleções, upload e organização de mídia. Campos com texto de ajuda e validação de tamanho — **nunca truncar texto silenciosamente** no frontend.
- Idioma único: português do Brasil (`lang="pt-BR"`, datas e números em pt-BR). Estrutura não deve impedir i18n futuro, mas não implementar agora.

---

## 1. Identidade visual — tokens oficiais

### 1.1 Marca
| Token | Valor | Uso |
|---|---|---|
| `brand.cyan` | `#68CCF2` | destaque, botões e links **sobre fundo escuro** |
| `brand.logoCyan` | `#5FC5EF` | somente detalhes **nos arquivos oficiais de logo** (não usar na UI) |
| `brand.cyanDark` | `#1B6F98` | links e destaques **sobre fundo claro** |
| `neutral.heroBlack` | `#05080A` | fundo do hero |
| `neutral.heroBlackAlt` | `#080B0D` | variação de profundidade/gradiente do hero |
| `neutral.graphite` | `#0E1113` | fundo principal escuro; texto principal em claro |
| `neutral.darkCard` | `#161B1F` | cards em escuro |
| `neutral.darkBorder` | `#262D33` | bordas em escuro |
| `neutral.darkText2` | `#C9CFD4` | texto secundário em escuro |
| `neutral.darkText3` | `#9AA3AB` | texto terciário/legendas em escuro |
| `neutral.paper` | `#F5F6F4` | seções claras; texto principal em escuro |
| `neutral.white` | `#FFFFFF` | cards em claro |
| `neutral.lightText2` | `#3F474E` | texto secundário em claro |
| `neutral.lightText3` | `#4A525A` | texto terciário/legendas em claro |
| `neutral.lightBorder` | `#E2E5E1` | bordas em claro |

**Regras de contraste:** `brand.cyan` (#68CCF2) **não** é usado como cor de texto sobre `paper`/`white` (contraste insuficiente) — em fundo claro use `brand.cyanDark`. Botão ciano em fundo escuro usa texto `graphite`. Todo par texto/fundo usado deve atingir WCAG 2.2 AA (4,5:1 texto normal; 3:1 texto grande e componentes de UI). Criar teste automatizado que valide os pares de tokens declarados.

### 1.2 Produtos — preservar cores dos logos oficiais
| Produto | Oficial (logo, áreas, ícones) | Texto sobre fundo escuro | Texto sobre fundo claro |
|---|---|---|---|
| Full Hub | `#0267C1` | `#4C9BE8` | `#0267C1` |
| Full Dash | `#F43156` | `#FF6B85` | `#C8183D` |
| Full Leads | `#09E85E` | `#09E85E` | `#087A35` |
| FullMix | `#FFDD21` | `#FFDD21` | `#7A6400` |
| FullMatch | `#FF7E70` | `#FF7E70` | `#B4402F` |

- **Proibido reintroduzir a paleta anterior** `#091B2D` / `#8ED2E5` (e qualquer azul-marinho/ciano antigo). Criar verificação automatizada (grep no CI/lint) que falhe se esses valores aparecerem no código-fonte (excluindo a pasta `docs/`, onde eles são citados como proibidos).
- Logos sempre dos arquivos oficiais (SVG preferencialmente); não redesenhar, recolorir ou recriar por aproximação. Enquanto o arquivo oficial não existir, usar **wordmark tipográfico neutro** com o nome do produto e registrar em `CONTENT_GAPS.md` — nunca um logo inventado.
- Nomenclatura exibida: **Full Hub, Full Dash, Full Leads, FullMix, FullMatch** (com e sem espaço exatamente assim).
- Tokens de cor ficam no código (fonte de verdade) e são **somente leitura** no CMS; o editor escolhe entre variantes nomeadas (ex.: `dark`, `light`, `product:hub`), nunca digita hex livre.

### 1.3 Tipografia
A tipografia definitiva ainda não foi informada. Na Sprint 01:
1. Verificar se existem arquivos de fonte licenciados no repositório ou em `assets/brand/`.
2. Se não houver, usar fontes open source de alta qualidade (licença OFL) via `next/font`, com escala tipográfica editorial (display grande e contrastante + texto de leitura confortável), e registrar em `DECISIONS.md` e `CONTENT_GAPS.md` como “fonte provisória — confirmar com a marca”.
3. Toda a tipografia sai de tokens (`font.display`, `font.text`, escala fluida com `clamp()`), para que a troca futura seja feita em um único lugar.

---

## 2. Arquitetura técnica e organização do trabalho

### 2.1 Stack padrão
**Next.js (App Router) + TypeScript (strict) + Tailwind CSS + Payload CMS 3 (integrado ao próprio app Next.js) + PostgreSQL.**
- Animação: GSAP + ScrollTrigger para narrativas de scroll; Motion para microinterações. **Nunca** duas bibliotecas animando a mesma propriedade do mesmo elemento. Toda animação passa por primitives próprias em `src/animations/` que respeitam `prefers-reduced-motion`.
- Mídia: adapter de storage abstraído — **disco local em desenvolvimento**; S3-compatível só quando houver conta autorizada.
- Banco local: PostgreSQL via Docker Compose (`docker-compose.yml` com Postgres e Mailpit para e-mail de desenvolvimento).
- Testes: **Vitest** (unitário/integração), **Playwright** (e2e, viewports 375/768/1280/1440), **@axe-core/playwright** (acessibilidade), Lighthouse CI local.
- Qualidade: ESLint, Prettier, `tsc --noEmit`, verificação de cores proibidas, verificação de strings hardcoded (ver Sprint 12).
- Usar as versões estáveis atuais e compatíveis entre si (conferir na instalação; não usar canary/beta). Registrar versões em `DECISIONS.md`.
- Antes de instalar, inspecionar repositório, Node/package manager disponíveis e restrições. Se algo impedir esta stack, propor alternativa documentada **antes** de seguir (é um ponto de parada, ver 2.4).
- Não criar contas, não configurar serviço pago, analytics, CDN, reCAPTCHA/Turnstile ou provedor de e-mail sem aprovação. Respeitar licenças de fontes, imagens e bibliotecas.

### 2.2 Arquivos de governança obrigatórios
- `CLAUDE.md` (raiz): instruções curtas e permanentes para sessões futuras — comandos, regras inegociáveis resumidas, “leia `docs/SPRINT_STATUS.md` para saber onde parou”, link para `docs/BRIEFING.md`.
- `docs/BRIEFING.md`: cópia integral deste documento.
- `docs/PROJECT_RULES.md`: decisões de marca, conteúdo, interação e arquitetura.
- `docs/CONTENT_MODEL.md`: coleções, globais, blocos, campos, relações, slugs, limites, regras de publicação e de exibição condicional.
- `docs/SPRINT_STATUS.md`: as 14 sprints com status `TODO / IN_PROGRESS / DONE / BLOCKED / DONE_WITH_GAPS`, checklist de entregáveis, commits, testes executados e resultado, riscos, decisões e **“Ponto de retomada”** sempre atualizado.
- `docs/DECISIONS.md`: ADRs curtos (contexto, decisão, alternativas, consequências).
- `docs/CONTENT_GAPS.md`: tabela de conteúdo/credenciais/ativos faltantes — item, onde aparece, impacto, fallback atual, prioridade (P0 lançamento / P1 / P2). Sem dados fictícios.
- `docs/QA_REPORT.md`: evidências de testes, a11y, performance e QA por viewport.
- `docs/DEPLOYMENT.md`: setup local, staging, produção, variáveis de ambiente, migrations, rollback, backup e restauração.
- `docs/EDITOR_GUIDE.md`: guia em linguagem simples para a equipe editar textos, imagens, cases, artigos e matérias no painel.

### 2.3 Política de execução automática
1. Ler tudo; auditar o repositório; preservar trabalho existente (nunca sobrescrever alterações do usuário sem conferir).
2. Criar governança e registrar as sprints; começar pela 00.
3. Uma sprint por vez. Ao concluir: `typecheck`, `lint`, `build`, testes unitários e e2e pertinentes. Corrigir falhas antes de marcar `DONE`. Se algo depende de pendência externa, marcar `DONE_WITH_GAPS` e listar.
4. Commits pequenos e rastreáveis (Conventional Commits, ex.: `feat(home): hero com alternância de headline`), um ou mais por sprint, quando o repositório permitir. Se não houver git, inicializar repositório local (sem remoto).
5. Atualizar `SPRINT_STATUS.md` e demais docs **ao final de cada sprint** e seguir para a próxima automaticamente.
6. **Gestão de contexto:** sessões longas perdem contexto. Ao fim de cada sprint, o “Ponto de retomada” em `SPRINT_STATUS.md` deve permitir que uma sessão nova continue só lendo `CLAUDE.md` + `SPRINT_STATUS.md`. Não depender de memória da conversa.
7. Bloqueio externo (credencial, logo oficial, dado real) → item `BLOCKED` + fallback honesto (estado vazio, wordmark neutro, bloco oculto) + seguir com o restante.
8. Não declarar concluído o que não foi testado. Se o tempo acabar, registrar exatamente onde parou e qual o próximo comando/tarefa.

### 2.4 Pontos de parada (lista fechada)
Parar e pedir decisão **somente** quando:
- a stack padrão for inviável ou houver escolha de hospedagem/arquitetura com impacto relevante;
- houver qualquer custo, criação de conta ou serviço externo;
- uma ação puder destruir dados ou exigir migração destrutiva;
- houver mudança de escopo em relação a este documento;
- for preciso usar ativo sem direito de uso confirmado;
- for publicar em produção ou apontar domínio.
Fora disso, decidir com bom senso, registrar em `DECISIONS.md` e continuar.

### 2.5 Definition of Done (vale para toda sprint)
- Conteúdo editorial 100% vindo do CMS; seed atualizado e idempotente.
- Rascunho invisível ao público; publicação revalida a página (on-demand revalidation via hooks do Payload).
- Estados vazio, carregando e erro tratados; bloco sem conteúdo é omitido sem deixar buraco no layout.
- Funciona com mouse, toque e teclado; foco visível; `aria-*` corretos; versão com movimento reduzido.
- Sem erros no console; sem overflow horizontal em 375px.
- Teste e2e cobrindo o fluxo principal da sprint + verificação axe sem violações sérias/críticas.
- `typecheck`, `lint`, `build` e testes passando; docs atualizados; commit feito.

---

## 3. Mapa do site

Menu principal: S&M; Ecossistema Full (com submenu); Cases; Insights Full; Full na Mídia; botão **Vamos conversar**. Logo retorna à Home.

Rotas (slugs editáveis no CMS; mudança de slug cria **redirect 301 automático** via coleção `redirects`):
- `/` — Home (11 seções)
- `/sm` — Método S&M (3 seções)
- `/ecossistema` — Ecossistema Full (3 seções)
- `/ecossistema/full-hub`, `/ecossistema/full-dash`, `/ecossistema/full-leads`, `/ecossistema/fullmix`, `/ecossistema/fullmatch` — 3 seções cada
- `/cases` e `/cases/[slug]`
- `/insights` e `/insights/[slug]`
- `/full-na-midia`
- `/contato`
- `/politica-de-privacidade`, `/politica-de-cookies` — estrutura pronta; conteúdo jurídico **somente após validação**. Enquanto não houver texto aprovado, a página fica em rascunho e os links apontam para ela apenas em staging (registrar como P0 em `CONTENT_GAPS.md`).
- `/admin` — painel (protegido, `noindex`).
- 404, erro global e estados de erro por rota.

---

## 4. Modelagem de conteúdo — CMS-first

**Globais:**
- `siteSettings`: nome, logos (claro/escuro), menu principal e submenu, rodapé (textos, links), contatos e redes (vazios até informados), SEO base e imagem OG padrão, `contactRecipients` (destinatários do formulário por assunto — vazio até informado).
- `themeSettings`: variantes de tema permitidas (somente leitura para editor; tokens no código).
- `uiCopy`: rótulos de botões, mensagens de validação/erro/sucesso, textos de acessibilidade (aria-labels), banner de cookies, 404 e erro temporário.
- `homepage`: singleton com os 11 blocos da Home em ordem fixa (cada bloco pode ser ocultado, não reordenado livremente, para preservar a narrativa).

**Páginas:** `pages` com `slug`, `title`, `status`, `seo` (title, description, OG image, `noindex`), `sections` em **blocos tipados** (um tipo por padrão de seção; nada de JSON livre), `themeVariant`. Páginas S&M, Ecossistema, Cases (listagem), Insights (listagem), Full na Mídia, Contato e legais usam `pages` com blocos específicos.

**Coleções:**
- `media`: arquivo, alt (obrigatório para imagens não decorativas), crédito/licença, direito de uso confirmado (boolean), focal point, poster para vídeo, tamanhos gerados.
- `platforms`: nome, slug, chave de cor (enum dos 5 produtos), logo oficial, slogan/assinatura, resumo (Home/Ecossistema), hero, itens de solução (título, texto, ordem), configuração da interação (somente parâmetros seguros), CTA, SEO, ordem.
- `sectors`: nome, título, texto, imagem, ordem (Varejo, Imobiliário, Feiras e Eventos — seed).
- `clients`: nome, logo, **autorização de exibição** (só aparece no marquee se `true`), setor, ordem.
- `cases`: título, slug, cliente (relação), setor (relação), tipo, headline conceitual, resumo, KV, desafio, ideia, nossa atuação, galeria ordenada (imagens/vídeos com legenda), resultados **opcionais** (rótulo, valor, fonte/observação), destaque na Home (posição 1–3), próximo case (manual ou automático), SEO, status.
- `institutionalMetrics`: rótulo, valor, fonte/validação, ordem (vazio até informado).
- `partners`: nome, cargo, bio, foto, ordem.
- `teamMembers`: nome, cargo, foto, frase “Conecta X a Y.”, ordem.
- `testimonials`: texto, nome, cargo, empresa, foto opcional, **autorização registrada**, ordem.
- `articleCategories`: Estratégia, Branding, Tecnologia, Comportamento, Negócios (seed).
- `authors`: nome, cargo, foto.
- `articles`: categoria, título, slug, subtítulo, resumo, corpo rico (Lexical com blocos de imagem, citação, embed de vídeo), autor, capa, data, leitura estimada (calculada automaticamente), destaque, relacionados (manual ou por categoria), SEO, status.
- `pressMentions`: veículo, logo do veículo (com autorização), manchete original, resumo próprio, data, tipo (entrevista/reportagem/artigo), URL original, capa, ordem, status.
- `contactSubmissions`: assunto, nome, e-mail, empresa, mensagem, consentimento + timestamp, status de envio. Acesso restrito a admin; política de retenção configurável (padrão sugerido 12 meses, registrar como decisão a validar).
- `redirects`: origem, destino, tipo.
- `users`: papéis `admin` (tudo) e `editor` (conteúdo e mídia; sem usuários, configurações sensíveis ou submissões de contato).

**Comportamento editorial:**
- Publicar apenas registros válidos; campos obrigatórios só para o que de fato quebra a página.
- Resultado/métrica ausente → bloco oculto, sem buraco. Lista vazia → estado vazio com texto do CMS (seção 6) ou omissão da seção na Home.
- Publicar case/artigo/matéria cria rota, entra em listagens, sitemap e “próximo/relacionados” automaticamente.
- Reordenação no painel reflete no frontend após publicação.
- APIs públicas nunca expõem rascunhos, submissões ou campos internos (testar).
- Preview autenticado (live preview do Payload) para páginas, cases e artigos.
- Seed idempotente: identificar registros por chave estável (`slug`/`seedKey`); rodar duas vezes não duplica nem sobrescreve edições feitas no painel (seed só cria o que não existe, salvo flag `--reset` explícita em ambiente local).
- Segurança: validação server-side, sanitização do rich text, proteção CSRF conforme arquitetura, rate limiting e honeypot no formulário, headers de segurança (CSP, HSTS em produção, X-Content-Type-Options, Referrer-Policy).

---

# 5. SPRINTS DE EXECUÇÃO

> Cada sprint tem: **Objetivo · Entregáveis · Testes · Aceite**. A Definition of Done (2.5) vale para todas. A consistência editorial (nada hardcoded) é verificada **em toda sprint**, não só no final.

## SPRINT 00 — Fundação, auditoria e arquitetura
**Objetivo:** base técnica confiável sem destruir nada existente.

**Entregáveis:**
1. Auditoria do repositório/ambiente (runtime, package manager, arquivos existentes) registrada em `DECISIONS.md`.
2. Projeto Next.js + TS strict + Tailwind + Payload 3 + PostgreSQL (ou alternativa aprovada).
3. `docker-compose.yml` (Postgres + Mailpit), `.env.example` documentado, sem segredos; `.env*` no `.gitignore`.
4. Scripts: `dev`, `build`, `start`, `lint`, `typecheck`, `test`, `test:e2e`, `db:migrate`, `db:seed`, `check:colors`.
5. Estrutura `src/app`, `src/components`, `src/features`, `src/animations`, `src/cms` (collections, globals, blocks, hooks, access), `src/lib`, `src/styles`, `tests/`.
6. Painel `/admin` com primeiro usuário admin criado via variável de ambiente local (nunca senha no código); upload de mídia em disco local.
7. Coleção `users` com papéis e globais `siteSettings`/`uiCopy` mínimos (necessários para a navegação da Sprint 01).
8. Todos os arquivos de governança (2.2) criados; sprints registradas.

**Testes:** build; typecheck; lint; teste de conexão com banco; e2e de login no painel.
**Aceite:** projeto sobe localmente com um comando documentado, painel autentica, banco conecta, checks passam, nenhuma credencial exposta.

## SPRINT 01 — Design system, navegação e motion foundation
**Objetivo:** identidade digital consistente e reutilizável.

**Entregáveis:** tokens (1.1/1.2) em CSS variables + Tailwind; tipografia (1.3); grid editorial (12 col. desktop, 4 col. mobile) e escala de espaçamento; componentes acessíveis base (Button, Link, Heading, Section, Container, Media com focal point, Tabs/Selector acessível, Slider acessível, Dialog/Menu); header com comportamento de scroll (esconde ao descer, aparece ao subir, nunca cobre foco); menu mobile full-screen com trap de foco e Esc; submenu do Ecossistema acessível por teclado; footer com CTA; transição de páginas por linha/conexão (desligada com movimento reduzido, sem atrasar navegação); primitives de motion; skeleton/loading/error; página `/styleguide` interna (`noindex`, só em dev/staging) para revisar componentes; menus e rodapé lidos do CMS.

**Testes:** e2e de navegação desktop/mobile/teclado; axe; teste de contraste dos pares de tokens; `check:colors`.
**Aceite:** tokens exatos; zero ocorrências da paleta antiga; menu/rodapé editáveis; nenhum efeito bloqueia ou atrasa a navegação.

## SPRINT 02 — CMS completo, painel editorial e seed
**Objetivo:** autonomia de edição antes das páginas.

**Entregáveis:** toda a modelagem da seção 4; painel agrupado em Home, Páginas, Ecossistema, Cases, Insights, Imprensa, Pessoas, Mídia, Configurações; papéis admin/editor com controle de acesso testado; versões, drafts, autosave, live preview, validações com texto de ajuda e limites de caracteres; ordenação; hooks de revalidação; redirects automáticos em troca de slug; **seed de todos os textos da seção 6** (sem os `[PENDENTE]`, que viram campos vazios + entradas em `CONTENT_GAPS.md`); `docs/CONTENT_MODEL.md` completo; primeira versão de `docs/EDITOR_GUIDE.md`.

**Testes:** integração — editor não acessa submissões/usuários; API pública não retorna rascunho; seed rodado 2× não duplica; trocar slug gera redirect.
**Aceite:** editar headline no CMS altera preview e, ao publicar, o site; criar artigo/case cria rota; rascunho privado; imagens trocáveis; seed repetível.

## SPRINT 03 — Home, parte 1 (seções 1 a 6)
**Objetivo:** abertura de marca e principais interações.

1. **Hero:** alternância vertical entre “O que se conecta, se multiplica.” e “Vire a chave dos seus resultados. Ative o modo Full no seu negócio.” (ambas no DOM e acessíveis; leitor de tela não fica repetindo a troca — `aria-live` desligado); background abstrato de pontos/linhas reagindo ao cursor (canvas/WebGL leve, pausa fora da viewport e em aba oculta; em toque, reage ao scroll ou fica estático; com movimento reduzido, estático). Texto de apoio e CTA “Descubra a Full”.
2. **Marquee de logos sem título:** só clientes com autorização; pausa no hover/foco; botão de pausa acessível; movimento reduzido = grade estática; com zero logos autorizados a seção é omitida.
3. **Nossa Essência:** título + quatro colunas editoriais (intro, Conecta, Organiza, Multiplica) com linhas que se desenham no scroll.
4. **Ecossistema:** cinco plataformas com cor oficial, resumo e link para cada página; CTA para `/ecossistema`.
5. **Setores:** Varejo, Imobiliário, Feiras e Eventos; estado inicial sem seleção (mostra intro); selecionar troca texto/imagem; clicar de novo desseleciona; implementado como grupo de botões com `aria-pressed`/`aria-controls`.
6. **Cases:** 1 grande à esquerda + 2 empilhados à direita, gaps pequenos, cantos retos, KVs reais (destacados no CMS); nome/categoria no hover/foco (desktop) e sempre visíveis de forma discreta no mobile; indicadores abaixo alternam conforme o case em foco **somente se houver métricas reais**; sem métricas, a faixa de indicadores é omitida. Com menos de 3 cases publicados, o grid se adapta (1 ou 2) ou a seção é omitida.

**Testes:** e2e de cada interação por mouse, toque (emulado) e teclado; axe; snapshot visual em 375/1280.
**Aceite:** seções 1–6 funcionam com conteúdo do CMS e com CMS vazio, sem buracos nem dados falsos.

## SPRINT 04 — Home, parte 2 (seções 7 a 11)
**Objetivo:** pessoas, prova social e conteúdo.

7. **Os Sócios:** três retratos altos; no hover/foco o retrato selecionado expande horizontalmente e revela a bio; no mobile, acordeão/lista acessível. Sem os três sócios cadastrados → seção omitida.
8. **Depoimentos:** slider com troca a cada ~6 s, setas, swipe, indicadores; pausa sob hover/foco e com movimento reduzido (sem autoplay); botão de pausa. Sem depoimentos autorizados → seção omitida.
9. **O Time:** galeria horizontal arrastável (também rolável por teclado e com botões); hover/foco revela “Conecta X a Y.”; no toque, a frase fica visível. Sem membros → seção omitida.
10. **Insights:** matéria grande + lista lateral de manchetes selecionáveis que trocam imagem e texto (tabs acessíveis); artigos reais do CMS; sem artigos → seção omitida.
11. **Footer CTA cinematográfico:** “Grandes resultados começam com uma conexão.” + apoio + “Vamos conversar”.

**Testes:** e2e das interações; axe; teste de Home com CMS vazio (só seções sem dependência de conteúdo real aparecem).
**Aceite:** Home completa; nenhuma seção quebra por ausência de conteúdo; nenhum depoimento, pessoa ou métrica inventada.

## SPRINT 05 — S&M, o método
**Objetivo:** narrativa de método, não catálogo de serviços.

**Exatamente três seções:** hero “Negócios evoluem. Pessoas mudam. Conexões transformam.”; narrativa interativa Entender / Conectar / Amplificar com transições por scroll (seção fixada com progresso visível; não apresentar como fases rígidas numeradas de processo formal); CTA “Esta é a chave.”. Com movimento reduzido, as três partes aparecem empilhadas e legíveis. **Sem cases; S&M nunca vira plataforma.**

**Testes:** e2e de scroll e teclado; axe; mobile.
**Aceite:** narrativa fluida, copy do CMS, acessível e funcional no mobile.

## SPRINT 06 — Ecossistema Full
**Objetivo:** visão geral com **o negócio do cliente** no centro.

**Entregáveis:** hero “Seu negócio no centro. Todas as possibilidades ao redor.” / “Um ecossistema. Múltiplas possibilidades.”; navegação interativa entre as cinco soluções (títulos à esquerda; visual grande à direita com “Seu negócio” no centro e linhas/pontos conectando a solução ativa); seleção troca imagem, cor, texto e conexões; link para cada página de produto; CTA “Qual conexão pode transformar o seu negócio?”. S&M aparece apenas como método transversal no texto, **não** como nó do diagrama.

**Testes:** e2e de seleção por clique, toque e setas do teclado; axe.
**Aceite:** centralidade do cliente evidente, cores oficiais, seleção acessível, conteúdo do CMS.

## SPRINT 07 — Páginas Full Hub, Full Dash, Full Leads, FullMix e FullMatch
**Objetivo:** cinco experiências próprias, cada uma com hero, interação e CTA, usando um template comum com uma **interação exclusiva por produto**.

- **Full Hub (organizar):** caos de mensagens/aprovações que se reorganiza em fluxo claro (solicitações → responsáveis e prazos → aprovações → visibilidade) conforme o scroll ou seleção.
- **Full Dash (otimizar):** seletor Gestão de mídia / Otimização contínua / Resultados de negócio, cada um com gráfico **ilustrativo** (rotulado como tal, sem números que pareçam dados reais). Não confundir com FullMix.
- **Full Leads (vender):** atrair → capturar → nutrir → vendas como rede de pontos que convergem em oportunidades — não um funil clichê.
- **FullMix (medir):** blocos Mensurar / Comparar / Orientar + simulador com sliders de distribuição de orçamento entre canais, **explicitamente ilustrativo**: aviso obrigatório (seção 6.7) sempre visível junto ao simulador, sem projeção de vendas, ROI ou recomendação automática.
- **FullMatch (fidelizar):** seletor de perfis Recorrente / Alto valor / Inativo mostrando tipo de oferta e ciclo de recompra de forma conceitual.

**Testes:** e2e das cinco interações (mouse, toque, teclado); axe; verificação de que o aviso do FullMix está presente.
**Aceite:** cinco rotas, cinco cores oficiais, cinco interações distintas, nenhum dado fictício apresentado como fato, textos/mídia editáveis.

## SPRINT 08 — Cases e case individual
**Objetivo:** portfólio com narrativa e impacto visual.

**Listagem (3 seções):** hero, grid, CTA. Filtros Todos/Varejo/Imobiliário/Feiras e Eventos (refletidos na URL, ex.: `?setor=varejo`, para compartilhar e voltar); grid editorial assimétrico, cantos retos, gaps pequenos; nome/categoria no hover/foco desktop e identificação discreta visível no mobile; reflow suave na filtragem; “Carregar mais”; estado vazio do CMS.
**Case individual (quatro momentos):** KV full-bleed com nome/categoria/headline; O desafio + A ideia (+ Nossa atuação); galeria editorial mista (imagens e vídeos com poster, sem autoplay com som); resultados **somente se reais** (bloco omitido se vazio) + próximo case.

**Testes:** e2e — publicar case no painel → aparece na listagem/filtro/rota/sitemap; next-case cíclico; case sem resultados sem buraco.
**Aceite:** novo case publicável sem código; filtros, mídia e próximo case funcionando.

## SPRINT 09 — Insights Full e artigos
**Objetivo:** revista digital autoral.

**Listagem:** hero “Ideias provocam. Conexões transformam.”; destaque editorial com manchetes laterais clicáveis; grid com filtro por categoria (na URL); “Carregar mais”; estado vazio; CTA.
**Artigo:** categoria, título, subtítulo, autor, data, leitura estimada automática, capa, corpo rico (imagens, citações, vídeo), largura de leitura confortável (~65–75 caracteres), relacionados, compartilhar (Web Share API + copiar link + links diretos, sem scripts de terceiros), SEO e structured data `Article`.

**Testes:** e2e de publicação, filtro, destaque, preview; axe; leitura em 375px.
**Aceite:** publicar artigo cria URL; filtros e destaque funcionam; leitura confortável no mobile.

## SPRINT 10 — Full na Mídia
**Objetivo:** arquivo de imprensa realmente interativo, **diferente** de Insights.

**Entregáveis:** hero “Boas histórias ganham o mundo.”; navegação horizontal imersiva entre matérias (manchetes respondendo ao cursor, expansão da matéria ativa, transições por máscara); itens com veículo, logo autorizado, manchete original, data, tipo, resumo próprio e link externo (`target="_blank" rel="noopener noreferrer"`, com aviso de link externo); arquivo e filtros aparecem **somente quando houver volume** (limiar configurável no CMS); CTA de imprensa levando ao contato com assunto “imprensa” pré-selecionado. Sem matérias → página mostra hero, estado vazio e CTA. **Nunca inventar publicações.**

**Testes:** e2e com teclado (setas/Tab) e toque; axe; verificação de links externos válidos nos registros publicados.
**Aceite:** não parece lista estática; utilizável por teclado e mobile; conteúdo do CMS.

## SPRINT 11 — Contato, privacidade e cookies
**Objetivo:** experiência conversacional com envio real e base legal preparada.

**Contato (2 seções):** hero “Toda grande ideia começa com uma conexão.” com pontos/linhas reagindo ao cursor; formulário progressivo — etapa 1 assunto (projeto, imprensa, parceria, outro; aceita pré-seleção por `?assunto=`), etapa 2 nome, e-mail, empresa opcional, mensagem e consentimento. Validação client + server (schema compartilhado, ex. Zod), honeypot, rate limiting por IP, sanitização; estados carregando/erro/sucesso; foco gerenciado entre etapas e nas mensagens de erro (`aria-describedby`). Mensagem “Sua conexão com a Full começou.” **apenas após sucesso confirmado pelo servidor**.
**Envio:** camada de e-mail com adapter — em desenvolvimento, Mailpit; em staging/produção, SMTP/provedor **somente após aprovação e credenciais**. Destinatários vêm de `siteSettings.contactRecipients`; sem destinatário configurado, a submissão é salva em `contactSubmissions` e o item é registrado como P0 em `CONTENT_GAPS.md`. Não inventar e-mail, telefone ou prazo de resposta.
**Cookies:** banner e central de preferências com os textos da seção 6.13. Como nenhuma ferramenta de análise/marketing está aprovada, o site **não carrega cookies opcionais**; deixar o mecanismo de consentimento pronto (categorias, armazenamento da escolha, bloqueio prévio de scripts) para quando houver ferramentas aprovadas. Se só houver cookies estritamente necessários, documentar se o banner deve ser exibido ou não (decisão a validar).
**Páginas legais:** estrutura e template; conteúdo em rascunho até texto jurídico aprovado.

**Testes:** e2e do formulário (sucesso via Mailpit, erros de validação, honeypot, rate limit, falha do servidor); axe; teste de que nenhum cookie opcional é gravado sem consentimento.
**Aceite:** envio real testado em ambiente local; dados protegidos; textos editáveis; nenhum script de terceiros sem consentimento.

## SPRINT 12 — Mobile, acessibilidade, performance, SEO e auditoria editorial
**Objetivo:** polimento técnico e garantia de que nada ficou preso ao código.

**Entregáveis:**
- **Auditoria editorial:** script que busca strings literais em componentes de página (exceto classes, aria vindos do CMS, chaves técnicas); conferência de cada texto do seed contra a seção 6; links internos, menus, relações e redirects.
- **Mobile:** breakpoints 320–1920, sem overflow, alvos de toque ≥ 44px, interações hover com equivalente em toque.
- **Acessibilidade:** WCAG 2.2 AA — teclado, ordem de foco, landmarks, headings, leitor de tela (roteiro manual básico documentado), contraste, movimento reduzido, skip link.
- **Performance:** imagens via `next/image` com tamanhos e formatos modernos, vídeos com poster e lazy, fontes com `display: swap` e subset, código de animação carregado sob demanda, canvas pausado fora da viewport. Lighthouse em Home, uma página de produto, um case e um artigo (mobile). Metas no p75 de campo: LCP ≤ 2,5 s, INP ≤ 200 ms, CLS ≤ 0,1 — medir em laboratório agora e documentar que validação de campo depende de tráfego real.
- **SEO técnico:** metadata por página a partir do CMS, OG/Twitter cards, canonical, `sitemap.xml` e `robots.txt` dinâmicos (staging sempre `noindex`), structured data (`Organization`, `WebSite`, `Article`, `BreadcrumbList`) apenas com dados reais.

**Testes:** axe em todas as rotas; Lighthouse documentado em `QA_REPORT.md`; script de strings hardcoded passando.
**Aceite:** sem barreiras críticas de acessibilidade, navegação mobile completa, performance medida com problemas prioritários corrigidos, nenhuma string editorial no código.

## SPRINT 13 — QA final, segurança, staging e entrega
**Objetivo:** homologação completa sem publicação não autorizada.

**Entregáveis:** suíte e2e completa (todas as rotas, formulário, CMS, auth, preview, filtros, responsividade); revisão de segredos (nenhum no repositório/histórico), permissões, headers de segurança, dependências com vulnerabilidades conhecidas (`npm audit` ou equivalente) e tratamento de erros; logging de erros no servidor (sem serviço pago); procedimento de backup e restauração do banco **testado localmente**; `DEPLOYMENT.md` completo (requisitos de hospedagem para Next.js + Payload + Postgres, variáveis, migrations, rollback, backup); staging configurado **apenas se houver infraestrutura autorizada** — caso contrário, documentar opções de hospedagem com prós/contras e custos estimados para decisão do usuário; checklist de pré-lançamento (conteúdo P0, textos legais, logos e direitos de uso, destinatários do formulário, domínio, DNS, HTTPS, redirects do site atual `fullconnectkey.com.br` para preservar SEO); `EDITOR_GUIDE.md` final; relatório final (seção 7).

**Testes:** build de produção local; e2e completo; teste de restore de backup.
**Aceite:** build e testes aprovados, CMS operacional, documentação de manutenção pronta; **nenhum deploy de produção sem autorização expressa**.

---

# 6. COPY DE REFERÊNCIA — SEED EDITÁVEL DO CMS

**Esta seção contém os textos aprovados como base de trabalho.** Não reescrever sem necessidade, não transformá-los em hardcode. Todo texto a seguir é sugestão editorial sujeita a alteração no painel. Texto entre colchetes é pendência real, não deve aparecer no site publicado.

## 6.1 HOME

**Hero:** “O que se conecta, se multiplica.” / “Vire a chave dos seus resultados. Ative o modo Full no seu negócio.” Apoio: “Conectamos estratégia, criatividade, tecnologia, marketing e vendas para transformar desafios em oportunidades e oportunidades em resultados.” CTA: “Descubra a Full”.

**Logos:** marquee sem título; exibir somente logos autorizados.

**Nossa Essência:** Título “Conexões que fazem negócios evoluírem.” Intro: “Acreditamos que grandes resultados não acontecem de maneira isolada. Eles surgem quando ideias, pessoas, estratégias e tecnologias trabalham em sintonia. É assim que enxergamos cada negócio: um universo de possibilidades que ganha força quando suas conexões são compreendidas, aprimoradas e potencializadas.” Conecta: “Aproximamos marcas, pessoas, ideias e oportunidades. Porque entender as relações é o primeiro passo para transformá-las.” Organiza: “Integramos conhecimento, processos e estratégias para que cada ação tenha propósito e cada decisão tenha direção.” Multiplica: “Testamos, aprendemos e aprimoramos continuamente para transformar boas conexões em resultados cada vez mais relevantes.”

**Ecossistema:** Título “Quem se conecta, evolui.” Apoio: “Um ecossistema de soluções que integra operação, mídia, oportunidades comerciais, inteligência de investimento e relacionamento com clientes. Tudo conectado às necessidades do seu negócio.” Resumos: Hub “Aprovações, solicitações, responsáveis e prazos conectados em um só lugar.” Dash “Gestão de mídia, acompanhamento de metas e otimização contínua dos investimentos.” Leads “Atração, captura e nutrição conectadas ao funil comercial.” FullMix “Inteligência para entender quanto cada canal contribui para as vendas.” FullMatch “Ofertas hiperpersonalizadas para estimular recorrência, ticket médio e reativação.” CTA “Explore o Ecossistema Full”.

**Setores:** Título “Cada mercado tem seus desafios. A gente se conecta a eles.” Intro “Conhecimento de mercado, repertório criativo e inteligência estratégica para entender diferentes jornadas de consumo e construir soluções relevantes.” Varejo — “Marcas presentes em cada escolha.” / “Do posicionamento à experiência de compra, conectamos comunicação, pontos de contato e estratégias comerciais para aproximar marcas e consumidores.” Imobiliário — “Conexões que transformam espaços em desejos.” / “Da construção de uma marca imobiliária à geração de oportunidades comerciais, criamos estratégias que valorizam empreendimentos e acompanham diferentes momentos da jornada de compra.” Feiras e Eventos — “Experiências que continuam além do evento.” / “Conectamos estratégia, comunicação e experiência para atrair públicos, fortalecer marcas e ampliar as oportunidades de negócio antes, durante e depois de cada evento.”

**Cases:** Título “Ideias que se transformam em resultados.” Apoio “Cada desafio exige um olhar diferente. Conheça projetos em que criatividade e estratégia se conectaram para construir novas possibilidades.” CTA “Explore nossos cases”. Indicadores [PENDENTE: métricas institucionais reais] e [PENDENTE: métricas reais por case].

**Sócios:** Título “Visões diferentes. Um propósito conectado.” Apoio “À frente da Full, diferentes experiências e perspectivas se encontram para construir uma visão integrada de comunicação, marketing e negócios.” [PENDENTE: nomes, cargos, bios, fotos dos três sócios].

**Depoimentos:** Título “Conexões que falam por si.” Apoio “Mais do que projetos, construímos relações. E são as pessoas que vivem essas experiências que melhor podem contar suas histórias.” [PENDENTE: depoimentos reais e autorizados].

**Time:** Título “Por trás de cada conexão, tem gente que faz acontecer.” Apoio “Diferentes talentos, repertórios e perspectivas. Um time que transforma colaboração em ideias, experiências e resultados.” [PENDENTE: nomes, fotos, frases individuais “Conecta X a Y.”].

**Insights:** Título “Ideias provocam. Conexões transformam.” Apoio “Perspectivas sobre marcas, comportamento, criatividade, tecnologia e negócios. Conteúdos para ampliar repertórios e descobrir novas possibilidades.” CTA “Explore nossos insights”.

**Footer CTA:** “Grandes resultados começam com uma conexão.” / “Seu próximo desafio pode ser o ponto de partida para algo extraordinário. Vamos descobrir juntos?” / “Vamos conversar”.

## 6.2 S&M

**Hero:** “Negócios evoluem. Pessoas mudam. Conexões transformam.” Apoio: “S&M é o nosso método de compreender negócios, identificar oportunidades e construir estratégias que conectam produtos e serviços aos desejos e interesses das pessoas. Integramos marketing e vendas para aprimorar cada ponto de contato entre marcas e consumidores, transformando conhecimento em ações e ações em resultados.”

**Método:** Título “Entender. Conectar. Amplificar.” Intro “Cada negócio possui seus próprios desafios, diferenciais e oportunidades. Por isso, nosso trabalho começa com um olhar atento à realidade de cada cliente e evolui por meio de estratégias integradas, testes e aprimoramento contínuo.” Entender — “Antes de criar conexões, precisamos compreender o que as movimenta.” / “Investigamos objetivos, particularidades, diferenciais e desafios de cada negócio. Observamos o mercado, o comportamento das pessoas e os pontos de contato que influenciam suas decisões.” Conectar — “Estratégias ganham força quando fazem sentido para as pessoas.” / “Transformamos conhecimento em estratégias que integram comunicação, marketing e vendas. Conectamos marcas aos interesses de seus públicos, criando experiências e oportunidades relevantes ao longo da jornada.” Amplificar — “O que funciona pode evoluir ainda mais.” / “Acompanhamos resultados, testamos possibilidades e aprimoramos continuamente as conexões construídas. Identificamos o que gera valor e direcionamos esforços para ampliar seu impacto.”

**CTA:** “Esta é a chave.” / “Entender as mudanças, conectar oportunidades e aprimorar resultados. Uma maneira integrada de pensar e atuar para que marcas e consumidores evoluam juntos.” / “Vamos conectar seu negócio?”

## 6.3 ECOSSISTEMA

**Hero:** “Seu negócio no centro. Todas as possibilidades ao redor.” / “Um ecossistema. Múltiplas possibilidades.” Apoio: “Cada negócio tem seus próprios desafios. O Ecossistema Full conecta soluções de gestão, performance, geração de oportunidades, inteligência de marketing e relacionamento para responder a diferentes necessidades. O ponto de partida é sempre o mesmo: o seu negócio.”

**Soluções:** Título “Soluções que se conectam ao que o seu negócio precisa.” Intro “Com o método S&M orientando nossa atuação, integramos plataformas que organizam processos, otimizam investimentos, conectam oportunidades comerciais e ajudam a ampliar resultados.” Hub: “Aprovação por WhatsApp não é processo. É sorte.” / “Gestão integrada da operação, com solicitações, aprovações, responsáveis, prazos e status visíveis para a Full e seus clientes.” Dash: “Verba vai para o que gera resultado.” / “Gestão e performance de mídia com otimização contínua, metas claras e acompanhamento de indicadores de negócio.” Leads: “Oportunidade pronta para fechar.” / “Estratégias de atração, captura e nutrição conectadas ao funil comercial para aproximar o time de vendas de oportunidades qualificadas.” FullMix: “Saiba o que cada canal vende.” / “Marketing Mix Modeling para analisar a contribuição dos canais online e offline para as vendas e orientar a distribuição dos investimentos.” FullMatch: “A oferta certa para quem já comprou.” / “Inteligência de comportamento de compra para personalizar ofertas, estimular a recorrência, aumentar o ticket médio e reativar clientes.”

**CTA:** “Qual conexão pode transformar o seu negócio?” / “As melhores soluções começam com a compreensão dos desafios certos. Vamos descobrir quais possibilidades fazem sentido para você.” / “Vamos conversar”.

## 6.4 FULL HUB

**Hero:** “Aprovação por WhatsApp não é processo. É sorte.” Apoio: “Solicitações espalhadas, mensagens perdidas e prazos difíceis de acompanhar não precisam fazer parte da rotina. O Full Hub integra a gestão da operação entre a Full e seus clientes, reunindo demandas, aprovações, responsáveis e prazos em um só lugar.” Assinatura “O jeito Full de atender.”

**Solução:** “Menos ruído. Mais fluxo.” / “Quando todos acompanham as mesmas informações, o trabalho ganha organização, transparência e fluidez.” Solicitações — “Toda demanda começa no lugar certo.” / “Solicitações organizadas em um ambiente compartilhado, facilitando o acompanhamento e a comunicação entre a equipe e o cliente.” Responsáveis e prazos — “Cada tarefa tem seu responsável. E seu momento de acontecer.” / “Demandas com responsáveis, prazos e status definidos, trazendo mais clareza para a rotina de trabalho.” Aprovações — “Aprovar também faz parte do processo.” / “Etapas de aprovação integradas ao fluxo de trabalho, reduzindo a dispersão de informações e facilitando o acompanhamento das entregas.” Visibilidade — “Todo mundo conectado ao andamento do projeto.” / “Equipe e cliente acompanham as informações relevantes da operação em um ambiente organizado.”

**CTA:** “Menos mensagens perdidas. Mais negócios em movimento.” / “Uma operação conectada cria espaço para o que realmente importa: fazer os projetos avançarem.” / “Conversar sobre o Full Hub”.

## 6.5 FULL DASH

**Hero:** “Sua mídia gera números. Mas gera negócios?” / “Impressões, cliques e alcance ajudam a acompanhar campanhas. Mas o que realmente importa é entender como o investimento contribui para os objetivos do negócio. O Full Dash conecta gestão de mídia, metas e otimização contínua para orientar decisões mais inteligentes.” Assinatura “Verba vai para o que gera resultado.”

**Solução:** “Mais clareza para decidir. Mais inteligência para investir.” Gestão — “Investimento com direção.” / “Acompanhamos campanhas e investimentos de mídia de forma integrada, conectando a operação aos objetivos definidos para cada negócio.” Otimização — “Resultado não se acompanha apenas no fim do mês.” / “Analisamos a performance no dia a dia, identificando oportunidades de ajuste e direcionando esforços para melhorar os resultados.” Negócio — “Além dos indicadores de mídia.” / “Dashboards e relatórios conectam metas, investimentos e resultados para apoiar decisões com uma visão mais próxima do negócio.”

**CTA:** “Não basta investir mais. É preciso investir melhor.” / “Transforme o acompanhamento de mídia em decisões mais claras e investimentos mais inteligentes.” / “Conversar sobre o Full Dash”.

## 6.6 FULL LEADS

**Hero:** “Atrair é só o começo. O negócio é converter.” / “Gerar interesse é importante. Transformá-lo em oportunidade comercial é o que aproxima marketing e vendas. O Full Leads conecta atração, captura e nutrição ao funil comercial para desenvolver oportunidades mais preparadas para a atuação do time de vendas.” Assinatura “Oportunidade pronta para fechar.”

**Solução:** “Cada conexão pode ser uma oportunidade.” / “Da descoberta da marca à aproximação comercial, diferentes pontos de contato podem trabalhar juntos para transformar interesse em oportunidades.” Atrair — “Tudo começa despertando o interesse certo.” / “Estratégias de atração aproximam o negócio de pessoas com potencial de se tornarem clientes.” Capturar — “Interesse precisa virar conexão.” / “A captura transforma interações em contatos que podem ser desenvolvidos ao longo da jornada comercial.” Nutrir — “Relacionamentos precisam evoluir.” / “A nutrição mantém o contato ativo e desenvolve oportunidades por meio de comunicações relevantes.” Vendas — “Oportunidades certas. No momento certo.” / “A integração com o funil comercial aproxima marketing e vendas, ajudando o time comercial a trabalhar oportunidades mais qualificadas.”

**CTA:** “Não basta gerar contatos. É preciso gerar oportunidades.” / “Conecte suas estratégias de atração ao processo comercial e aproxime sua equipe de vendas das oportunidades certas.” / “Conversar sobre o Full Leads”.

## 6.7 FULLMIX

**Hero:** “Você sabe onde investe. Mas sabe o que realmente vende?” / “Cada canal de marketing desempenha um papel na construção dos resultados. Entender essa contribuição é essencial para tomar decisões mais inteligentes. O FullMix utiliza Marketing Mix Modeling para analisar quanto os canais online e offline contribuem para as vendas e orientar a composição do mix e a distribuição do orçamento.” Assinatura “Saiba o que cada canal vende.”

**Solução:** “Cada canal tem seu papel. Descubra qual.” / “Mais do que acompanhar investimentos isolados, é preciso compreender como os diferentes canais contribuem para os resultados do negócio.” Mensurar — “Entenda a contribuição de cada canal.” / “A análise estatística de dados históricos ajuda a estimar como diferentes investimentos em marketing contribuem para as vendas.” Comparar — “Enxergue o mix como um todo.” / “Compreenda a participação dos canais online e offline e identifique oportunidades de melhoria na composição dos investimentos.” Orientar — “Decida com mais inteligência.” / “Utilize os resultados das análises para apoiar decisões sobre alocação de orçamento e planejamento de marketing.” Simulador: “Explore diferentes distribuições de investimento e entenda por que conhecer a participação de cada canal é importante para tomar decisões.” Aviso obrigatório: “Esta experiência é ilustrativa. As proporções exibidas não representam previsões de vendas ou recomendações reais de investimento.”

**CTA:** “Seu próximo investimento merece mais inteligência.” / “Entenda a contribuição dos canais e transforme informação em decisões mais estratégicas para o seu negócio.” / “Conversar sobre o FullMix”.

## 6.8 FULLMATCH

**Hero:** “A primeira compra não precisa ser a última.” / “Cada compra revela interesses, preferências e oportunidades de relacionamento. O FullMatch analisa o comportamento de compra para desenvolver ofertas hiperpersonalizadas, estimular a recorrência, aumentar o ticket médio e reativar clientes que deixaram de comprar.” Assinatura “A oferta certa para quem já comprou.”

**Solução:** “Cada cliente é único. A próxima oferta também deveria ser.” / “Relacionamentos mais relevantes começam quando as marcas compreendem o comportamento de seus consumidores e utilizam esse conhecimento para criar novas oportunidades.” Recorrente — “Quem já compra pode encontrar novos motivos para voltar.” / “O histórico de compras ajuda a identificar interesses e oportunidades para estimular novas compras.” Alto valor — “Mais relevância em cada nova oferta.” / “O comportamento de compra pode orientar recomendações complementares e oportunidades para aumentar o valor das compras.” Inativo — “Uma conexão pode ser retomada.” / “Identificar consumidores que deixaram de comprar permite desenvolver ações de reativação mais relevantes.” Complemento “Não se trata de oferecer a mesma promoção para todo mundo. Trata-se de compreender diferentes comportamentos para criar ofertas mais relevantes.”

**CTA:** “Quem já comprou pode ter muito mais para viver com sua marca.” / “Transforme informações sobre comportamento de compra em oportunidades de recorrência, reativação e crescimento.” / “Conversar sobre o FullMatch”.

## 6.9 CASES

**Listagem hero:** “Ideias se conectam. Resultados aparecem.” / “Conheça projetos em que estratégia, criatividade e inteligência se conectam para transformar desafios em oportunidades.” Chamada “Explore nossos projetos”. Filtros “Todos”, “Varejo”, “Imobiliário”, “Feiras e Eventos”. Botões “Ver projeto”, “Carregar mais projetos”. Vazio “Ainda não há projetos publicados nesta categoria no momento.” CTA “O próximo case pode ser o seu.” / “Toda transformação começa com um desafio. Qual é o próximo que vamos enfrentar juntos?” / “Vamos conversar”.

**Template de case:** Hero `[Nome do projeto]`, `[Categoria]`, `[Headline conceitual]`, `[Resumo]`; seção “O desafio” `[Texto real]`; “A ideia” `[Texto real]`; “Nossa atuação” `[Texto real]`; galeria “Uma ideia. Diferentes pontos de conexão.” + legenda opcional; resultados “Quando estratégia e criatividade se conectam, o impacto aparece.” + `[Métricas reais, se disponíveis]`; “Continue explorando nossas conexões.” / “Conhecer próximo projeto”. **Não publicar colchetes.**

## 6.10 INSIGHTS FULL

**Hero:** “Ideias provocam. Conexões transformam.” / “Perspectivas sobre marcas, comportamento, criatividade, tecnologia e negócios. Conteúdos para ampliar repertórios e descobrir novas possibilidades.” Chamada “Explore novas perspectivas”.

**Destaque:** “Em destaque”, “Outras perspectivas”, “Ler artigo”. Categorias “Todos”, “Estratégia”, “Branding”, “Tecnologia”, “Comportamento”, “Negócios”. Grid: “O que movimenta suas ideias?” / “Diferentes olhares sobre os assuntos que transformam a maneira como marcas e pessoas se conectam.” CTAs “Continuar lendo”, “Carregar mais artigos”. Vazio “Novas perspectivas estão a caminho. Explore outros conteúdos enquanto isso.”

**CTA:** “O próximo grande insight pode surgir de uma conversa.” / “Compartilhe seus desafios e descubra novas possibilidades para o seu negócio.” / “Vamos conversar”.

**Artigo:** campos categoria, título, subtítulo, autor, data, leitura estimada, capa, corpo rico, blocos de citação, relacionados. Fecho “Continue explorando novas perspectivas.” / “Outras ideias e análises para ampliar seu olhar sobre marcas, comunicação e negócios.” / “Ver mais insights”. Ação “Compartilhar artigo”.

## 6.11 FULL NA MÍDIA

**Hero:** “Boas histórias ganham o mundo.” / “Notícias, entrevistas e publicações que ampliam as conversas sobre a Full Connect Key, nossas ideias e nossa atuação no mercado.” Chamada “Explore nossas publicações”.

**Arquivo:** “O que estão falando sobre a Full.” / “Nossa presença em diferentes veículos, conversas e espaços de comunicação.” Ações “Ler publicação original”, “Outras publicações”, “Nossa presença na mídia”, “Acessar matéria”, “Ver mais publicações”. Filtros opcionais “Todas”, “Entrevistas”, “Reportagens”, “Artigos”. Vazio “Não há publicações disponíveis nesta categoria no momento.”

**CTA:** “Tem uma boa história para contar com a gente?” / “Para entrevistas, pautas, parcerias editoriais e oportunidades de comunicação, entre em contato com a Full.” / “Fale com a Full”.

## 6.12 CONTATO

**Hero:** “Toda grande ideia começa com uma conexão.” / “Conte para a gente o que movimenta o seu negócio. Vamos descobrir juntos quais possibilidades podemos criar.” / “Vamos nos conectar?”.

**Etapa 1:** “O que te trouxe até aqui?” / “Escolha o assunto que melhor representa o início da nossa conversa.” Opções: “Quero desenvolver um projeto.” — “Estratégia, comunicação, marketing ou soluções Full.”; “Sou da imprensa.” — “Entrevistas, pautas e oportunidades editoriais.”; “Quero propor uma parceria.” — “Novas ideias e possibilidades de colaboração.”; “Outro assunto.” — “Vamos descobrir como podemos ajudar.” Botão “Continuar”.

**Etapa 2:** “Agora, conta um pouco mais.” / “Queremos entender sua ideia e saber como podemos entrar em contato.” Campos “Seu nome” (placeholder “Como podemos te chamar?”), “Seu e-mail” (“voce@empresa.com.br”), “Sua empresa (opcional)” (“Nome da empresa”), “Conte sua ideia” (“Qual é o seu desafio ou objetivo?”). Botões “Voltar”, “Enviar mensagem”. Consentimento: “Ao enviar, você concorda com o tratamento dos dados informados para que a Full Connect Key possa responder à sua solicitação, conforme nossa Política de Privacidade.” **Revisão jurídica obrigatória antes de publicar**.

**Estados:** sucesso “Sua conexão com a Full começou.” / “Recebemos sua mensagem. Nossa equipe entrará em contato pelos dados informados.” / “Voltar para a Home”. Erros “Preencha este campo para continuar.”, “Confira se o endereço de e-mail está correto.”, “Não foi possível enviar sua mensagem agora. Tente novamente em instantes.”; carregamento “Estamos enviando sua mensagem...”.

## 6.13 NAVEGAÇÃO, RODAPÉ, ESTADOS GLOBAIS

Menu: “S&M”, “Ecossistema Full”, “Cases”, “Insights Full”, “Full na Mídia”; CTA “Vamos conversar”. Submenu “Visão geral”, “Full Hub”, “Full Dash”, “Full Leads”, “FullMix”, “FullMatch”. Acessibilidade: “Abrir menu”, “Fechar menu”, “Voltar ao topo”, “Próximo projeto”, “Projeto anterior”, “Próximo artigo”, “Artigo anterior”, “Ver todas as publicações”.

Rodapé: “O que se conecta, se multiplica.” / “Estratégia, criatividade, tecnologia, marketing e vendas conectados para transformar oportunidades em resultados.” / links das páginas e plataformas / `[PENDENTE: e-mail, telefone, redes, endereço opcional]` / “© [ano dinâmico] Full Connect Key. Todos os direitos reservados.” / “Política de Privacidade”, “Política de Cookies”, “Vamos conversar”.

404: “Parece que essa conexão se perdeu.” / “A página que você procura não está disponível. Mas ainda existem muitas possibilidades para explorar.” / “Voltar para a Home”. Erro temporário: “Essa conexão precisa de um instante.” / “Encontramos uma dificuldade para carregar o conteúdo. Tente novamente.” / “Tentar novamente”.

Cookies: “Sua experiência também importa.” / “Utilizamos cookies necessários para o funcionamento do site e, com sua autorização, outros cookies para analisar a navegação e aprimorar sua experiência. Saiba mais em nossa Política de Cookies.” Ações “Aceitar todos”, “Recusar opcionais”, “Personalizar preferências”; categorias “Cookies necessários”, “Cookies de análise”, “Cookies de marketing”; “Salvar preferências”. **Somente ativar cookies opcionais após consentimento e revisão de implementação/política**.

## 6.14 SEO — títulos e descrições iniciais

- Home: **Full Connect Key | Estratégia, Criatividade e Resultados** — “Conectamos estratégia, criatividade, tecnologia, marketing e vendas para transformar desafios em oportunidades e resultados.”
- S&M: **Método S&M | Full Connect Key** — “Conheça o método S&M da Full Connect Key, que integra conhecimento, estratégia, marketing e vendas para aprimorar conexões e resultados.”
- Ecossistema: **Ecossistema Full | Soluções Conectadas para Negócios** — “Conheça as plataformas Full Hub, Full Dash, Full Leads, FullMix e FullMatch e descubra soluções para diferentes desafios do seu negócio.”
- Hub: **Full Hub | Gestão Integrada de Demandas e Aprovações** — “Organize solicitações, responsáveis, prazos e aprovações em uma gestão integrada entre a Full e seus clientes.”
- Dash: **Full Dash | Gestão e Otimização de Mídia** — “Gestão de mídia, acompanhamento de metas e otimização contínua para conectar investimentos a resultados de negócio.”
- Leads: **Full Leads | Geração de Leads e Oportunidades Comerciais** — “Conecte atração, captura e nutrição ao funil comercial para desenvolver oportunidades mais qualificadas.”
- Mix: **FullMix | Marketing Mix Modeling** — “Entenda a contribuição dos canais online e offline para as vendas e tome decisões mais inteligentes sobre investimentos em marketing.”
- Match: **FullMatch | Personalização e Fidelização de Clientes** — “Utilize o comportamento de compra para criar ofertas hiperpersonalizadas, estimular recorrência e reativar clientes.”
- Cases: **Cases | Projetos Full Connect Key** — “Conheça projetos que conectam estratégia, criatividade e inteligência para transformar desafios em oportunidades.”
- Insights: **Insights Full | Estratégia, Marcas e Negócios** — “Explore ideias e perspectivas sobre branding, comportamento, criatividade, tecnologia, marketing e negócios.”
- Imprensa: **Full na Mídia | Notícias e Publicações** — “Confira entrevistas, notícias e publicações sobre a Full Connect Key e sua atuação no mercado.”
- Contato: **Contato | Full Connect Key** — “Entre em contato com a Full Connect Key e descubra como conectar novas possibilidades ao seu negócio.”


---

# 7. LACUNAS REAIS E ENTREGA

**Conteúdo a obter (registrar todos em `CONTENT_GAPS.md` com prioridade):**
- **P0 (impede lançamento):** logos oficiais da Full e das 5 plataformas (SVG); textos legais aprovados (privacidade, cookies, consentimento do formulário); destinatário(s) do formulário e credenciais de envio de e-mail; domínio, hospedagem e credenciais de produção; confirmação da tipografia oficial.
- **P1 (seções ficam ocultas sem eles):** KVs e imagens de cases com autorização; logos de clientes autorizados para o marquee; nomes, cargos, bios e fotos dos três sócios; equipe com fotos e frases “Conecta X a Y.”; depoimentos autorizados; imagens dos setores.
- **P2 (enriquecem):** métricas institucionais e por case verificadas; artigos reais; matérias de imprensa reais com links; canais oficiais de contato e redes; imagens/vídeos das páginas de produto.

**Não bloqueie o desenvolvimento por ausência desses dados:** estruture CMS, UI e estados vazios, registre lacunas e prossiga. Não use marcas inventadas nem publique textos `[PENDENTE]`. Durante o desenvolvimento, conteúdo de teste para validar layouts deve ficar **apenas** em fixtures de teste (Playwright/Vitest), nunca no seed nem no banco usado para revisão.

**Relatório final exigido (em `docs/SPRINT_STATUS.md` e na resposta final):**
1. Tabela das 14 sprints: status, funcionalidades entregues, arquivos principais, testes e resultados.
2. Como rodar localmente (passo a passo) e URLs locais/preview disponíveis.
3. Pendências por prioridade (P0/P1/P2) com onde cada uma aparece no site.
4. Instruções resumidas para editar textos, imagens, cases, artigos e matérias no painel (link para `EDITOR_GUIDE.md`).
5. Backup, restauração e rollback.
6. Decisões que precisam de aprovação antes do lançamento (hospedagem, e-mail, textos legais, banner de cookies, retenção de dados, tipografia, deploy).

**Comando de início:** “Execute a Sprint 00 agora e siga automaticamente, em ordem, até a Sprint 13, observando a Definition of Done (2.5), os pontos de parada (2.4) e as regras de autorização deste documento. Ao fim de cada sprint, atualize `docs/SPRINT_STATUS.md` com o ponto de retomada. Não peça novos prompts entre as sprints.”
