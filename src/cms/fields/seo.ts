import type { GroupField } from 'payload'

export const seoGroup: GroupField = {
  name: 'seo',
  label: 'SEO',
  type: 'group',
  admin: {
    description: 'Como a página aparece no Google e ao ser compartilhada. Vazio = usa o padrão do site.',
  },
  fields: [
    {
      name: 'title',
      label: 'Título (meta title)',
      type: 'text',
      maxLength: 70,
      admin: { description: 'Até 70 caracteres. Recomendado: 50–60.' },
    },
    {
      name: 'description',
      label: 'Descrição (meta description)',
      type: 'textarea',
      maxLength: 170,
      admin: { description: 'Até 170 caracteres. Recomendado: 120–160.' },
    },
    {
      name: 'image',
      label: 'Imagem de compartilhamento (OG)',
      type: 'upload',
      relationTo: 'media',
      admin: { description: 'Proporção 1200×630. Vazio = imagem padrão do site.' },
    },
    {
      name: 'noindex',
      label: 'Ocultar dos buscadores (noindex)',
      type: 'checkbox',
      defaultValue: false,
    },
  ],
}
