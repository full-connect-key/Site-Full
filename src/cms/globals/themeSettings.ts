import type { GlobalConfig } from 'payload'

import { adminOnly } from '../access'
import { themeVariants } from '../fields/theme'

export const ThemeSettingsGlobal: GlobalConfig = {
  slug: 'themeSettings',
  label: 'Tema (somente leitura)',
  admin: {
    group: 'Configurações',
    description:
      'As cores oficiais da marca e das plataformas ficam no código (src/styles/tokens.ts) e não são editáveis aqui. Esta tela lista as variantes que os editores podem escolher nas seções.',
  },
  access: { read: () => true, update: adminOnly },
  fields: [
    {
      name: 'variants',
      label: 'Variantes disponíveis',
      type: 'select',
      hasMany: true,
      options: [...themeVariants],
      defaultValue: themeVariants.map((v) => v.value),
      admin: { readOnly: true },
    },
  ],
}
