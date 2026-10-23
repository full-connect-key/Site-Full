import type { RoutePaths } from './paths'
import { hrefFor } from './paths'

type RelationValue = {
  relationTo: 'pages' | 'platforms' | 'cases' | 'articles'
  value: number | string | { slug?: string | null; _status?: string | null }
}

export type CmsLink = {
  label?: string | null
  type?: 'reference' | 'custom' | null
  reference?: RelationValue | null
  url?: string | null
} | null | undefined

export type ResolvedLink = { href: string; label: string; external: boolean }

export const isExternalHref = (href: string) => /^https?:\/\//.test(href)

/**
 * Converte um link do CMS em href. Retorna null quando o destino não existe
 * (ex.: página ainda em rascunho) ou quando não há rótulo — o botão some.
 */
export function resolveLink(link: CmsLink, paths: RoutePaths, opts: { requireLabel?: boolean } = {}): ResolvedLink | null {
  if (!link) return null
  const label = (link.label ?? '').trim()
  if ((opts.requireLabel ?? true) && !label) return null
  let href: string | null = null
  if (link.type === 'custom') {
    href = link.url?.trim() || null
  } else if (link.reference && typeof link.reference.value === 'object' && link.reference.value) {
    href = hrefFor(paths, link.reference.relationTo, link.reference.value)
  }
  if (!href) return null
  return { href, label, external: isExternalHref(href) }
}
