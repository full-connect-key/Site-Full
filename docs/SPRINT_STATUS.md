# Status das sprints

Status: `TODO` / `IN_PROGRESS` / `DONE` / `BLOCKED` / `DONE_WITH_GAPS`

> Stack atual: **PHP 8.1+ + MySQL + painel próprio** (ADR-001). Next.js/Payload e WordPress foram descartados por causa da hospedagem (Hostinger compartilhada, deploy por Git).

## Ponto de retomada

**Todas as 14 sprints foram executadas.** Conteúdo limitado a 1080 px centralizados (token `--content`). O lançamento depende só de conteúdo e decisões do cliente (seção "Decisões" abaixo e `docs/CONTENT_GAPS.md`). Nada foi publicado em produção.

Possíveis próximos passos:
- receber os conteúdos P0 e P1;
- definir o cargo dos sócios;
- escolher a branch publicada pela Hostinger e configurar o webhook;
- conferir a versão no rodapé depois do deploy.

Extra: o rodapé exibe a versão publicada (commit + data do deploy) — `deploy_info()` em `app/lib/helpers.php`.

Para retomar: ler `CLAUDE.md`; `php scripts/fck.php migrate && php scripts/fck.php seed`; `npm run serve`; `npm run test:db && npx playwright test`.

## Tabela

