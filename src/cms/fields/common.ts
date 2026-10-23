import type { Field, TextField, TextareaField } from 'payload'

/** Campo de texto com limite e ajuda — o frontend nunca trunca. */
export const text = (
  name: string,
  label: string,
  maxLength: number,
  extra: Partial<TextField> = {},
): TextField =>
  ({
    name,
    label,
    type: 'text',
    maxLength,
    ...extra,
    admin: { description: `Até ${maxLength} caracteres.`, ...(extra.admin ?? {}) },
  }) as TextField

export const textarea = (
  name: string,
  label: string,
  maxLength: number,
  extra: Partial<TextareaField> = {},
): TextareaField =>
  ({
    name,
    label,
    type: 'textarea',
    maxLength,
    ...extra,
    admin: { description: `Até ${maxLength} caracteres.`, ...(extra.admin ?? {}) },
  }) as TextareaField

export const enabledField = (label = 'Exibir esta seção'): Field => ({
  name: 'enabled',
  label,
  type: 'checkbox',
  defaultValue: true,
})

export const orderField: Field = {
  name: 'order',
  label: 'Ordem',
  type: 'number',
  defaultValue: 0,
  admin: { position: 'sidebar', description: 'Menor aparece primeiro.' },
}

export const seedKeyField: Field = {
  name: 'seedKey',
  type: 'text',
  index: true,
  admin: { hidden: true },
}

/** Aceita apenas slugs simples: minúsculas, números e hífens. */
export const slugField = (description = 'Parte final do endereço. Minúsculas, números e hífens.'): TextField => ({
  name: 'slug',
  label: 'Slug (endereço)',
  type: 'text',
  required: true,
  unique: true,
  index: true,
  admin: { position: 'sidebar', description },
  validate: (value: unknown) =>
    typeof value === 'string' && /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(value)
      ? true
      : 'Use apenas letras minúsculas, números e hífens (ex.: meu-case).',
})
