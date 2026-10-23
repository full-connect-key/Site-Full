import type { CollectionConfig } from 'payload'

import { adminFieldOnly, adminOnly, isAdminUser, isStaffUser } from '../access'

export const Users: CollectionConfig = {
  slug: 'users',
  labels: { singular: 'Usuário', plural: 'Usuários' },
  admin: {
    useAsTitle: 'email',
    group: 'Configurações',
    defaultColumns: ['name', 'email', 'roles'],
    description: 'Pessoas com acesso ao painel. Apenas administradores gerenciam usuários.',
  },
  auth: {
    tokenExpiration: 60 * 60 * 8,
    maxLoginAttempts: 5,
    lockTime: 15 * 60 * 1000,
  },
  access: {
    // Painel: qualquer membro da equipe pode entrar.
    admin: ({ req }) => isStaffUser(req.user as never),
    create: adminOnly,
    delete: adminOnly,
    // Admin vê todos; editor vê apenas o próprio cadastro.
    read: ({ req }) => {
      if (isAdminUser(req.user as never)) return true
      if (req.user) return { id: { equals: req.user.id } }
      return false
    },
    update: ({ req }) => {
      if (isAdminUser(req.user as never)) return true
      if (req.user) return { id: { equals: req.user.id } }
      return false
    },
  },
  fields: [
    {
      name: 'name',
      label: 'Nome',
      type: 'text',
      maxLength: 120,
    },
    {
      name: 'roles',
      label: 'Papéis',
      type: 'select',
      hasMany: true,
      required: true,
      defaultValue: ['editor'],
      saveToJWT: true,
      access: { create: adminFieldOnly, update: adminFieldOnly },
      options: [
        { label: 'Administrador (acesso total)', value: 'admin' },
        { label: 'Editor (conteúdo e mídia)', value: 'editor' },
      ],
      admin: {
        description:
          'Administrador: tudo. Editor: conteúdo e mídia, sem usuários, configurações sensíveis ou mensagens de contato.',
      },
    },
  ],
}
