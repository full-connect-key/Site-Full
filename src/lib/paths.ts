import type { Payload } from 'payload'

/**
 * Papéis de página: ligam uma página editável (slug livre) a uma função fixa
 * do site. Os endereços dos itens (plataformas, cases, artigos) derivam do
 * slug da página com o papel correspondente.
 */
export const pageRoles = [
  { label: 'Método S&M', value: 'sm' },
  { label: 'Ecossistema Full (base das plataformas)', value: 'ecosystem' },
  { label: 'Cases (base dos cases)', value: 'cases' },
  { label: 'Insights (base dos artigos)', value: 'insights' },
  { label: 'Full na Mídia', value: 'press' },
  { label: 'Contato', value: 'contact' },
  { label: 'Política de Privacidade', value: 'privacy' },
  { label: 'Política de Cookies', value: 'cookies' },
] as const

export type PageRole = (typeof pageRoles)[number]['value']

/** Slugs técnicos usados apenas se a página com o papel ainda não existir. */
const fallbackSlugs: Record<PageRole, string> = {
  sm: 'sm',
  ecosystem: 'ecossistema',
  cases: 'cases',
  insights: 'insights',
  press: 'full-na-midia',
  contact: 'contato',
  privacy: 'politica-de-privacidade',
  cookies: 'politica-de-cookies',
}

export type RoutePaths = Record<PageRole, string>

export const fallbackRoutePaths = (): RoutePaths =>
  Object.fromEntries(Object.entries(fallbackSlugs).map(([k, v]) => [k, `/${v}`])) as RoutePaths

export async function loadRoutePaths(payload: Payload, draft = false): Promise<RoutePaths> {
  const paths = fallbackRoutePaths()
  const res = await payload.find({
    collection: 'pages',
    where: { role: { exists: true } },
    limit: 50,
    depth: 0,
    draft,
    overrideAccess: true,
    select: { slug: true, role: true },
  })
  for (const page of res.docs) {
    if (page.role && page.slug) paths[page.role as PageRole] = `/${page.slug}`
  }
  return paths
}

type DocWithSlug = { slug?: string | null }

export function hrefFor(
  paths: RoutePaths,
  collection: 'pages' | 'platforms' | 'cases' | 'articles',
  doc: DocWithSlug,
): string | null {
  if (!doc?.slug) return null
  switch (collection) {
    case 'pages':
      return `/${doc.slug}`
    case 'platforms':
      return `${paths.ecosystem}/${doc.slug}`
    case 'cases':
      return `${paths.cases}/${doc.slug}`
    case 'articles':
      return `${paths.insights}/${doc.slug}`
  }
}

export async function pathForDoc(
  payload: Payload,
  collection: 'pages' | 'platforms' | 'cases' | 'articles',
  doc: DocWithSlug,
): Promise<string | null> {
  const paths = await loadRoutePaths(payload)
  return hrefFor(paths, collection, doc)
}
