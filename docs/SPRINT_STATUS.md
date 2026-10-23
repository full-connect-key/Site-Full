# Status das sprints

Status: `TODO` / `IN_PROGRESS` / `DONE` / `BLOCKED` / `DONE_WITH_GAPS`

> Stack atual: **PHP 8.1+ + MySQL + painel próprio** (ADR-001). Next.js/Payload e WordPress foram descartados por causa da hospedagem (Hostinger compartilhada, deploy por Git).

## Ponto de retomada

Base PHP + painel + MySQL aprovada; conteúdo limitado a 1080px centralizados (token `--content`). Sprints 08 a 11 concluídas (Sprint 11 com pendências de conteúdo jurídico e SMTP, que dependem do cliente).

Sprint 12 concluída (ver `docs/QA_REPORT.md`).

Próximo passo: **Sprint 13 — QA final e entrega** (segurança e segredos, `npm audit`, backup/restauração testados, checklist de pré-lançamento, guia do editor e relatório final).

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
| 08 Cases | DONE | Listagem: hero, filtros por setor (só setores com case) refletidos na URL (`?setor=`, funcionam sem JS e com voltar/avançar), grid editorial assimétrico em blocos de 7 (7/5 · 4/4/4 · 5/7) sem buracos, cantos retos e gaps pequenos, nome/categoria no hover/foco (visíveis no mobile), reflow suave (FLIP, desligado com movimento reduzido), "Carregar mais" (`page_size`, link `?mostrar=` sem JS), estado vazio, CTA. Página do case: KV full-bleed com parallax leve, cliente · categoria, headline e resumo; O desafio / A ideia / Nossa atuação; galeria mista (imagem/vídeo com poster, sem autoplay) com tamanhos do painel; resultados só se preenchidos; próximo case manual ou cíclico; CTA da página Cases | KVs e imagens reais com autorização (P1) |
| 09 Insights | DONE | Listagem: destaque editorial (artigo grande + manchetes como abas; marcados como destaque primeiro), grid sem repetir o destaque, filtro por categoria na URL (`?categoria=`, também sem JS), carregar mais, estado vazio, CTA. Artigo: categoria (link para o filtro), título, subtítulo, autor e cargo, data, leitura estimada, capa, corpo em texto rico sanitizado (títulos, listas, citações, imagens, vídeos YouTube/Vimeo), compartilhar (LinkedIn, WhatsApp, e-mail, copiar link), relacionados (manuais → mesma categoria → recentes), JSON-LD Article com autor | Artigos reais (P2) |
| 10 Full na Mídia | DONE | Hero; faixa horizontal imersiva (matéria sob o cursor/foco se expande e revela a capa por máscara, manchete acompanha o cursor, setas ← → e botões anterior/próximo; no toque, rolagem com snap); veículo (logo só com direito de uso), manchete original, data, tipo, resumo próprio e link externo `noopener noreferrer` com aviso de nova aba; arquivo com filtros por tipo (`?tipo=`) e "Ver mais" **só acima do limiar do painel**; sem matérias: hero, estado vazio e CTA; CTA para `/contato/?assunto=imprensa` | Matérias reais (P2) |
| 11 Contato/cookies/legais | DONE_WITH_GAPS | Formulário progressivo: etapa 1 assunto (cards de opção, pré-seleção por `?assunto=`), etapa 2 nome, e-mail, empresa opcional, mensagem e consentimento (trecho vira link para a Política de Privacidade quando publicada); foco gerenciado entre etapas e nos erros (`aria-invalid` + `aria-describedby`), validação no cliente espelhando o servidor, estados enviando/erro/sucesso, sucesso **só após confirmação do servidor**; sem JS as duas etapas aparecem e o envio volta com `?contato=sucesso`. Back-end: validação, honeypot, tempo mínimo, token, limite por IP, gravação no painel, SMTP. Banner/central de cookies e página jurídica | Textos jurídicos, destinatários e SMTP aprovados (P0, CONTENT_GAPS) |
| 12 Polimento | DONE | Lighthouse mobile em 6 páginas (Performance 93–98, Acessibilidade 100, Boas práticas 100; SEO 69 só pelo `noindex` de staging); CLS da Home 0,224 → 0 (troca `no-js`→`js` antes da 1ª pintura); cache de JS corrigido (módulos sem versão ficavam 1 ano no navegador → `no-cache` + `?v=<commit>`); favicon e ícone Apple a partir do símbolo oficial; sem overflow de 320 a 1920 px; alvos de toque ≥ 44 px; roteiro manual de leitor de tela; axe em todas as rotas | Medição de campo (Core Web Vitals) só com tráfego real |
| 13 QA e entrega | TODO | `docs/DEPLOYMENT.md` (Hostinger, backup, rollback) | Relatório final |

## Testes executados (23/10/2026)

- Sprint 11: `tests/e2e/contact.spec.js` (etapas, foco, erros acessíveis, e-mail inválido, envio real com sucesso, pré-seleção `?assunto=imprensa` + mensagem no painel, envio sem JS, honeypot não grava). Suíte completa: **106/106** em 1280, 375, 768 e 1440px.
- Sprint 10: `tests/e2e/press.spec.js` (expansão por cursor e foco, setas, links externos, botões no toque, arquivo/filtros acima do limiar e ausentes abaixo). Suíte completa: **101/101** em 1280, 375, 768 e 1440px.
- Sprint 09: `tests/e2e/insights.spec.js` (abas do destaque, grid sem duplicar, filtro na URL e sem JS, artigo com leitura/autor/compartilhar/copiar link/relacionados/JSON-LD) + axe no artigo — 50/50 nas suítes públicas em 1280 + 375px.
- Sprint 08: Playwright **59/59 em 1280 + 375px** (inclui `tests/e2e/cases.spec.js`: filtro na URL, histórico, filtro sem JS, card acessível, página do case, próximo cíclico, case sem resultados sem bloco vazio, publicar → listagem + filtro + rota + sitemap) e axe na página do case.

- `npm run check`: ok. Sem paleta antiga, sem textos fixos nos templates, tokens e contraste ok.
- `php -l` em todos os arquivos: ok.
- Playwright: **31/31 em 1280px** e **25/25 em 375, 768 e 1440px**. Cobrem login e bloqueio, rascunho privado, preview, publicação, restauração de versão, limite de caracteres sem truncar, redirect 301 ao trocar slug, upload (e recusa de PHP disfarçado), permissões do editor, 8 rotas públicas com axe (sem violações sérias ou críticas), ausência de overflow, setores, menu mobile, submenu por teclado, carrossel, movimento reduzido, bloqueio de pastas internas, cabeçalhos de segurança e CSRF.
- Instalação do zero num banco vazio, simulando o primeiro deploy: ok.
