import type { CollectionConfig } from 'payload'

import { publishedOrStaff, staffOnly } from '../access'
import { pageBlocks } from '../blocks/pageBlocks'
import { seedKeyField, slugField } from '../fields/common'
import { seoGroup } from '../fields/seo'
import { themeVariantField } from '../fields/theme'
import { redirectOnSlugChange } from '../hooks/redirectOnSlugChange'
import { revalidateCollection, revalidateCollectionDelete } from '../hooks/revalidate'
import { livePreview, previewUrl } from '../preview'
import { pageRoles } from '../../lib/paths'

export const Pages: CollectionConfig = {
  slug: 'pages',
  labels: { singular: 'Página', plural: 'Páginas' },
  admin: {
    group: 'Páginas',
    useAsTitle: 'title',
    defaultColumns: ['title', 'slug', 'role', '_status', 'updatedAt'],
    description: 'S&M, Ecossistema, Cases, Insights, Full na Mídia, Contato e páginas legais.',
    livePreview: livePreview('pages'),
    preview: (doc) => previewUrl('pages', doc as { id: string; slug?: string }),
  },
  access: {
    read: publishedOrStaff,
    create: staffOnly,
    update: staffOnly,
    delete: staffOnly,
  },
  versions: { drafts: { autosave: { interval: 2000 }, schedulePublish: true }, maxPerDoc: 50 },
  hooks: {
    beforeChange: [redirectOnSlugChange('pages')],
    afterChange: [revalidateCollection],
    afterDelete: [revalidateCollectionDelete],
  },
  fields: [
    { name: 'title', label: 'Título interno', type: 'text', required: true, maxLength: 80 },
    slugField(),
    {
      name: 'role',
      label: 'Função da página',
      type: 'select',
      unique: true,
      options: [...pageRoles],
      admin: {
        position: 'sidebar',
        description:
          'Liga a página a uma função do site (ex.: base dos cases). Cada função pode ser usada por uma página só.',
      },
    },
    { ...themeVariantField('dark'), admin: { position: 'sidebar', description: 'Tema base da página.' } },
    {
      name: 'sections',
      label: 'Seções',
      type: 'blocks',
      blocks: pageBlocks,
      admin: { initCollapsed: true },
    },
    seoGroup,
    seedKeyField,
  ],
}
