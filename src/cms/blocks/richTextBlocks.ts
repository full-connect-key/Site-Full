import type { Block } from 'payload'

import { text, textarea } from '../fields/common'

export const QuoteBlock: Block = {
  slug: 'quote',
  labels: { singular: 'Citação', plural: 'Citações' },
  interfaceName: 'QuoteBlock',
  fields: [textarea('quote', 'Citação', 600, { required: true }), text('attribution', 'Autoria', 120)],
}

const videoUrl = /^https:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/|player\.vimeo\.com\/video\/|vimeo\.com\/)[\w-]+/

export const VideoEmbedBlock: Block = {
  slug: 'videoEmbed',
  labels: { singular: 'Vídeo (YouTube/Vimeo)', plural: 'Vídeos' },
  interfaceName: 'VideoEmbedBlock',
  fields: [
    {
      name: 'url',
      label: 'URL do vídeo',
      type: 'text',
      required: true,
      validate: (v: unknown) =>
        typeof v === 'string' && videoUrl.test(v) ? true : 'Use um link do YouTube ou Vimeo.',
    },
    text('title', 'Título do vídeo (acessibilidade)', 120, { required: true }),
    text('caption', 'Legenda', 200),
  ],
}

export const MediaBlock: Block = {
  slug: 'mediaBlock',
  labels: { singular: 'Imagem com legenda', plural: 'Imagens' },
  interfaceName: 'MediaBlock',
  fields: [
    { name: 'media', label: 'Imagem', type: 'upload', relationTo: 'media', required: true },
    text('caption', 'Legenda', 200),
    {
      name: 'width',
      label: 'Largura',
      type: 'select',
      defaultValue: 'text',
      options: [
        { label: 'Largura do texto', value: 'text' },
        { label: 'Larga', value: 'wide' },
      ],
    },
  ],
}
