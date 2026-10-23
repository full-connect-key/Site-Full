import type { CollectionConfig } from 'payload'

import { staffOnly } from '../access'
import { revalidateCollection, revalidateCollectionDelete } from '../hooks/revalidate'

const pathPattern = /^\/[^\s]*$/

export const Redirects: CollectionConfig = {
  slug: 'redirects',
  labels: { singular: 'Redirecionamento', plural: 'Redirecionamentos' },
  admin: {
    group: 'Configurações',
    useAsTitle: 'from',
    defaultColumns: ['from', 'to', 'type', 'automatic'],
    description: 'Criados automaticamente quando um slug muda. Também podem ser criados à mão (ex.: endereços do site antigo).',
  },
  access: { read: staffOnly, create: staffOnly, update: staffOnly, delete: staffOnly },
  hooks: { afterChange: [revalidateCollection], afterDelete: [revalidateCollectionDelete] },
  fields: [
    {
      name: 'from',
      label: 'De (caminho antigo)',
      type: 'text',
      required: true,
      unique: true,
      index: true,
      admin: { description: 'Ex.: /servicos/branding' },
      validate: (v: unknown) =>
        typeof v === 'string' && pathPattern.test(v) ? true : 'Use um caminho começando com "/".',
    },
    {
      name: 'to',
      label: 'Para',
      type: 'text',
      required: true,
      admin: { description: 'Caminho interno (/cases) ou URL completa.' },
      validate: (v: unknown) =>
        typeof v === 'string' && (pathPattern.test(v) || /^https:\/\//.test(v))
          ? true
          : 'Use um caminho interno ou uma URL https://.',
    },
    {
      name: 'type',
      label: 'Tipo',
      type: 'select',
      defaultValue: '301',
      options: [
        { label: 'Permanente (301/308)', value: '301' },
        { label: 'Temporário (302/307)', value: '302' },
      ],
    },
    {
      name: 'automatic',
      label: 'Criado automaticamente',
      type: 'checkbox',
      defaultValue: false,
      admin: { readOnly: true, position: 'sidebar' },
    },
  ],
}
