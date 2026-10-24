# Modelo de conteúdo

Os campos editáveis ficam em `app/schema/`:
- `templates.php`: páginas, por modelo;
- `types.php`: coleções;
- `globals.php`: configurações e textos de interface.

O painel e a validação são gerados a partir desses arquivos. Esta página resume o modelo das plataformas e do Ecossistema, revisado na Sprint ECO (`docs/BRIEFING_ECOSSISTEMA.md`).

## Plataforma (`type = platform`)

O **nome é o título do registro** (campo único). Nos textos, `{platform}` é trocado pelo nome em todo o site, inclusive no SEO e nos CTAs. A **ordem** é o campo *Ordem* da lista: FullMix 1, FullDash 2, FullLeads 3, FullMatch 4, FullHub 5. O slug é o endereço: `/ecossistema/{slug}`.

| Grupo | Campos | Onde aparece |
|---|---|---|
| Identidade | product (cor/interação), verb, logo, logo_light, home_line, card_title, card_text, connects, eco_image | Home (nome + verbo + linha), card do Ecossistema |
| 1. Hero | headline, lead, cta_label, media, alt_titles[] (só painel) | Hero; a chamada "nome · verbo" é automática |
| 2. Problema | enabled, title, text | Bloco 2 (com visual de rolagem no FullDash, FullMix e FullMatch) |
| 3. Como funciona | enabled, motion, intensity, intro, steps[] {title, text, claim_status, claim_note} | Cena guiada pela rolagem |
| 3a. Ajustes do visual | illustrative_note; FullMix: simulador, canais, rótulos e fluxos; FullDash: seletor, rótulos e palavras; FullMatch: clusters[]; FullHub: columns | Visuais conceituais (sem números) |
| 4. Benefícios | enabled, manifesto, items[] {title, text, claim_status, claim_note}, note, note_claim_status, note_claim_note | Grid de até 6 itens |
| 5. Interface | enabled, items[] {media, caption, is_real} | Só itens com mídia e `is_real` marcado |
| 6. Conexões | enabled, intro, items[] {platform, text, claim_status, claim_note} | Mapa e lista de relações |
| 7. CTA final | title, text, cta_label | Botão → `/contato?assunto=projeto&plataforma={slug}` |
| SEO | title, description, image, noindex | `<head>` |

## Página Ecossistema (`template = ecosystem`)

- **Hero:** título, título alternativo (o subtítulo "Seu negócio no centro…") e texto.
- **Soluções:**
  - title (destaque) e intro (jornada);
  - center_label (centro do diagrama);
  - link_label (aceita `{platform}`) e connects_label;
  - independence (nota de independência) e method_note (S&M como método);
  - platforms (ordem opcional).
- **CTA e SEO.**

## Home › Ecossistema

Título, texto e link. Os itens vêm das plataformas: nome, `verb` e `home_line`, na ordem do campo *Ordem*.

## Globais

- **Marca › Assinatura** (`brand.signature`): usada no hero da Home quando o título alternativo da Home está vazio (D2).
- **Textos de interface › Páginas de plataforma:** rótulos dos blocos, nota de independência, "Pular animação" e o selo "A validar".

## Mensagens de contato

A tabela `submissions` ganhou `platform`, com o nome da plataforma de origem quando o contato vem de `?plataforma={slug}` (migration `002`).
