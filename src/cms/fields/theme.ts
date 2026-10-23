import type { SelectField } from 'payload'

/**
 * Variantes de tema nomeadas. As cores ficam no código (src/styles/tokens.ts);
 * o editor só escolhe a variante, nunca digita hex.
 */
export const themeVariants = [
  { label: 'Escuro', value: 'dark' },
  { label: 'Claro', value: 'light' },
] as const

export const themeVariantField = (defaultValue: 'dark' | 'light' = 'dark'): SelectField => ({
  name: 'themeVariant',
  label: 'Tema da seção',
  type: 'select',
  defaultValue,
  options: [...themeVariants],
  admin: { description: 'Variante visual definida pela marca.' },
})

export const productKeys = [
  { label: 'Full Hub', value: 'hub' },
  { label: 'Full Dash', value: 'dash' },
  { label: 'Full Leads', value: 'leads' },
  { label: 'FullMix', value: 'mix' },
  { label: 'FullMatch', value: 'match' },
] as const

export type ProductKey = (typeof productKeys)[number]['value']
