import type { CollectionConfig } from 'payload'

import { publishedOrStaff, staffOnly } from '../access'
import { orderField, seedKeyField, slugField, text, textarea } from '../fields/common'
import { ctaField } from '../fields/link'
import { seoGroup } from '../fields/seo'
import { productKeys } from '../fields/theme'
import { redirectOnSlugChange } from '../hooks/redirectOnSlugChange'
import { revalidateCollection, revalidateCollectionDelete } from '../hooks/revalidate'
import { livePreview, previewUrl } from '../preview'

export const Platforms: CollectionConfig = {
  slug: 'platforms',
  labels: { singular: 'Plataforma', plural: 'Plataformas' },
  admin: {
    group: 'Ecossistema',
    useAsTitle: 'name',
    defaultColumns: ['name', 'productKey', 'order', '_status'],
    description: 'Full Hub, Full Dash, Full Leads, FullMix e FullMatch. S&M é método, não plataforma.',
    livePreview: livePreview('platforms'),
    preview: (doc) => previewUrl('platforms', doc as { id: string; slug?: string }),
  },
  defaultSort: 'order',
  access: { read: publishedOrStaff, create: staffOnly, update: staffOnly, delete: staffOnly },
  versions: { drafts: { autosave: { interval: 2000 } }, maxPerDoc: 50 },
  hooks: {
    beforeChange: [redirectOnSlugChange('platforms')],
    afterChange: [revalidateCollection],
    afterDelete: [revalidateCollectionDelete],
  },
  fields: [
    text('name', 'Nome', 30, { required: true }),
    slugField(),
    {
      name: 'productKey',
      label: 'Produto (define cor oficial e interação)',
      type: 'select',
      required: true,
      unique: true,
      options: [...productKeys],
      admin: { position: 'sidebar', description: 'A cor oficial vem dos tokens da marca e não é editável.' },
    },
    orderField,
    {
      type: 'tabs',
      tabs: [
        {
          label: 'Resumo',
          fields: [
            {
              name: 'logo',
              label: 'Logo oficial (SVG)',
              type: 'upload',
              relationTo: 'media',
              admin: { description: 'Sem logo oficial, o site exibe o nome em texto (wordmark neutro).' },
            },
            text('signature', 'Assinatura', 80),
            textarea('summary', 'Resumo (Home)', 200),
            text('ecosystemHeadline', 'Frase no Ecossistema', 120),
            textarea('ecosystemText', 'Texto no Ecossistema', 400),
            {
              name: 'ecosystemMedia',
              label: 'Imagem no Ecossistema (opcional)',
              type: 'upload',
              relationTo: 'media',
            },
          ],
        },
        {
          label: 'Hero',
          fields: [
            text('heroHeadline', 'Título', 120, { required: true }),
            textarea('heroLead', 'Texto de apoio', 700),
            { name: 'heroMedia', label: 'Imagem ou vídeo (opcional)', type: 'upload', relationTo: 'media' },
          ],
        },
        {
          label: 'Solução e interação',
          fields: [
            text('solutionTitle', 'Título', 120),
            textarea('solutionIntro', 'Introdução', 400),
            {
              name: 'solutionItems',
              label: 'Itens da solução',
              type: 'array',
              maxRows: 6,
              labels: { singular: 'Item', plural: 'Itens' },
              admin: {
                description:
                  'Cada item vira uma etapa da interação (ex.: Solicitações, Responsáveis e prazos...). A ordem aqui é a ordem no site.',
              },
              fields: [
                text('label', 'Nome curto', 40, { required: true }),
                text('headline', 'Frase de destaque', 160, { required: true }),
                textarea('text', 'Texto', 500),
              ],
            },
            textarea('solutionComplement', 'Texto complementar (opcional)', 400),
            {
              name: 'interaction',
              label: 'Ajustes da interação',
              type: 'group',
              admin: { description: 'Somente parâmetros seguros. O comportamento é definido no código.' },
              fields: [
                text('illustrativeNote', 'Aviso de conteúdo ilustrativo', 300, {
                  admin: {
                    description:
                      'Obrigatório no FullMix (aparece junto ao simulador). No Full Dash, rotula os gráficos.',
                  },
                }),
                textarea('simulatorIntro', 'Introdução do simulador (FullMix)', 300),
                text('simulatorTotalLabel', 'Rótulo do total (FullMix)', 40),
                text('simulatorResetLabel', 'Botão "restaurar" (FullMix)', 40),
                {
                  name: 'channels',
                  label: 'Canais do simulador (FullMix)',
                  type: 'array',
                  maxRows: 8,
                  admin: { description: 'Nomes de canais para a experiência ilustrativa. Sem números de vendas.' },
                  fields: [
                    text('name', 'Canal', 40, { required: true }),
                    {
                      name: 'kind',
                      label: 'Tipo',
                      type: 'select',
                      defaultValue: 'online',
                      options: [
                        { label: 'Online', value: 'online' },
                        { label: 'Offline', value: 'offline' },
                      ],
                    },
                    { name: 'share', label: 'Participação inicial (%)', type: 'number', min: 0, max: 100, defaultValue: 20 },
                  ],
                },
                {
                  name: 'profileFields',
                  label: 'Rótulos dos perfis (FullMatch)',
                  type: 'group',
                  fields: [
                    text('offerLabel', 'Rótulo "Tipo de oferta"', 40),
                    text('cycleLabel', 'Rótulo "Ciclo de recompra"', 40),
                  ],
                },
                {
                  name: 'profiles',
                  label: 'Detalhes por perfil (FullMatch)',
                  type: 'array',
                  maxRows: 6,
                  admin: { description: 'Um por item da solução, na mesma ordem. Conceitual, sem números.' },
                  fields: [text('offer', 'Tipo de oferta', 120), text('cycle', 'Ciclo de recompra', 120)],
                },
              ],
            },
          ],
        },
        {
          label: 'CTA',
          fields: [
            text('ctaTitle', 'Título', 120),
            textarea('ctaText', 'Texto', 300),
            ctaField('cta', 'Botão'),
          ],
        },
        { label: 'SEO', fields: [seoGroup] },
      ],
    },
    seedKeyField,
  ],
}
