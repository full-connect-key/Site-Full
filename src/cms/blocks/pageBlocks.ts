import type { Block } from 'payload'

import { text, textarea } from '../fields/common'
import { ctaField } from '../fields/link'
import { themeVariantField } from '../fields/theme'
import { contactSubjects } from '../globals/siteSettings'

export const HeroBlock: Block = {
  slug: 'hero',
  labels: { singular: 'Hero', plural: 'Heros' },
  interfaceName: 'HeroBlock',
  fields: [
    text('headline', 'Título principal', 120, { required: true }),
    text('headlineAlt', 'Título alternativo (alterna com o principal)', 120, {
      admin: { description: 'Opcional. Até 120 caracteres. Quando preenchido, os dois títulos se alternam.' },
    }),
    textarea('lead', 'Texto de apoio', 600),
    ctaField(),
    {
      name: 'visual',
      label: 'Fundo interativo',
      type: 'select',
      defaultValue: 'connections',
      options: [
        { label: 'Pontos e linhas (conexões)', value: 'connections' },
        { label: 'Sem fundo interativo', value: 'none' },
      ],
    },
    { name: 'media', label: 'Imagem de fundo (opcional)', type: 'upload', relationTo: 'media' },
    themeVariantField('dark'),
  ],
}

export const MethodNarrativeBlock: Block = {
  slug: 'methodNarrative',
  labels: { singular: 'Narrativa do método', plural: 'Narrativas do método' },
  interfaceName: 'MethodNarrativeBlock',
  fields: [
    text('title', 'Título', 120),
    textarea('intro', 'Introdução', 600),
    {
      name: 'steps',
      label: 'Movimentos',
      type: 'array',
      minRows: 1,
      maxRows: 5,
      labels: { singular: 'Movimento', plural: 'Movimentos' },
      fields: [
        text('name', 'Nome (ex.: Entender)', 30, { required: true }),
        text('headline', 'Frase de destaque', 160, { required: true }),
        textarea('text', 'Texto', 600, { required: true }),
        { name: 'media', label: 'Imagem (opcional)', type: 'upload', relationTo: 'media' },
      ],
    },
  ],
}

export const CtaBlock: Block = {
  slug: 'cta',
  labels: { singular: 'Chamada final (CTA)', plural: 'Chamadas (CTA)' },
  interfaceName: 'CtaBlock',
  fields: [
    text('title', 'Título', 120, { required: true }),
    textarea('text', 'Texto', 400),
    ctaField('cta', 'Botão'),
    {
      name: 'style',
      label: 'Estilo',
      type: 'select',
      defaultValue: 'cinematic',
      options: [
        { label: 'Cinematográfico (escuro com conexões)', value: 'cinematic' },
        { label: 'Simples escuro', value: 'dark' },
        { label: 'Simples claro', value: 'light' },
      ],
    },
  ],
}

export const EcosystemNavigatorBlock: Block = {
  slug: 'ecosystemNavigator',
  labels: { singular: 'Navegador do ecossistema', plural: 'Navegadores do ecossistema' },
  interfaceName: 'EcosystemNavigatorBlock',
  fields: [
    text('title', 'Título', 120),
    textarea('intro', 'Introdução', 600),
    text('centerLabel', 'Rótulo do centro do diagrama', 30, {
      admin: { description: 'Ex.: Seu negócio. Até 30 caracteres.' },
    }),
    text('linkLabel', 'Texto do link para a plataforma', 40),
    {
      name: 'platforms',
      label: 'Plataformas',
      type: 'relationship',
      relationTo: 'platforms',
      hasMany: true,
      admin: { description: 'Vazio = todas as plataformas publicadas, na ordem definida em cada uma.' },
    },
  ],
}

export const CasesListingBlock: Block = {
  slug: 'casesListing',
  labels: { singular: 'Listagem de cases', plural: 'Listagens de cases' },
  interfaceName: 'CasesListingBlock',
  fields: [
    text('title', 'Chamada da listagem', 80),
    text('filterAllLabel', 'Filtro "Todos"', 30),
    text('viewLabel', 'Botão "Ver projeto"', 30),
    text('loadMoreLabel', 'Botão "Carregar mais"', 40),
    textarea('emptyText', 'Texto quando não há cases', 200),
    { name: 'pageSize', label: 'Cases por carregamento', type: 'number', min: 3, max: 24, defaultValue: 9 },
  ],
}

export const InsightsListingBlock: Block = {
  slug: 'insightsListing',
  labels: { singular: 'Listagem de insights', plural: 'Listagens de insights' },
  interfaceName: 'InsightsListingBlock',
  fields: [
    text('featuredLabel', 'Rótulo "Em destaque"', 30),
    text('othersLabel', 'Rótulo "Outras perspectivas"', 40),
    text('readLabel', 'Botão "Ler artigo"', 30),
    text('gridTitle', 'Título do grid', 120),
    textarea('gridText', 'Texto do grid', 300),
    text('filterAllLabel', 'Filtro "Todos"', 30),
    text('continueLabel', 'Link "Continuar lendo"', 30),
    text('loadMoreLabel', 'Botão "Carregar mais"', 40),
    textarea('emptyText', 'Texto quando não há artigos', 200),
    { name: 'pageSize', label: 'Artigos por carregamento', type: 'number', min: 3, max: 24, defaultValue: 9 },
  ],
}

