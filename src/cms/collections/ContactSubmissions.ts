import type { CollectionConfig } from 'payload'

import { adminOnly } from '../access'
import { contactSubjects } from '../globals/siteSettings'

export const ContactSubmissions: CollectionConfig = {
  slug: 'contactSubmissions',
  labels: { singular: 'Mensagem de contato', plural: 'Mensagens de contato' },
  admin: {
    group: 'Configurações',
    useAsTitle: 'email',
    defaultColumns: ['createdAt', 'subject', 'name', 'email', 'deliveryStatus'],
    description: 'Somente administradores. Dados pessoais — tratar conforme a política de privacidade.',
    hidden: ({ user }) => !(user as { roles?: string[] } | null)?.roles?.includes('admin'),
  },
  // Criação apenas pelo servidor (Server Action com validação). Nunca pela API pública.
  access: { read: adminOnly, create: () => false, update: adminOnly, delete: adminOnly },
  timestamps: true,
  fields: [
    { name: 'subject', label: 'Assunto', type: 'select', required: true, options: [...contactSubjects] },
    { name: 'name', label: 'Nome', type: 'text', required: true, maxLength: 120 },
    { name: 'email', label: 'E-mail', type: 'email', required: true },
    { name: 'company', label: 'Empresa', type: 'text', maxLength: 120 },
    { name: 'message', label: 'Mensagem', type: 'textarea', required: true, maxLength: 4000 },
    { name: 'consent', label: 'Consentimento', type: 'checkbox', required: true },
    { name: 'consentAt', label: 'Data do consentimento', type: 'date', required: true, admin: { date: { pickerAppearance: 'dayAndTime' } } },
    {
      name: 'deliveryStatus',
      label: 'Status do envio',
      type: 'select',
      defaultValue: 'pending',
      options: [
        { label: 'Pendente', value: 'pending' },
        { label: 'Enviado por e-mail', value: 'sent' },
        { label: 'Salvo sem destinatário configurado', value: 'stored' },
        { label: 'Falha no envio', value: 'failed' },
      ],
      admin: { position: 'sidebar' },
    },
    { name: 'deliveryError', label: 'Erro de envio', type: 'text', admin: { readOnly: true, position: 'sidebar' } },
  ],
}
