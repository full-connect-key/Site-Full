import type { CollectionConfig } from 'payload'

import { staffOnly } from '../access'
import { orderField, text } from '../fields/common'
import { revalidateCollection, revalidateCollectionDelete } from '../hooks/revalidate'

export const Clients: CollectionConfig = {
  slug: 'clients',
  labels: { singular: 'Cliente', plural: 'Clientes' },
  admin: {
    group: 'Cases',
    useAsTitle: 'name',
    defaultColumns: ['name', 'displayAuthorized', 'order'],
    description: 'O logo só aparece na faixa da Home com autorização de exibição registrada.',
  },
  defaultSort: 'order',
  access: {
    // Público só vê clientes autorizados.
    read: ({ req }) => (req.user ? true : { displayAuthorized: { equals: true } }),
    create: staffOnly,
    update: staffOnly,
    delete: staffOnly,
  },
  hooks: { afterChange: [revalidateCollection], afterDelete: [revalidateCollectionDelete] },
  fields: [
    text('name', 'Nome', 80, { required: true }),
    { name: 'logo', label: 'Logo (SVG ou PNG transparente)', type: 'upload', relationTo: 'media' },
    {
      name: 'displayAuthorized',
      label: 'Autorização de exibição registrada',
      type: 'checkbox',
      defaultValue: false,
      admin: { position: 'sidebar', description: 'Sem isso o logo não aparece no site.' },
    },
    text('authorizationNote', 'Registro da autorização (interno)', 200, {
      admin: { description: 'Quem autorizou, quando e onde está o registro. Não aparece no site.' },
      access: { read: ({ req }) => Boolean(req.user) },
    }),
    { name: 'sector', label: 'Setor', type: 'relationship', relationTo: 'sectors' },
    orderField,
  ],
}
