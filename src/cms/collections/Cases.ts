import type { CollectionConfig } from 'payload'

import { publishedOrStaff, staffOnly } from '../access'
import { orderField, seedKeyField, slugField, text, textarea } from '../fields/common'
import { seoGroup } from '../fields/seo'
import { redirectOnSlugChange } from '../hooks/redirectOnSlugChange'
import { revalidateCollection, revalidateCollectionDelete } from '../hooks/revalidate'
import { livePreview, previewUrl } from '../preview'

export const Cases: CollectionConfig = {
  slug: 'cases',
  labels: { singular: 'Case', plural: 'Cases' },
  admin: {
    group: 'Cases',
    useAsTitle: 'title',
    defaultColumns: ['title', 'client', 'sector', 'featuredPosition', '_status'],
    description: 'Publicar um case cria a página, coloca na listagem, nos filtros e no sitemap.',
    livePreview: livePreview('cases'),
    preview: (doc) => previewUrl('cases', doc as { id: string; slug?: string }),
  },
  defaultSort: 'order',
  access: { read: publishedOrStaff, create: staffOnly, update: staffOnly, delete: staffOnly },
  versions: { drafts: { autosave: { interval: 2000 }, schedulePublish: true }, maxPerDoc: 50 },
  hooks: {
    beforeChange: [redirectOnSlugChange('cases')],
    afterChange: [revalidateCollection],
    afterDelete: [revalidateCollectionDelete],
  },
  fields: [
    text('title', 'Nome do projeto', 100, { required: true }),
    slugField(),
    {
      type: 'row',
      fields: [
        { name: 'client', label: 'Cliente', type: 'relationship', relationTo: 'clients', admin: { width: '50%' } },
        { name: 'sector', label: 'Setor', type: 'relationship', relationTo: 'sectors', admin: { width: '50%' } },
      ],
    },
    text('category', 'Categoria / tipo de projeto', 60, {
      admin: { description: 'Ex.: Branding, Campanha, Lançamento. Até 60 caracteres.' },
    }),
    text('headline', 'Headline conceitual', 160),
    textarea('summary', 'Resumo', 400),
    {
      name: 'kv',
      label: 'Key visual (KV)',
      type: 'upload',
      relationTo: 'media',
      admin: { description: 'Imagem principal, usada na listagem, na Home e no topo do case.' },
    },
    {
      type: 'collapsible',
      label: 'Narrativa',
      fields: [
        textarea('challenge', 'O desafio', 2000),
        textarea('idea', 'A ideia', 2000),
        textarea('approach', 'Nossa atuação', 2000),
      ],
    },
    {
      type: 'collapsible',
      label: 'Galeria',
      fields: [
        text('galleryTitle', 'Título da galeria', 120),
        {
          name: 'gallery',
          label: 'Itens',
          type: 'array',
          maxRows: 30,
          labels: { singular: 'Item', plural: 'Itens' },
          fields: [
            { name: 'media', label: 'Imagem ou vídeo', type: 'upload', relationTo: 'media', required: true },
            text('caption', 'Legenda', 200),
            {
              name: 'size',
              label: 'Tamanho no grid',
              type: 'select',
              defaultValue: 'half',
              options: [
                { label: 'Largura total', value: 'full' },
                { label: 'Metade', value: 'half' },
                { label: 'Um terço', value: 'third' },
                { label: 'Dois terços', value: 'twoThirds' },
              ],
            },
          ],
        },
      ],
    },
    {
      type: 'collapsible',
      label: 'Resultados (opcional — somente dados reais)',
      fields: [
        text('resultsTitle', 'Título dos resultados', 160),
        {
          name: 'results',
          label: 'Indicadores',
          type: 'array',
          maxRows: 6,
          admin: { description: 'Vazio = bloco de resultados oculto. Nunca use números de exemplo.' },
          fields: [
            text('value', 'Valor', 20, { required: true }),
            text('label', 'Rótulo', 80, { required: true }),
            text('source', 'Fonte / observação', 160),
          ],
        },
      ],
    },
    {
      type: 'collapsible',
      label: 'Próximo case',
      fields: [
        text('nextTitle', 'Título da seção', 120),
        text('nextLabel', 'Texto do link', 40),
        {
          name: 'nextCase',
          label: 'Próximo case (manual)',
          type: 'relationship',
          relationTo: 'cases',
          admin: { description: 'Vazio = o próximo da listagem (volta ao primeiro no fim).' },
          filterOptions: ({ id }) => ({ id: { not_equals: id } }),
        },
      ],
    },
    {
      name: 'featuredPosition',
      label: 'Destaque na Home',
      type: 'select',
      options: [
        { label: '1 — grande', value: '1' },
        { label: '2', value: '2' },
        { label: '3', value: '3' },
      ],
      admin: { position: 'sidebar', description: 'Posição no grid de cases da Home.' },
    },
    orderField,
    seoGroup,
    seedKeyField,
  ],
}
