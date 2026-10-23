import type { CollectionConfig } from 'payload'

import { publishedOrStaff, staffOnly } from '../access'
import { orderField, text, textarea } from '../fields/common'
import { revalidateCollection, revalidateCollectionDelete } from '../hooks/revalidate'

export const pressKinds = [
  { label: 'Entrevista', value: 'interview' },
  { label: 'Reportagem', value: 'report' },
  { label: 'Artigo', value: 'article' },
] as const

export const PressMentions: CollectionConfig = {
  slug: 'pressMentions',
  labels: { singular: 'Matéria', plural: 'Full na Mídia' },
  admin: {
    group: 'Imprensa',
    useAsTitle: 'headline',
    defaultColumns: ['headline', 'outlet', 'publishedAt', 'kind', '_status'],
    description: 'Somente publicações reais, com link para a matéria original.',
  },
  defaultSort: '-publishedAt',
  access: { read: publishedOrStaff, create: staffOnly, update: staffOnly, delete: staffOnly },
  versions: { drafts: true, maxPerDoc: 20 },
  hooks: { afterChange: [revalidateCollection], afterDelete: [revalidateCollectionDelete] },
  fields: [
    text('outlet', 'Veículo', 80, { required: true }),
    {
      name: 'outletLogo',
      label: 'Logo do veículo (somente com autorização)',
      type: 'upload',
      relationTo: 'media',
      admin: { description: 'Sem autorização confirmada na mídia, o site mostra o nome do veículo em texto.' },
    },
    text('headline', 'Manchete original', 200, { required: true }),
    textarea('summary', 'Resumo próprio', 400),
    {
      type: 'row',
      fields: [
        {
          name: 'publishedAt',
          label: 'Data da publicação',
          type: 'date',
          required: true,
          admin: { width: '50%', date: { pickerAppearance: 'dayOnly', displayFormat: 'dd/MM/yyyy' } },
        },
        {
          name: 'kind',
          label: 'Tipo',
          type: 'select',
          required: true,
          defaultValue: 'report',
          options: [...pressKinds],
          admin: { width: '50%' },
        },
      ],
    },
    {
      name: 'url',
      label: 'URL da matéria original',
      type: 'text',
      required: true,
      validate: (v: unknown) => {
        if (typeof v !== 'string') return 'Informe a URL.'
        try {
          const u = new URL(v)
          return u.protocol === 'https:' || u.protocol === 'http:' ? true : 'Use uma URL http(s).'
        } catch {
          return 'URL inválida.'
        }
      },
    },
    { name: 'cover', label: 'Capa (opcional)', type: 'upload', relationTo: 'media' },
    orderField,
  ],
}
