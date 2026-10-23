import type { CollectionConfig } from 'payload'

import { anyone, staffOnly } from '../access'
import { orderField, text, textarea } from '../fields/common'
import { revalidateCollection, revalidateCollectionDelete } from '../hooks/revalidate'

const hooks = { afterChange: [revalidateCollection], afterDelete: [revalidateCollectionDelete] }
const staffWrite = { create: staffOnly, update: staffOnly, delete: staffOnly }

export const Partners: CollectionConfig = {
  slug: 'partners',
  labels: { singular: 'Sócio', plural: 'Sócios' },
  admin: {
    group: 'Pessoas',
    useAsTitle: 'name',
    defaultColumns: ['name', 'role', 'order'],
    description: 'A seção "Os Sócios" aparece na Home somente com os três sócios cadastrados.',
  },
  defaultSort: 'order',
  access: { read: anyone, ...staffWrite },
  hooks,
  fields: [
    text('name', 'Nome', 80, { required: true }),
    text('role', 'Cargo', 80),
    textarea('bio', 'Bio', 700),
    { name: 'photo', label: 'Retrato (vertical)', type: 'upload', relationTo: 'media', required: true },
    orderField,
  ],
}

export const TeamMembers: CollectionConfig = {
  slug: 'teamMembers',
  labels: { singular: 'Pessoa do time', plural: 'Time' },
  admin: { group: 'Pessoas', useAsTitle: 'name', defaultColumns: ['name', 'role', 'order'] },
  defaultSort: 'order',
  access: { read: anyone, ...staffWrite },
  hooks,
  fields: [
    text('name', 'Nome', 80, { required: true }),
    text('role', 'Cargo', 80),
    { name: 'photo', label: 'Foto', type: 'upload', relationTo: 'media', required: true },
    text('connects', 'Frase "Conecta X a Y."', 100, {
      admin: { description: 'Ex.: Conecta dados a decisões. Até 100 caracteres.' },
    }),
    orderField,
  ],
}

export const Testimonials: CollectionConfig = {
  slug: 'testimonials',
  labels: { singular: 'Depoimento', plural: 'Depoimentos' },
  admin: {
    group: 'Pessoas',
    useAsTitle: 'name',
    defaultColumns: ['name', 'company', 'authorized', 'order'],
    description: 'Somente depoimentos reais com autorização registrada aparecem no site.',
  },
  defaultSort: 'order',
  access: {
    read: ({ req }) => (req.user ? true : { authorized: { equals: true } }),
    ...staffWrite,
  },
  hooks,
  fields: [
    textarea('quote', 'Depoimento', 600, { required: true }),
    text('name', 'Nome', 80, { required: true }),
    text('role', 'Cargo', 80),
    text('company', 'Empresa', 80),
    { name: 'photo', label: 'Foto (opcional)', type: 'upload', relationTo: 'media' },
    {
      name: 'authorized',
      label: 'Autorização registrada',
      type: 'checkbox',
      defaultValue: false,
      admin: { position: 'sidebar' },
    },
    text('authorizationNote', 'Registro da autorização (interno)', 200, {
      access: { read: ({ req }) => Boolean(req.user) },
    }),
    orderField,
  ],
}

export const InstitutionalMetrics: CollectionConfig = {
  slug: 'institutionalMetrics',
  labels: { singular: 'Métrica institucional', plural: 'Métricas institucionais' },
  admin: {
    group: 'Cases',
    useAsTitle: 'label',
    defaultColumns: ['value', 'label', 'validated', 'order'],
    description: 'Apenas números verificados. Sem métricas validadas, a faixa de indicadores não aparece.',
  },
  defaultSort: 'order',
  access: {
    read: ({ req }) => (req.user ? true : { validated: { equals: true } }),
    ...staffWrite,
  },
  hooks,
  fields: [
    text('value', 'Valor', 20, { required: true }),
    text('label', 'Rótulo', 80, { required: true }),
    text('source', 'Fonte / validação', 200, { required: true }),
    {
      name: 'validated',
      label: 'Número validado',
      type: 'checkbox',
      defaultValue: false,
      admin: { position: 'sidebar' },
    },
    orderField,
  ],
}
