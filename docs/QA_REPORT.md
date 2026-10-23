# Relatório de QA

Ambiente de medição: servidor embutido do PHP (`php -S`, **sem compressão gzip**) + MySQL local, banco de teste com fixtures fictícias (imagens geradas, nunca publicadas). Data: 23/10/2026.

## 1. Lighthouse (mobile, throttling simulado de 4G lento)

| Página | Performance | Acessibilidade | Boas práticas | SEO* | LCP | CLS | TBT |
|---|---|---|---|---|---|---|---|
| Home `/` | 93 | 100 | 100 | 69 | 2,9 s | 0 | 0 ms |
| Produto `/ecossistema/fullmix` | 96 | 100 | 100 | 69 | 2,6 s | 0,002 | 0 ms |
| Case `/cases/case-teste-1` | 96 | 100 | 100 | 69 | 2,6 s | 0 | 0 ms |
| Artigo `/insights/artigo-teste-1` | 96 | 100 | 100 | 69 | 2,5 s | 0 | 0 ms |
| S&M `/sm` | 97 | 100 | 100 | 69 | 2,4 s | 0,02 | 0 ms |
| Ecossistema `/ecossistema` | 98 | 100 | 100 | 69 | 2,3 s | 0 | 0 ms |

\* SEO 69 é **esperado** fora de produção: o único item reprovado é `is-crawlable`, porque o staging sai com `noindex` de propósito (`env` ≠ `production`). Em produção o item passa.

**Corrigido nesta rodada**
- **CLS da Home 0,224 → 0.** A classe `no-js` só saía quando o JS (adiado) rodava, então o primeiro quadro mostrava o layout sem JS e depois "pulava". Agora um script inline no `<head>` troca `no-js` por `js` antes da primeira pintura, e o título alternado do hero já nasce no layout final.
- **Cache de JavaScript.** Os módulos (`assets/js/modules/*`) eram importados sem versão e o `.htaccess` mandava guardar JS por 1 ano (`immutable`). Depois de um deploy, quem já tinha visitado o site continuaria com JS antigo. Agora o JS revalida a cada visita (`no-cache`, resposta 304) e os imports levam `?v=<commit>`.

**LCP**
- O elemento de LCP é o título do hero (texto).
- Em tempo real: TTFB de 30 ms e atraso de renderização de 150 ms. Os 2,5–2,9 s vêm da simulação de rede lenta sobre CSS sem compressão (76 KB). Com o gzip do Apache (`mod_deflate`, já no `.htaccess`), o CSS cai para cerca de 15 KB.
- As metas de campo (p75: LCP ≤ 2,5 s, INP ≤ 200 ms, CLS ≤ 0,1) só podem ser confirmadas com tráfego real, por exemplo no relatório Core Web Vitals do Search Console depois do lançamento.

**Já atendido**
- Fontes com `font-display: swap`, subset latino e preload da principal.
- Imagens responsivas (`srcset` + versões WebP), lazy load fora da primeira dobra e `fetchpriority="high"` no KV.
- Vídeos com poster e `preload="none"`.
- JS de interação carregado sob demanda por `data-module`.
- Canvas de conexões pausado fora da viewport e com a aba oculta.

## 2. Acessibilidade (WCAG 2.2 AA)

Automático:
- axe em todas as rotas públicas (Home, S&M, Ecossistema, 5 plataformas, Cases, case, Insights, artigo, Full na Mídia, Contato) em 1280, 375, 768 e 1440 px, sem violações sérias ou críticas.
- Lighthouse Acessibilidade 100 nas 6 páginas medidas.

Coberto por testes:
- skip link;
- menu mobile com trap de foco e Esc;
- submenu por teclado;
- abas com setas;
- carrossel com pausa;
- movimento reduzido com versão estática;
- formulário com foco gerenciado e erros por `aria-invalid` + `aria-describedby`;
- alvos de toque ≥ 44 px em 375 px (links dentro de texto corrido são exceção permitida pelo critério 2.5.8).

### Roteiro manual de leitor de tela (NVDA + Firefox / VoiceOver + Safari)
1. Abrir a Home e pressionar Tab: o primeiro foco é "Pular para o conteúdo"; Enter leva ao `main`.
2. Navegar por regiões (D/NVDA ou rotor/VO): cabeçalho, navegação principal, principal, rodapé.
3. Navegar por títulos (H): um único H1 por página, hierarquia sem saltos.
4. Menu: o botão do Ecossistema anuncia "recolhido/expandido". No celular, o menu abre como diálogo e Esc fecha devolvendo o foco.
5. Ecossistema e Full Dash: abas anunciam "aba, selecionada, 1 de 5"; as setas trocam.
6. Cases: os filtros anunciam o estado atual (`aria-current`) e a contagem de resultados é lida (região `aria-live`).
7. Contato: escolher assunto (rádios agrupados com legenda) → Continuar → o foco vai ao título da etapa 2. Enviar vazio: o foco vai ao primeiro campo inválido e o erro é lido. O sucesso é anunciado ao receber foco.
8. Full na Mídia: os links externos anunciam "(abre em nova aba)".
9. Ativar "reduzir movimento" no sistema e recarregar: o hero mostra os dois títulos empilhados, S&M aparece empilhada e o marquee fica parado.

## 3. Responsivo

- Sem overflow horizontal em 320 e 1920 px em 11 rotas (`tests/e2e/responsive.spec.js`) e em 375, 768, 1280 e 1440 px em todas as rotas.
- Corrigido: em 320 px a navegação Entender/Conectar/Amplificar da página S&M estourava 8 px; agora quebra linha.
- Interações de hover têm equivalente em toque e teclado: a faixa da imprensa responde a foco e botões e tem snap no toque; os cards de case mostram as informações sempre visíveis no mobile.

## 4. Auditoria editorial

- `npm run check:strings`: nenhum texto fixo em `app/views` (blocos `<script>` ignorados, por serem código).
- Microtextos criados no desenvolvimento estão marcados com `(*)` em `app/seed/content.php` e listados em CONTENT_GAPS para revisão.

## 5. Suíte de testes

`npm run check` ok. Playwright: **109/109** (desktop-1280, mobile-375, tablet-768, desktop-1440).
