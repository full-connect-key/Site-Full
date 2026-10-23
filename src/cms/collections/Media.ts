import path from 'path'
import { fileURLToPath } from 'url'
import type { CollectionConfig } from 'payload'

import { staffOnly } from '../access'
import { revalidateCollection, revalidateCollectionDelete } from '../hooks/revalidate'

const dirname = path.dirname(fileURLToPath(import.meta.url))

export const Media: CollectionConfig = {
  slug: 'media',
  labels: { singular: 'Mídia', plural: 'Mídias' },
  admin: {
    group: 'Mídia',
    useAsTitle: 'filename',
    defaultColumns: ['filename', 'alt', 'folder', 'rightsConfirmed', 'updatedAt'],
    description:
      'Imagens, vídeos e SVGs. Só use arquivos com direito de uso confirmado. Logos de terceiros apenas com autorização.',
  },
  access: {
    read: () => true,
    create: staffOnly,
    update: staffOnly,
    delete: staffOnly,
  },
  folders: true,
  hooks: {
    afterChange: [revalidateCollection],
    afterDelete: [revalidateCollectionDelete],
  },
  upload: {
    // Disco local em desenvolvimento. Adapter S3 só com conta autorizada (ver docs/DECISIONS.md).
    staticDir: path.resolve(dirname, '../../../media'),
    mimeTypes: ['image/*', 'video/mp4', 'video/webm', 'application/pdf'],
    focalPoint: true,
    crop: true,
    imageSizes: [
      { name: 'thumb', width: 480 },
      { name: 'card', width: 960 },
      { name: 'wide', width: 1600 },
      { name: 'full', width: 2400 },
      { name: 'og', width: 1200, height: 630, position: 'centre' },
    ],
    formatOptions: { format: 'webp', options: { quality: 82 } },
    adminThumbnail: 'thumb',
  },
  fields: [
    {
      name: 'decorative',
      label: 'Imagem decorativa (sem texto alternativo)',
      type: 'checkbox',
      defaultValue: false,
      admin: { description: 'Marque apenas se a imagem não transmite informação.' },
    },
    {
      name: 'alt',
      label: 'Texto alternativo',
      type: 'text',
      maxLength: 200,
      admin: {
        description: 'Descreva o que a imagem mostra para quem usa leitor de tela. Até 200 caracteres.',
      },
      validate: (value: unknown, { siblingData }: { siblingData?: { decorative?: boolean; mimeType?: string } }) => {
        const isImage = !siblingData?.mimeType || siblingData.mimeType.startsWith('image/')
        if (!isImage || siblingData?.decorative) return true
        return typeof value === 'string' && value.trim().length > 0
          ? true
          : 'O texto alternativo é obrigatório para imagens não decorativas.'
      },
    },
    {
      name: 'caption',
      label: 'Legenda',
      type: 'text',
      maxLength: 200,
    },
    {
      type: 'row',
      fields: [
        {
          name: 'credit',
          label: 'Crédito / autoria',
          type: 'text',
          maxLength: 120,
          admin: { width: '50%' },
        },
        {
          name: 'license',
          label: 'Licença / origem',
          type: 'text',
          maxLength: 120,
          admin: { width: '50%' },
        },
      ],
    },
    {
      name: 'rightsConfirmed',
      label: 'Direito de uso confirmado',
      type: 'checkbox',
      defaultValue: false,
      admin: {
        position: 'sidebar',
        description: 'Obrigatório para que a imagem apareça em logos de clientes, imprensa e cases.',
      },
    },
    {
      name: 'poster',
      label: 'Poster (para vídeos)',
      type: 'upload',
      relationTo: 'media',
      admin: {
        condition: (data) => typeof data?.mimeType === 'string' && data.mimeType.startsWith('video/'),
        description: 'Imagem exibida antes do vídeo tocar.',
      },
    },
  ],
}
