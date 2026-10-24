# Registro de afirmações (claims)

Afirmações de funcionalidade que dependem de confirmação (adendo `docs/BRIEFING_ECOSSISTEMA.md`, E4).

- No painel, cada passo, benefício, nota e conexão tem **Status da afirmação**:
  - *Confirmado*;
  - *A validar*, com a dúvida em "O que precisa ser validado", visível só no painel.
- **Regra de exibição:**
  - em desenvolvimento e staging, itens *A validar* aparecem; no preview autenticado levam o selo interno "A validar";
  - em produção (`env = production`, ou `FCK_CLAIMS_STRICT=1` / `'claims_strict' => true`), são omitidos e o layout se reorganiza sem buracos.
- **Antes do lançamento (P0):** `php scripts/fck.php claims` lista as pendentes e sai com erro enquanto houver alguma. O painel também as mostra em Início › Pendências de conteúdo.
- **Para validar:** edite o item no painel, mude o status para *Confirmado* (ou reescreva/remova o texto) e publique. Atualize a linha abaixo.

| # | Plataforma | Bloco | Texto exato | Dúvida | Responsável sugerido | Status |
|---|---|---|---|---|---|---|
| 1 | FullMix | Conexões (FullLeads) | "Os dados de oportunidades geradas ajudam a enriquecer a leitura da contribuição de cada canal." | Existe integração de dados entre FullLeads e FullMix? | Produto | validar |
| 2 | FullDash | Como funciona · Planejar e aprovar | "Orçamento, plano de mídia e pedidos de inserção (POs) organizados no mesmo lugar." | A plataforma faz gestão de POs? | Produto | validar |
| 3 | FullDash | Como funciona · Acompanhar o ritmo | "Acompanhamento diário do ritmo de investimento (pacing), com apoio de inteligência artificial, para manter a distribuição da verba alinhada ao planejamento." | Há uso de IA no pacing? | Produto | validar |
| 4 | FullDash | Benefícios · Gestão integrada de investimentos | "Orçamento, plano de mídia e POs organizados em um único ambiente." | Gestão de POs na plataforma | Produto | validar |
| 5 | FullDash | Benefícios · Inteligência artificial aplicada | "Apoio no acompanhamento diário do pacing para manter a verba alinhada ao plano." | Uso de IA no pacing | Produto | validar |
| 6 | FullDash | Benefícios · Resultados conectados às metas | "Dashboards que transformam indicadores em informação para decisão." | Frequência de atualização ("tempo real" só se confirmado) | Produto | validar |
| 7 | FullLeads | Como funciona · Nutrir e qualificar | "Comunicação personalizada ao longo da jornada e identificação dos contatos com maior potencial." | Automação da nutrição e critério de qualificação (lead scoring) | Produto | validar |
| 8 | FullLeads | Benefícios · Qualificação de leads | "Identificação dos contatos com maior potencial de conversão." | Critério de qualificação (lead scoring) | Produto | validar |
| 9 | FullLeads | Benefícios · Marketing e vendas integrados | "Oportunidades organizadas em um funil conectado, com visibilidade de cada etapa." | Integração com CRM — quais | Produto | validar |
| 10 | FullMatch | Benefícios · Comunicação multicanal | "Contato pelos canais de preferência de cada consumidor, respeitando o consentimento e as preferências de comunicação." | Canais suportados | Produto | validar |
| 11 | FullMatch | Benefícios · nota | "Dados tratados conforme a LGPD, com uso orientado pelas bases legais aplicáveis e pelas preferências de comunicação dos consumidores." | Revisão jurídica | Jurídico | validar |
| 12 | FullHub | Como funciona · Do lado do cliente | "Você solicita novos projetos, acompanha o andamento das entregas, aprova materiais, consulta cronogramas e acessa os arquivos da sua marca." | Cronogramas e biblioteca de arquivos disponíveis? | Produto | validar |
| 13 | FullHub | Benefícios · Transparência no andamento | "Acompanhamento de cada demanda sem depender de atualizações por mensagens." | Usar "em tempo real" só se confirmado | Produto | validar |
| 14 | FullHub | Benefícios · Autonomia para o cliente | "Acesso a arquivos, materiais da marca, entregas finais e histórico de projetos." | Biblioteca de arquivos e histórico disponíveis? | Produto | validar |
| 15 | FullHub | Benefícios · Histórico centralizado | "Informações e versões registradas para facilitar consultas e reduzir retrabalho." | Versionamento de materiais | Produto | validar |
