import type { GlobalConfig } from 'payload'

import { adminFieldOnly, staffOnly } from '../access'
import { text, textarea } from '../fields/common'
import { linkGroup } from '../fields/link'
import { revalidateGlobal } from '../hooks/revalidate'

export const contactSubjects = [
  { label: 'Projeto', value: 'projeto' },
  { label: 'Imprensa', value: 'imprensa' },
  { label: 'Parceria', value: 'parceria' },
  { label: 'Outro assunto', value: 'outro' },
] as const

export type ContactSubject = (typeof contactSubjects)[number]['value']

export const SiteSettingsGlobal: GlobalConfig = {
  slug: 'siteSettings',
  label: 'Configurações do site',
  admin: {
    group: 'Configurações',
    description: 'Nome, logos, menu, rodapé, contatos e SEO padrão.',
  },
  access: { read: () => true, update: staffOnly },
  versions: { max: 50 },
  hooks: { afterChange: [revalidateGlobal] },
  fields: [
    {
      type: 'tabs',
      tabs: [
        {
          label: 'Marca',
          fields: [
            text('siteName', 'Nome do site', 80, { required: true }),
            {
              type: 'row',
              fields: [
                {
                  name: 'logoOnDark',
                  label: 'Logo para fundo escuro (SVG oficial)',
                  type: 'upload',
                  relationTo: 'media',
                  admin: { width: '50%', description: 'Vazio = nome do site em texto (wordmark neutro).' },
                },
                {
                  name: 'logoOnLight',
                  label: 'Logo para fundo claro (SVG oficial)',
                  type: 'upload',
                  relationTo: 'media',
                  admin: { width: '50%' },
                },
              ],
            },
          ],
        },
        {
          label: 'Menu',
          fields: [
            {
              name: 'mainNav',
              label: 'Menu principal',
              type: 'array',
              maxRows: 8,
              labels: { singular: 'Item', plural: 'Itens' },
              admin: { initCollapsed: true },
              fields: [
                linkGroup({ name: 'link', label: 'Link', requireLabel: true }),
                {
                  name: 'children',
                  label: 'Submenu',
                  type: 'array',
                  maxRows: 10,
                  labels: { singular: 'Subitem', plural: 'Subitens' },
                  fields: [linkGroup({ name: 'link', label: 'Link', requireLabel: true })],
                },
              ],
            },
            linkGroup({ name: 'headerCta', label: 'Botão do menu', description: 'Ex.: Vamos conversar.' }),
          ],
        },
        {
          label: 'Rodapé',
          fields: [
            text('footerTagline', 'Assinatura do rodapé', 120),
            textarea('footerText', 'Texto do rodapé', 300),
            {
              name: 'footerGroups',
              label: 'Grupos de links',
              type: 'array',
              maxRows: 6,
              labels: { singular: 'Grupo', plural: 'Grupos' },
              fields: [
                text('title', 'Título do grupo', 60),
                {
                  name: 'links',
                  label: 'Links',
                  type: 'array',
                  maxRows: 12,
                  fields: [linkGroup({ name: 'link', label: 'Link', requireLabel: true })],
                },
              ],
            },
            {
              name: 'legalLinks',
              label: 'Links legais',
              type: 'array',
              maxRows: 4,
              fields: [linkGroup({ name: 'link', label: 'Link', requireLabel: true })],
            },
            linkGroup({ name: 'footerCta', label: 'Botão do rodapé' }),
            text('copyright', 'Texto de direitos autorais', 120, {
              admin: { description: 'Use {ano} para o ano atual. Até 120 caracteres.' },
            }),
          ],
        },
        {
          label: 'Contatos e redes',
          description: 'Preencha apenas com canais oficiais. Campos vazios não aparecem no site.',
          fields: [
            { name: 'email', label: 'E-mail público', type: 'email' },
            text('phone', 'Telefone', 30),
            textarea('address', 'Endereço (opcional)', 200),
            {
              name: 'socials',
              label: 'Redes sociais',
              type: 'array',
              maxRows: 8,
              fields: [
                {
                  name: 'network',
                  label: 'Rede',
                  type: 'select',
                  required: true,
                  options: [
                    { label: 'Instagram', value: 'instagram' },
                    { label: 'LinkedIn', value: 'linkedin' },
                    { label: 'YouTube', value: 'youtube' },
                    { label: 'Facebook', value: 'facebook' },
                    { label: 'TikTok', value: 'tiktok' },
                    { label: 'X', value: 'x' },
                    { label: 'Behance', value: 'behance' },
                  ],
                },
                text('label', 'Nome exibido', 40, { required: true }),
                {
                  name: 'url',
                  label: 'URL',
                  type: 'text',
                  required: true,
                  validate: (v: unknown) =>
                    typeof v === 'string' && /^https:\/\//.test(v) ? true : 'Use uma URL completa com https://.',
                },
              ],
            },
          ],
        },
        {
          label: 'SEO padrão',
          fields: [
            text('defaultTitle', 'Título padrão', 70),
            text('titleTemplate', 'Modelo de título', 40, {
              admin: { description: 'Use %s para o título da página. Ex.: %s | Full Connect Key. Vazio = só o título.' },
            }),
            textarea('defaultDescription', 'Descrição padrão', 170),
            {
              name: 'defaultOgImage',
              label: 'Imagem de compartilhamento padrão',
              type: 'upload',
              relationTo: 'media',
            },
          ],
        },
        {
          label: 'Cookies',
          fields: [
            {
              name: 'cookieBannerMode',
              label: 'Exibição do banner de cookies',
              type: 'select',
              defaultValue: 'auto',
              options: [
                { label: 'Automático — só quando houver ferramentas opcionais aprovadas', value: 'auto' },
                { label: 'Sempre exibir', value: 'always' },
                { label: 'Nunca exibir', value: 'never' },
              ],
              admin: {
                description:
                  'Hoje o site usa apenas o essencial e não grava cookies opcionais. Decisão a validar com o jurídico (docs/DECISIONS.md).',
              },
            },
          ],
        },
        {
          label: 'Formulário de contato',
          description: 'Somente administradores veem e alteram os destinatários.',
          fields: [
            {
              name: 'contactRecipients',
              label: 'Destinatários por assunto',
              type: 'array',
              access: { read: adminFieldOnly, update: adminFieldOnly, create: adminFieldOnly },
              admin: {
                description:
                  'Sem destinatário, as mensagens ficam salvas em "Mensagens de contato" e nenhum e-mail é enviado.',
              },
              fields: [
                {
                  name: 'subject',
                  label: 'Assunto',
                  type: 'select',
                  required: true,
                  options: [{ label: 'Todos os assuntos', value: 'all' }, ...contactSubjects],
                },
                { name: 'email', label: 'E-mail', type: 'email', required: true },
              ],
            },
            {
              name: 'retentionMonths',
              label: 'Retenção das mensagens (meses)',
              type: 'number',
              min: 1,
              max: 60,
              defaultValue: 12,
              access: { read: adminFieldOnly, update: adminFieldOnly, create: adminFieldOnly },
              admin: { description: 'Padrão sugerido: 12 meses. Decisão a validar com o jurídico.' },
            },
          ],
        },
      ],
    },
  ],
}
