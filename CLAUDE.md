# Full Connect Key — instruções para sessões de desenvolvimento

Site em **PHP 8.1+ puro + MySQL**, com painel próprio em `/admin`. Hospedagem: Hostinger compartilhada, deploy via Git (o repositório inteiro é o `public_html`). **Não usar WordPress nem Node.js em produção.**

- Fonte de verdade do escopo: `docs/BRIEFING.md`. A seção 6 não pode ser alterada.
- Onde parou: **`docs/SPRINT_STATUS.md` › Ponto de retomada**.
- Decisões: `docs/DECISIONS.md`. Pendências de conteúdo: `docs/CONTENT_GAPS.md`. Deploy: `docs/DEPLOYMENT.md`.

## Estrutura
- `index.php` → `app/bootstrap.php` → `app/lib/router.php` (site) / `app/admin/admin.php` (painel)
- `app/schema/` — campos editáveis (grupos por modelo de página e por tipo). O painel e a validação saem daqui.
- `app/lib/content.php` — leitura pública, preview, publicação, histórico e links.
- `app/views/` — templates públicos (sem texto editorial fixo). `app/admin/views/` — telas do painel.
- `app/seed/content.php` — textos aprovados (seção 6) e logos oficiais (`app/seed/logos/`).
- `assets/` — CSS (`css/main.css`, tokens), JS (`js/main.js` carrega `js/modules/*` sob demanda), fontes Google Sans (OFL).

## Comandos
```bash
php scripts/fck.php migrate|seed|create-admin email senha|gaps
npm run serve            # http://localhost:8080
npm run check            # check:colors + check:strings + test:unit
npm run test:db && npx playwright test
```

## Regras inegociáveis (resumo)
- CMS-first: todo texto do site vem do painel (`ui('grupo','chave')` para microtextos). `npm run check:strings` precisa passar.
- Nunca inventar dados (métricas, depoimentos, pessoas, clientes, imprensa). Sem conteúdo real, a seção some.
- `[PENDENTE]` nunca vai ao site publicado.
- Cores só pelos tokens (`assets/tokens.json` = `assets/css/main.css`). Paleta antiga proibida (`npm run check:colors`).
- Acessibilidade: teclado, toque, leitor de tela, foco visível e `prefers-reduced-motion` com versão estática equivalente.
- S&M é método, não plataforma. O negócio do cliente é o centro do ecossistema.
- Não publicar em produção, não contratar serviços e não configurar SMTP real sem autorização.
