import type { CollectionConfig } from 'payload'

import { anyone, staffOnly } from '../access'
import { orderField, seedKeyField, slugField, text, textarea } from '../fields/common'
import { revalidateCollection, revalidateCollectionDelete } from '../hooks/revalidate'

export const Sectors: CollectionConfig = {
  slug: 'sectors',
  labels: { singular: 'Setor', plural: 'Setores' },
  admin: {
    group: 'Home',
    useAsTitle: 'name',
    defaultColumns: ['name', 'order'],
    description: 'Mercados atendidos. Também usados como filtro de cases.',
  },
  defaultSort: 'order',
  access: { read: anyone, create: staffOnly, update: staffOnly, delete: staffOnly },
  hooks: { afterChange: [revalidateCollection], afterDelete: [revalidateCollectionDelete] },
  fields: [
    text('name', 'Nome', 40, { required: true }),
    slugField('Usado no filtro de cases (ex.: ?setor=varejo).'),
    text('title', 'Frase de destaque', 120),
    textarea('text', 'Texto', 400),
    { name: 'image', label: 'Imagem', type: 'upload', relationTo: 'media' },
    orderField,
    seedKeyField,
  ],
}
