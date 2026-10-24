# Alterações de copy feitas no desenvolvimento

Registro exigido pelo adendo (`docs/BRIEFING_ECOSSISTEMA.md`, E4). Nenhuma afirmação de funcionalidade, número ou integração foi criada.

## Sprint ECO (24/10/2026)

| Onde | Texto | Origem / motivo |
|---|---|---|
| Plataformas › rótulos dos blocos (Textos de interface › Páginas de plataforma) | "Ver como funciona", "Como funciona", "Benefícios e diferenciais", "Interface", "Conexões no ecossistema", "Pular animação", "A validar" | Nomes dos blocos e controles citados no próprio adendo (E2, E2.1 e E4) |
| Plataformas › link de cada conexão | "Explore o {platform}" | Reaproveita o CTA dos cards do Ecossistema (E3.2) |
| Plataformas › bloco Conexões | "Cada solução pode ser contratada de forma independente. A maior força aparece quando elas trabalham conectadas." | Nota de independência do adendo (E3.2), repetida para deixar claro que cada plataforma pode ser contratada sozinha (E2, bloco 6) |
| Ecossistema › card | "Como se conecta" | Rótulo da coluna do adendo (E3.2) |
| FullDash › visual | "planejado", "realizado", "ajuste", "objetivo do negócio"; seletor "Gestão de mídia / Otimização contínua / Resultados de negócio" | Rótulos conceituais do adendo (E2.1); seletor aprovado na v1.1 |
| FullMix › visual | "vendas", "o que aconteceria sem campanhas", "contribuição estimada"; fluxos "online, offline, sazonalidade, preço, promoções, mercado" | Rótulos conceituais do adendo (E2.1) |
| FullMatch › perfis | "Recorrente / Alto valor / Inativo" e as frases de cada perfil | Textos aprovados na v1.1 (seção 6.7) |
| FullHub › quadro | Colunas "Solicitações / Responsáveis e prazos / Aprovações / Visibilidade" | Itens aprovados na v1.1 (seção 6.4) |
| Home › hero | Título alternativo vazio → usa a assinatura global "Vire a chave dos seus resultados. Ative o modo Full." | D2 |

## Conflitos de migração

A migração mantém qualquer texto editado no painel e lista o campo em Pendências de conteúdo ("Edição do painel preservada…"). No banco de revisão local e no banco de teste não houve conflito. Os conflitos do site no ar aparecem no painel depois do primeiro login após o deploy.

## Sócios (24/10/2026)

| Onde | Texto | Origem / motivo |
|---|---|---|
| Biblioteca de mídia › texto alternativo dos retratos | "Retrato de Rita Frare", "Retrato de Emerson Aguiar", "Retrato de Felipe Pelá" | Descrição mínima para leitores de tela; editável na Biblioteca de mídia |

Cargos e bios são exatamente os textos enviados pelo cliente.

## Sprint de conteúdo da Home (24/10/2026)

Textos enviados pelo cliente, que substituem os da v1.1 (seção 6) nos pontos abaixo. O hero não foi alterado. O site no ar recebe as trocas uma única vez (`home-copy-2026-10`), seguindo a mesma regra da migração do Ecossistema: edições feitas no painel são preservadas.

| Onde | Antes | Agora |
|---|---|---|
| Home › Método (Nossa Essência) › introdução | "Acreditamos que grandes resultados não acontecem de maneira isolada…" | "Não acreditamos em marketing e vendas trabalhando em caminhos separados. / Na Full, estratégia, criatividade, tecnologia e inteligência comercial…" (dois parágrafos) |
| Home › Método › pilares Conecta, Organiza, Multiplica | Textos v1.1 | Novos textos dos três pilares |
| Home › Ecossistema › título e texto | "Quem se conecta, evolui." | "Tecnologia própria. Possibilidades reais." + nova descrição |
| Plataformas › Identidade › linha da Home (as 5) | Linhas v1.1 | "Descubra onde investir com inteligência estratégica." etc. |
| Setores › texto (Varejo, Imobiliário, Feiras e Eventos) | Textos v1.1 | Novos textos |
| Home › Sócios › título e texto | "Visões diferentes. Um propósito conectado." | "Experiências diferentes. Uma visão de futuro." + texto em dois parágrafos |
| Home › CTA final › texto e botão | "Seu próximo desafio…" / "Vamos conversar" | "Toda transformação começa com uma boa conversa…" / "Vamos conversar?" (o botão continua levando a Contato; o botão do cabeçalho continua "Vamos conversar") |

O que ficou de fora, como manda a sprint:
- **Título da faixa de logos** ("Marcas que fazem parte da nossa história."): não entrou, porque a faixa não tem espaço de texto previsto (o briefing define o marquee sem título). Para incluir, é preciso adicionar um campo de título à seção.
- **CTA "Conheça nosso método"**: a seção Método da Home não tem CTA, então nada foi criado.
- As setas (→) dos botões já são desenhadas pelo próprio botão, por isso não fazem parte do texto.

Ajuste técnico: o texto de apoio das seções passa a respeitar quebras de linha digitadas no painel, como os demais campos de texto.

## FullHub (24/10/2026)

| Onde | Antes | Agora |
|---|---|---|
| FullHub › 2. Problema › título | "Aprovação por WhatsApp não é processo. É sorte." | "Sua operação não precisa de mais mensagens. Precisa de conexão." |

Pedido do cliente. No site no ar, a troca acontece uma única vez (`hub-problem-2026-10`) e preserva edições feitas no painel.

## Time (24/10/2026)

| Pessoa | Cargo | Frase |
|---|---|---|
| Gabriela Oliveira | Gerente de Projetos | "Conecta estratégia a execução." (ponto final acrescentado, no padrão "Conecta X a Y.") |
| Victor Fávaro | Diretor de Arte | "Conecta conceitos a soluções criativas." |

As fotos foram convertidas do RAW (IMG_9927.CR2 e IMG_9980_1.CR2) com a mesma luz, o mesmo branco de parede e o mesmo enquadramento dos sócios (`app/seed/people/`). O seed publica cada pessoa do time uma única vez (`team:<slug>`). A seção usa a grade de retratos do ADR-018.

## Hero da Home e faixa de logos (24/10/2026)

| Onde | Antes | Agora |
|---|---|---|
| Home › Hero › título principal | "O que se conecta, se multiplica." | "O que se conecta, multiplica." |
| Home › Faixa de logos | 20 clientes | 19 clientes: Brentwood saiu a pedido do cliente |

Os dois pedidos são do cliente. A assinatura do rodapé e o título do CTA do Ecossistema continuam com "O que se conecta, se multiplica.", porque o pedido foi só para o hero. No site no ar, as duas mudanças acontecem uma única vez (`home-headline-2026-10` e `client-brentwood-off-2026-10`). A Brentwood fica despublicada, não apagada, e pode voltar pelo painel.

Correção técnica junto: o nome da ação gravada no histórico de revisões é limitado a 20 caracteres, e a migração agora corta rótulos maiores. Sem isso, a atualização automática pararia com erro.
