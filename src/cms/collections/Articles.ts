import {
  BlocksFeature,
  FixedToolbarFeature,
  HeadingFeature,
  lexicalEditor,
} from '@payloadcms/richtext-lexical'
import type { CollectionConfig } from 'payload'

import { anyone, publishedOrStaff, staffOnly } from '../access'
import { MediaBlock, QuoteBlock, VideoEmbedBlock } from '../blocks/richTextBlocks'
import { orderField, seedKeyField, slugField, text, textarea } from '../fields/common'
import { seoGroup } from '../fields/seo'
import { redirectOnSlugChange } from '../hooks/redirectOnSlugChange'
import { revalidateCollection, revalidateCollectionDelete } from '../hooks/revalidate'
import { livePreview, previewUrl } from '../preview'
import { lexicalPlainText, readingMinutes } from '../../lib/readingTime'

export const ArticleCategories: CollectionConfig = {
  slug: 'articleCategories',
  labels: { singular: 'Categoria', plural: 'Categorias' },
  admin: { group: 'Insights', useAsTitle: 'name', defaultColumns: ['name', 'slug', 'order'] },
  defaultSort: 'order',
  access: { read: anyone, create: staffOnly, update: staffOnly, delete: staffOnly },
  hooks: { afterChange: [revalidateCollection], afterDelete: [revalidateCollectionDelete] },
  fields: [text('name', 'Nome', 40, { required: true }), slugField('Usado no filtro (?categoria=...).'), orderField, seedKeyField],
}

export const Authors: CollectionConfig = {
  slug: 'authors',
  labels: { singular: 'Autor', plural: 'Autores' },
  admin: { group: 'Insights', useAsTitle: 'name' },
  access: { read: anyone, create: staffOnly, update: staffOnly, delete: staffOnly },
  hooks: { afterChange: [revalidateCollection], afterDelete: [revalidateCollectionDelete] },
  fields: [
    text('name', 'Nome', 80, { required: true }),
    text('role', 'Cargo', 80),
    { name: 'photo', label: 'Foto', type: 'upload', relationTo: 'media' },
  ],
}

export const Articles: CollectionConfig = {
  slug: 'articles',
  labels: { singular: 'Artigo', plural: 'Artigos' },
  admin: {
    group: 'Insights',
    useAsTitle: 'title',
    defaultColumns: ['title', 'category', 'publishedAt', 'featured', '_status'],
    description: 'Publicar um artigo cria a página, coloca na listagem, nos filtros e no sitemap.',
    livePreview: livePreview('articles'),
    preview: (doc) => previewUrl('articles', doc as { id: string; slug?: string }),
  },
  defaultSort: '-publishedAt',
  access: { read: publishedOrStaff, create: staffOnly, update: staffOnly, delete: staffOnly },
  versions: { drafts: { autosave: { interval: 2000 }, schedulePublish: true }, maxPerDoc: 50 },
  hooks: {
    beforeChange: [
      redirectOnSlugChange('articles'),
      ({ data }) => {
        data.readingTime = readingMinutes(data.title, data.subtitle, lexicalPlainText(data.body))
        if (data._status === 'published' && !data.publishedAt) data.publishedAt = new Date().toISOString()
        return data
      },
    ],
    afterChange: [revalidateCollection],
    afterDelete: [revalidateCollectionDelete],
  },
  fields: [
    text('title', 'Título', 140, { required: true }),
    slugField(),
    text('subtitle', 'Subtítulo', 240),
    textarea('excerpt', 'Resumo (listagens)', 300),
    { name: 'cover', label: 'Capa', type: 'upload', relationTo: 'media' },
    {
      name: 'body',
      label: 'Corpo do artigo',
      type: 'richText',
      editor: lexicalEditor({
        features: ({ defaultFeatures }) => [
          ...defaultFeatures.filter((f) => !['upload', 'relationship', 'heading'].includes(f.key)),
          HeadingFeature({ enabledHeadingSizes: ['h2', 'h3', 'h4'] }),
          FixedToolbarFeature(),
          BlocksFeature({ blocks: [MediaBlock, QuoteBlock, VideoEmbedBlock] }),
        ],
      }),
    },
    {
      name: 'category',
      label: 'Categoria',
      type: 'relationship',
      relationTo: 'articleCategories',
      admin: { position: 'sidebar' },
    },
    { name: 'author', label: 'Autor', type: 'relationship', relationTo: 'authors', admin: { position: 'sidebar' } },
    {
      name: 'publishedAt',
      label: 'Data de publicação',
      type: 'date',
      admin: { position: 'sidebar', date: { pickerAppearance: 'dayOnly', displayFormat: 'dd/MM/yyyy' } },
    },
    {
      name: 'readingTime',
      label: 'Leitura estimada (min)',
      type: 'number',
      admin: { position: 'sidebar', readOnly: true, description: 'Calculada automaticamente ao salvar.' },
    },
    {
      name: 'featured',
      label: 'Destaque',
      type: 'checkbox',
      defaultValue: false,
      admin: { position: 'sidebar', description: 'O destaque mais recente abre a página de Insights e a seção da Home.' },
    },
    {
      name: 'related',
      label: 'Relacionados (manual)',
      type: 'relationship',
      relationTo: 'articles',
      hasMany: true,
      maxRows: 3,
      filterOptions: ({ id }) => ({ id: { not_equals: id } }),
      admin: { description: 'Vazio = artigos mais recentes da mesma categoria.' },
    },
    orderField,
    seoGroup,
    seedKeyField,
  ],
}
