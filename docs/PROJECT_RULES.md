# Regras do projeto

Resumo operacional: as regras completas estão em `docs/BRIEFING.md` (v1.1) e `docs/BRIEFING_ECOSSISTEMA.md` (adendo).

## Gerais
- CMS-first: nenhum texto editorial no código (`npm run check:strings`).
- Nunca inventar dados (métricas, depoimentos, pessoas, clientes, imprensa). Sem conteúdo real, a seção some.
- `[PENDENTE]` e `[VALIDAR]` nunca aparecem no site (há teste automatizado).
- Cores só pelos tokens. Acessibilidade WCAG 2.2 AA e `prefers-reduced-motion` com versão estática equivalente.
- Não publicar em produção, não contratar serviços e não configurar SMTP real sem autorização.

## Ecossistema (adendo)
- **Ordem e verbos:** FullMix (Planejar), FullDash (Otimizar), FullLeads (Converter), FullMatch (Fidelizar), FullHub (Organizar), com o FullHub como base operacional. A ordem é editável no CMS.
- **O negócio do cliente fica no centro do Ecossistema.** S&M é método, não plataforma.
- **Plataformas independentes:** nunca sugerir que precisam ser contratadas juntas. A nota de independência aparece no Ecossistema e no bloco Conexões.
- **Tom:** clareza, personalidade, benefício antes da funcionalidade, autoridade sem promessa, conexão. Na página da plataforma, nenhum parágrafo passa de cerca de 60 palavras.
- **Cuidados com promessas:**
  - FullMix: estimativa estatística, nunca medição causal;
  - FullDash: a IA auxilia, sem garantir ausência de sobras;
  - FullLeads: não prometer venda;
  - FullMatch: LGPD, consentimento e preferências;
  - FullHub: só recursos reais ou previstos.
- **Claims:** funcionalidades não confirmadas ficam com status *A validar* e nunca vão para produção (`docs/CLAIMS_REGISTER.md`, `php scripts/fck.php claims`).
- **Visuais:** conceituais e sempre identificados como ilustrativos, sem números, eixos com valores ou percentuais. O aviso do simulador do FullMix é obrigatório e fica sempre visível.
- **Motion:** a narrativa se forma com a rolagem; o hover só acrescenta. Clique, toque e teclado são opcionais; movimento reduzido mostra o estado final.