export const PressArchiveBlock: Block = {
  slug: 'pressArchive',
  labels: { singular: 'Arquivo de imprensa', plural: 'Arquivos de imprensa' },
  interfaceName: 'PressArchiveBlock',
  fields: [
    text('title', 'Título', 120),
    textarea('text', 'Texto', 300),
    text('readOriginalLabel', 'Botão "Ler publicação original"', 40),
    text('othersLabel', 'Rótulo "Outras publicações"', 40),
    text('archiveTitle', 'Título do arquivo', 60),
    text('accessLabel', 'Link "Acessar matéria"', 30),
    text('loadMoreLabel', 'Botão "Ver mais publicações"', 40),
    text('filterAllLabel', 'Filtro "Todas"', 30),
    text('filterInterviewLabel', 'Filtro "Entrevistas"', 30),
    text('filterReportLabel', 'Filtro "Reportagens"', 30),
    text('filterArticleLabel', 'Filtro "Artigos"', 30),
    textarea('emptyText', 'Texto quando não há publicações', 200),
    {
      name: 'archiveThreshold',
      label: 'Mínimo de matérias para exibir arquivo e filtros',
      type: 'number',
      min: 1,
      max: 50,
      defaultValue: 6,
    },
  ],
}

export const ContactFormBlock: Block = {
  slug: 'contactForm',
  labels: { singular: 'Formulário de contato', plural: 'Formulários de contato' },
  interfaceName: 'ContactFormBlock',
  fields: [
    {
      type: 'collapsible',
      label: 'Etapa 1 — assunto',
      fields: [
        text('step1Title', 'Título', 120),
        textarea('step1Text', 'Texto', 300),
        {
          name: 'subjects',
          label: 'Opções de assunto',
          type: 'array',
          maxRows: 4,
          fields: [
            {
              name: 'value',
              label: 'Assunto',
              type: 'select',
              required: true,
              options: [...contactSubjects],
            },
            text('title', 'Título da opção', 80, { required: true }),
            text('description', 'Descrição', 120),
          ],
        },
        text('continueLabel', 'Botão "Continuar"', 30),
      ],
    },
    {
      type: 'collapsible',
      label: 'Etapa 2 — dados',
      fields: [
        text('step2Title', 'Título', 120),
        textarea('step2Text', 'Texto', 300),
        text('nameLabel', 'Rótulo — nome', 40),
        text('namePlaceholder', 'Exemplo — nome', 60),
        text('emailLabel', 'Rótulo — e-mail', 40),
        text('emailPlaceholder', 'Exemplo — e-mail', 60),
        text('companyLabel', 'Rótulo — empresa', 40),
        text('companyPlaceholder', 'Exemplo — empresa', 60),
        text('messageLabel', 'Rótulo — mensagem', 40),
        text('messagePlaceholder', 'Exemplo — mensagem', 80),
        textarea('consentText', 'Texto de consentimento', 400, {
          admin: { description: 'Revisão jurídica obrigatória antes de publicar. Até 400 caracteres.' },
        }),
        text('consentLinkLabel', 'Texto do link da política dentro do consentimento', 60, {
          admin: { description: 'Trecho exato do texto acima que vira link para a Política de Privacidade.' },
        }),
        text('backLabel', 'Botão "Voltar"', 30),
        text('submitLabel', 'Botão "Enviar"', 30),
      ],
    },
    {
      type: 'collapsible',
      label: 'Sucesso',
      fields: [
        text('successTitle', 'Título', 120),
        textarea('successText', 'Texto', 300),
        text('successCtaLabel', 'Botão', 40),
      ],
    },
  ],
}

export const LegalTextBlock: Block = {
  slug: 'legalText',
  labels: { singular: 'Texto jurídico', plural: 'Textos jurídicos' },
  interfaceName: 'LegalTextBlock',
  fields: [
    {
      name: 'body',
      label: 'Texto',
      type: 'richText',
      required: true,
      admin: { description: 'Publicar somente após aprovação jurídica.' },
    },
    { name: 'updatedOn', label: 'Atualizado em', type: 'date' },
    text('updatedLabel', 'Rótulo "Atualizado em"', 40),
  ],
}

export const pageBlocks: Block[] = [
  HeroBlock,
  MethodNarrativeBlock,
  EcosystemNavigatorBlock,
  CasesListingBlock,
  InsightsListingBlock,
  PressArchiveBlock,
  ContactFormBlock,
  LegalTextBlock,
  CtaBlock,
]
