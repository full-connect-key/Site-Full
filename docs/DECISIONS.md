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
