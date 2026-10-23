/** Substitui tokens {chave} em microtextos vindos do CMS. */
export function fill(template: string | null | undefined, values: Record<string, string | number>): string {
  if (!template) return ''
  return template.replace(/\{(\w+)\}/g, (match, key: string) =>
    key in values ? String(values[key]) : match,
  )
}

const dateFormatter = new Intl.DateTimeFormat('pt-BR', { day: '2-digit', month: 'long', year: 'numeric', timeZone: 'UTC' })
const shortDateFormatter = new Intl.DateTimeFormat('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', timeZone: 'UTC' })

export const formatDate = (iso: string | null | undefined) => (iso ? dateFormatter.format(new Date(iso)) : '')
export const formatShortDate = (iso: string | null | undefined) => (iso ? shortDateFormatter.format(new Date(iso)) : '')

/** Verdadeiro se o texto existe e não é uma pendência entre colchetes. */
export const hasText = (value: string | null | undefined): value is string =>
  typeof value === 'string' && value.trim().length > 0 && !/^\s*\[PENDENTE/i.test(value)
