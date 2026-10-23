import type { Field, GroupField } from 'payload'

export const linkableCollections = ['pages', 'platforms', 'cases', 'articles'] as const

type LinkOptions = {
  name?: string
  label?: string
  /** Quando false, o rótulo não é obrigatório (ex.: links opcionais). */
  requireLabel?: boolean
  description?: string
}

/**
 * Link reutilizável: aponta para um documento do CMS (o endereço acompanha
 * mudanças de slug) ou para uma URL livre.
 */
export const linkGroup = ({
  name = 'link',
  label = 'Link',
  requireLabel = false,
  description,
}: LinkOptions = {}): GroupField => ({
  name,
  label,
  type: 'group',
  admin: { description },
  fields: [
    {
      type: 'row',
      fields: [
        {
          name: 'label',
          label: 'Texto do link',
          type: 'text',
          required: requireLabel,
          maxLength: 60,
          admin: { width: '50%' },
        },
        {
          name: 'type',
          label: 'Destino',
          type: 'radio',
          defaultValue: 'reference',
          options: [
            { label: 'Página do site', value: 'reference' },
            { label: 'URL', value: 'custom' },
          ],
          admin: { layout: 'horizontal', width: '50%' },
        },
      ],
    },
    {
      name: 'reference',
      label: 'Página de destino',
      type: 'relationship',
      relationTo: [...linkableCollections],
      admin: { condition: (_, sibling) => sibling?.type !== 'custom' },
    },
    {
      name: 'url',
      label: 'URL',
      type: 'text',
      admin: {
        condition: (_, sibling) => sibling?.type === 'custom',
        description: 'Endereço interno começando com "/" (ex.: /contato?assunto=imprensa) ou externo com https://.',
      },
      validate: (value: unknown, { siblingData }: { siblingData: { type?: string } }) => {
        if (siblingData?.type !== 'custom') return true
        if (typeof value !== 'string' || !value) return 'Informe a URL.'
        if (value.startsWith('/') || /^https?:\/\//.test(value) || value.startsWith('mailto:') || value.startsWith('tel:'))
          return true
        return 'Use um caminho interno (/...) ou uma URL completa (https://...).'
      },
    },
  ],
})

export const ctaField = (name = 'cta', label = 'Botão (CTA)'): Field =>
  linkGroup({ name, label, description: 'Deixe o texto vazio para ocultar o botão.' })