| Sprint | Status | Entregue | Pendências |
|---|---|---|---|
| 00 Fundação | DONE | PHP puro com controlador frontal; `.htaccess` (bloqueio de pastas internas, HTTPS, cache); MySQL via PDO; migrations versionadas; `app/config.php` fora do Git; instalador `/admin/install`; CLI `scripts/fck.php`; docker-compose; governança | — |
| 01 Design system e navegação | DONE | Tokens (`assets/tokens.json` = CSS, teste de contraste WCAG AA); Google Sans; grid editorial; botões, seções, mídia; cabeçalho que esconde/mostra; menu mobile `<dialog>` (trap de foco, Esc); submenu por teclado; rodapé; linha de transição; primitives de movimento com `prefers-reduced-motion`; logos oficiais | `/styleguide` interno não recriado na nova stack |
| 02 CMS e seed | DONE | Painel `/admin`: login (bloqueio por tentativas), papéis admin/editor; rascunho × publicado, preview autenticado, histórico com restauração; campos com limites e ajuda (sem truncar); repetidores, links internos, relações; biblioteca de mídia (upload validado, versões WebP, ponto focal, direito de uso, alt); texto rico (Quill) sanitizado; redirects 301 automáticos e manuais; mensagens de contato e destinatários (admin); usuários; ferramentas; seed idempotente com toda a seção 6 + logos; pendências no painel | Agendamento de publicação não implementado |
| 03 Home 1–6 | DONE | Hero com alternância e canvas de conexões; faixa de logos (só autorizados, pausa); Nossa Essência; Ecossistema com cores oficiais; Setores (`aria-pressed`, desseleção); Cases 1+2 com indicadores só reais | — |
| 04 Home 7–11 | DONE | Sócios (expansão/acordeão); Depoimentos (carrossel acessível); Time (galeria arrastável); Insights (abas acessíveis); CTA final | — |
| 05 S&M | DONE | Hero; narrativa Entender/Conectar/Amplificar fixa com progresso e diagrama; empilhada com movimento reduzido; CTA | — |
| 06 Ecossistema | DONE | Hero (título + alternativo); navegação entre as soluções (abas acessíveis: clique, toque, setas) com diagrama "Seu negócio" no centro, linha e nó na cor oficial da ativa, logo, frase, texto, imagem opcional e link para a página; S&M só no texto; CTA | — |
| 07 Plataformas | DONE | Template comum (hero com logo oficial, assinatura e cor do produto; solução; CTA) e 5 interações: Hub (cards em caos que se organizam em colunas pela rolagem), Dash (seletor com 3 gráficos ilustrativos e selo), Leads (rede de pontos que converge em oportunidades), FullMix (blocos + simulador de distribuição que mantém 100%, com aviso obrigatório sempre visível e sem projeções), FullMatch (seletor de perfis, detalhes de oferta/ciclo quando preenchidos) | Imagens/vídeos de produto (P2) |
| 08 Cases | DONE | Listagem: hero, filtros por setor (só setores com case) refletidos na URL (`?setor=`, funcionam sem JS e com voltar/avançar), grid editorial assimétrico em blocos de 7 (7/5 · 4/4/4 · 5/7) sem buracos, cantos retos e gaps pequenos, nome/categoria no hover/foco (visíveis no mobile), reflow suave (FLIP, desligado com movimento reduzido), "Carregar mais" (`page_size`, link `?mostrar=` sem JS), estado vazio, CTA. Página do case: KV full-bleed com parallax leve, cliente · categoria, headline e resumo; O desafio / A ideia / Nossa atuação; galeria mista (imagem/vídeo com poster, sem autoplay) com tamanhos do painel; resultados só se preenchidos; próximo case manual ou cíclico; CTA da página Cases | KVs e imagens reais com autorização (P1) |
| 09 Insights | DONE | Listagem: destaque editorial (artigo grande + manchetes como abas; marcados como destaque primeiro), grid sem repetir o destaque, filtro por categoria na URL (`?categoria=`, também sem JS), carregar mais, estado vazio, CTA. Artigo: categoria (link para o filtro), título, subtítulo, autor e cargo, data, leitura estimada, capa, corpo em texto rico sanitizado (títulos, listas, citações, imagens, vídeos YouTube/Vimeo), compartilhar (LinkedIn, WhatsApp, e-mail, copiar link), relacionados (manuais → mesma categoria → recentes), JSON-LD Article com autor | Artigos reais (P2) |
| 10 Full na Mídia | DONE | Hero; faixa horizontal imersiva (matéria sob o cursor/foco se expande e revela a capa por máscara, manchete acompanha o cursor, setas ← → e botões anterior/próximo; no toque, rolagem com snap); veículo (logo só com direito de uso), manchete original, data, tipo, resumo próprio e link externo `noopener noreferrer` com aviso de nova aba; arquivo com filtros por tipo (`?tipo=`) e "Ver mais" **só acima do limiar do painel**; sem matérias: hero, estado vazio e CTA; CTA para `/contato/?assunto=imprensa` | Matérias reais (P2) |
| 11 Contato/cookies/legais | DONE_WITH_GAPS | Formulário progressivo: etapa 1 assunto (cards de opção, pré-seleção por `?assunto=`), etapa 2 nome, e-mail, empresa opcional, mensagem e consentimento (trecho vira link para a Política de Privacidade quando publicada); foco gerenciado entre etapas e nos erros (`aria-invalid` + `aria-describedby`), validação no cliente espelhando o servidor, estados enviando/erro/sucesso, sucesso **só após confirmação do servidor**; sem JS as duas etapas aparecem e o envio volta com `?contato=sucesso`. Back-end: validação, honeypot, tempo mínimo, token, limite por IP, gravação no painel, SMTP. Banner/central de cookies e página jurídica | Textos jurídicos, destinatários e SMTP aprovados (P0, CONTENT_GAPS) |
| 12 Polimento | DONE | Lighthouse mobile em 6 páginas (Performance 93–98, Acessibilidade 100, Boas práticas 100; SEO 69 só pelo `noindex` de staging); CLS da Home 0,224 → 0 (troca `no-js`→`js` antes da 1ª pintura); cache de JS corrigido (módulos sem versão ficavam 1 ano no navegador → `no-cache` + `?v=<commit>`); favicon e ícone Apple a partir do símbolo oficial; sem overflow de 320 a 1920 px; alvos de toque ≥ 44 px; roteiro manual de leitor de tela; axe em todas as rotas | Medição de campo (Core Web Vitals) só com tráfego real |
| 13 QA e entrega | DONE | Revisão de segredos (nenhum no repositório nem no histórico; `app/config.php` nunca versionado), `npm audit` sem vulnerabilidades, PHPMailer 6.12.0, headers de segurança e CSRF testados; backup e restauração **testados localmente** (contagens e checksum idênticos, site no ar a partir do banco restaurado); checklist de pré-lançamento ampliado; `EDITOR_GUIDE.md` final; relatório final (abaixo) | Deploy de produção só com autorização |

