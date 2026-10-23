# Status das sprints

Status: `TODO` / `IN_PROGRESS` / `DONE` / `BLOCKED` / `DONE_WITH_GAPS`

> Stack atual: **PHP 8.1+ + MySQL + painel próprio** (ADR-001). Next.js/Payload e WordPress foram descartados por causa da hospedagem (Hostinger compartilhada, deploy por Git).

## Ponto de retomada

Base PHP + painel + MySQL aprovada; conteúdo limitado a 1200px centralizados (token `--content`). Sprints retomadas.

Próximo passo: **Sprint 08 — Cases** (listagem com filtros na URL e página do case). Depois as Sprints 09 a 13 em ordem.

Extra: rodapé exibe a versão publicada (commit + data do deploy) — `deploy_info()` em `app/lib/helpers.php`.

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
| 08 Cases | TODO | Listagem e página do item (hero + CTA) | Grid, filtros na URL, galeria, resultados, próximo case |
| 09 Insights | TODO | Listagem e página do item (hero + CTA) | Destaque, filtros, artigo completo, compartilhar |
| 10 Full na Mídia | TODO | Hero + CTA | Navegação horizontal imersiva |
| 11 Contato/cookies/legais | IN_PROGRESS | Back-end do formulário (validação, honeypot, token, limite por IP, gravação, SMTP), banner/central de cookies, página jurídica | Formulário progressivo na página Contato |
| 12 Polimento | TODO | Testes de acessibilidade (axe) nas rotas atuais; SEO técnico (meta, OG, canonical, sitemap, robots, JSON-LD) | Lighthouse, auditoria completa |
| 13 QA e entrega | TODO | `docs/DEPLOYMENT.md` (Hostinger, backup, rollback) | Relatório final |

## Testes executados (23/10/2026)

- `npm run check`: ok. Sem paleta antiga, sem textos fixos nos templates, tokens e contraste ok.
- `php -l` em todos os arquivos: ok.
- Playwright: **31/31 em 1280px** e **25/25 em 375, 768 e 1440px**. Cobrem login e bloqueio, rascunho privado, preview, publicação, restauração de versão, limite de caracteres sem truncar, redirect 301 ao trocar slug, upload (e recusa de PHP disfarçado), permissões do editor, 8 rotas públicas com axe (sem violações sérias ou críticas), ausência de overflow, setores, menu mobile, submenu por teclado, carrossel, movimento reduzido, bloqueio de pastas internas, cabeçalhos de segurança e CSRF.
- Instalação do zero num banco vazio, simulando o primeiro deploy: ok.