## Testes executados (23/10/2026)

- Sprints 12–13: `tests/e2e/responsive.spec.js` (320 e 1920 px sem overflow, alvos de toque ≥ 44 px), Lighthouse (ver `docs/QA_REPORT.md`), `npm audit` (0 vulnerabilidades), varredura de segredos no histórico (nada encontrado), backup → restauração → site no ar a partir do banco restaurado. **Suíte completa: 109/109** em 1280, 375, 768 e 1440 px; `npm run check` ok.

- Sprint 11: `tests/e2e/contact.spec.js` (etapas, foco, erros acessíveis, e-mail inválido, envio real com sucesso, pré-seleção `?assunto=imprensa` + mensagem no painel, envio sem JS, honeypot não grava). Suíte completa: **106/106** em 1280, 375, 768 e 1440px.
- Sprint 10: `tests/e2e/press.spec.js` (expansão por cursor e foco, setas, links externos, botões no toque, arquivo/filtros acima do limiar e ausentes abaixo). Suíte completa: **101/101** em 1280, 375, 768 e 1440px.
- Sprint 09: `tests/e2e/insights.spec.js` (abas do destaque, grid sem duplicar, filtro na URL e sem JS, artigo com leitura/autor/compartilhar/copiar link/relacionados/JSON-LD) + axe no artigo — 50/50 nas suítes públicas em 1280 + 375px.
- Sprint 08: Playwright **59/59 em 1280 + 375px** (inclui `tests/e2e/cases.spec.js`: filtro na URL, histórico, filtro sem JS, card acessível, página do case, próximo cíclico, case sem resultados sem bloco vazio, publicar → listagem + filtro + rota + sitemap) e axe na página do case.

- `npm run check`: ok. Sem paleta antiga, sem textos fixos nos templates, tokens e contraste ok.
- `php -l` em todos os arquivos: ok.
- Playwright: **31/31 em 1280px** e **25/25 em 375, 768 e 1440px**. Cobrem login e bloqueio, rascunho privado, preview, publicação, restauração de versão, limite de caracteres sem truncar, redirect 301 ao trocar slug, upload (e recusa de PHP disfarçado), permissões do editor, 8 rotas públicas com axe (sem violações sérias ou críticas), ausência de overflow, setores, menu mobile, submenu por teclado, carrossel, movimento reduzido, bloqueio de pastas internas, cabeçalhos de segurança e CSRF.
- Instalação do zero num banco vazio, simulando o primeiro deploy: ok.

## Arquivos principais

| Área | Arquivos |
|---|---|
| Entrada e rotas | `index.php`, `.htaccess`, `app/bootstrap.php`, `app/lib/router.php` |
| Conteúdo e painel | `app/schema/*.php`, `app/lib/content.php`, `app/lib/fields.php`, `app/admin/` |
| Seed (seção 6) e logos | `app/seed/content.php`, `app/seed/logos/` |
| Páginas | `app/views/pages/{home,sm,ecosystem,platform,cases,case,insights,article,press,contact,legal,404}.php` |
| Seções | `app/views/home/`, `app/views/platform/`, `app/views/insights/card.php`, `app/views/parts/` |
| Estilo e interações | `assets/css/main.css`, `assets/tokens.json`, `assets/js/main.js`, `assets/js/modules/*.js` |
| Formulário | `app/lib/contact.php`, `app/lib/mail.php`, `assets/js/modules/contact-form.js` |
| Testes | `tests/e2e/*.spec.js`, `tests/fixtures/fixtures.php`, `tests/unit/tokens.test.mjs`, `scripts/check-*.mjs` |
| Documentação | `docs/DEPLOYMENT.md`, `docs/EDITOR_GUIDE.md`, `docs/QA_REPORT.md`, `docs/CONTENT_GAPS.md`, `docs/DECISIONS.md` |

## Como rodar localmente

```bash
cp app/config.sample.php app/config.php     # preencha db e secret
php scripts/fck.php migrate && php scripts/fck.php seed
php scripts/fck.php create-admin voce@exemplo.com 'senha-com-12+caracteres'
npm run serve                                # http://localhost:8080  (painel: /admin)
npm run check                                # cores, textos fixos e tokens
npm run test:db && npx playwright test       # banco de teste + e2e (porta 8081)
```
Alternativa com Docker: `docker compose up` (PHP 8.3 + MySQL 8 + Mailpit em http://localhost:8025).
Site no ar para revisão (staging Hostinger): https://lightskyblue-magpie-424044.hostingersite.com/ — atualiza ao clicar em **Implantar** no hPanel (ou por webhook).

## Pendências por prioridade

Lista completa e onde cada item aparece: `docs/CONTENT_GAPS.md`. Resumo:
- **P0, impedem o lançamento:**
  - textos jurídicos (Privacidade, Cookies, consentimento);
  - destinatários do formulário e SMTP;
  - domínio, SSL e `app/config.php` de produção;
  - decisão sobre o banner de cookies.
- **P1, seções ficam ocultas sem eles:**
  - KVs e imagens de cases;
  - logos de clientes autorizados;
  - sócios: cargo, bio e retrato (nomes recebidos);
  - time;
  - depoimentos autorizados;
  - imagens dos setores.
- **P2, enriquecem o site:**
  - métricas verificadas;
  - artigos e matérias reais;
  - canais de contato e redes;
  - mídia das páginas de produto.

## Editar o site

Resumo em `docs/EDITOR_GUIDE.md`:
- textos em Páginas;
- imagens na Biblioteca de mídia, com texto alternativo e ponto focal;
- cases, artigos e matérias em Adicionar › Publicar (entram sozinhos em listagens, filtros e sitemap);
- microtextos em Configurações › Textos de interface.

## Backup, restauração e rollback

Detalhes em `docs/DEPLOYMENT.md`, itens 3 e 4:
- **banco:** backups automáticos da Hostinger + exportação pelo phpMyAdmin ou `mysqldump`, procedimento testado localmente;
- **arquivos enviados:** backup da pasta `uploads/`;
- **conteúdo:** histórico de 50 versões por registro;
- **código:** `git revert` + Implantar.

## Decisões que precisam de aprovação antes do lançamento

1. **Hospedagem e deploy:** Hostinger compartilhada já em uso. Falta definir a branch publicada (`main` ou a atual), o webhook automático e a autorização para o deploy de produção.
2. **E-mail:** qual caixa da Hostinger envia, em `smtp` no `app/config.php`, e quem recebe cada assunto.
3. **Textos legais:** Política de Privacidade, Política de Cookies e texto de consentimento, com revisão jurídica.
4. **Banner de cookies:** hoje o site não usa cookies de terceiros e o banner fica oculto em modo automático (ADR-007). Decidir se haverá analytics ou pixels.
5. **Retenção de dados:** prazo para guardar as mensagens do formulário. Padrão de 12 meses (ADR-008).
6. **Tipografia:** Google Sans confirmada pelo cliente e servida localmente sob a licença OFL (ADR-004).
7. **Conteúdo pendente de confirmação:** cargo dos sócios ("CEO e sócios": os três com o mesmo cargo, ou um CEO e dois sócios).

Confirmado pelo cliente: a assinatura "powered by 4REAL" fica no logo do FullMatch.
