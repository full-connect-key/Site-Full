import 'server-only'

import type { Where } from 'payload'
import { cache } from 'react'

import type {
  Article,
  ArticleCategory,
  Case,
  Client,
  Homepage,
  InstitutionalMetric,
  Page,
  Partner,
  Platform,
  PressMention,
  Sector,
  SiteSetting,
  TeamMember,
  Testimonial,
  UiCopy,
} from '@/payload-types'

import { getPayloadClient, isDraft } from './payload'
import { loadRoutePaths, type RoutePaths } from './paths'

/**
 * Camada de leitura do CMS para o site público.
 * - Fora do preview: overrideAccess=false → as regras de acesso garantem que
 *   só documentos publicados/autorizados saem do banco.
 * - Em preview (draft mode autenticado): lê rascunhos.
 */
const readOpts = async () => {
  const draft = await isDraft()
  return { draft, overrideAccess: draft }
}

export const getSiteSettings = cache(async (): Promise<SiteSetting> => {
  const payload = await getPayloadClient()
  return payload.findGlobal({ slug: 'siteSettings', depth: 2, ...(await readOpts()) })
})

export const getUiCopy = cache(async (): Promise<UiCopy> => {
  const payload = await getPayloadClient()
  return payload.findGlobal({ slug: 'uiCopy', depth: 0, ...(await readOpts()) })
})

export const getHomepage = cache(async (): Promise<Homepage> => {
  const payload = await getPayloadClient()
  return payload.findGlobal({ slug: 'homepage', depth: 2, ...(await readOpts()) })
})

export const getRoutePaths = cache(async (): Promise<RoutePaths> => {
  const payload = await getPayloadClient()
  return loadRoutePaths(payload, await isDraft())
})

export const getPageBySlug = cache(async (slug: string): Promise<Page | null> => {
  const payload = await getPayloadClient()
  const res = await payload.find({
    collection: 'pages',
    where: { slug: { equals: slug } },
    limit: 1,
    depth: 2,
    ...(await readOpts()),
  })
  return res.docs[0] ?? null
})

export const getPageByRole = cache(async (role: NonNullable<Page['role']>): Promise<Page | null> => {
  const payload = await getPayloadClient()
  const res = await payload.find({
    collection: 'pages',
    where: { role: { equals: role } },
    limit: 1,
    depth: 2,
    ...(await readOpts()),
  })
  return res.docs[0] ?? null
})

export const getPlatforms = cache(async (): Promise<Platform[]> => {
  const payload = await getPayloadClient()
  const res = await payload.find({ collection: 'platforms', sort: 'order', limit: 20, depth: 1, ...(await readOpts()) })
  return res.docs
})

export const getPlatformBySlug = cache(async (slug: string): Promise<Platform | null> => {
  const payload = await getPayloadClient()
  const res = await payload.find({
    collection: 'platforms',
    where: { slug: { equals: slug } },
    limit: 1,
    depth: 2,
    ...(await readOpts()),
  })
  return res.docs[0] ?? null
})

export const getSectors = cache(async (): Promise<Sector[]> => {
  const payload = await getPayloadClient()
  const res = await payload.find({ collection: 'sectors', sort: 'order', limit: 50, depth: 1, overrideAccess: false })
  return res.docs
})

/** Clientes autorizados com logo cujo direito de uso foi confirmado. */
export const getAuthorizedClients = cache(async (): Promise<Client[]> => {
  const payload = await getPayloadClient()
  const res = await payload.find({
    collection: 'clients',
    where: { displayAuthorized: { equals: true }, logo: { exists: true } },
    sort: 'order',
    limit: 100,
    depth: 1,
    overrideAccess: false,
  })
  return res.docs.filter((c) => typeof c.logo === 'object' && c.logo?.url)
})

export const getCases = cache(async (where?: Where): Promise<Case[]> => {
  const payload = await getPayloadClient()
  const res = await payload.find({
    collection: 'cases',
    where,
    sort: 'order',
    limit: 200,
    depth: 1,
    ...(await readOpts()),
  })
  return res.docs
})

export const getFeaturedCases = cache(async (): Promise<Case[]> => {
  const docs = await getCases({ featuredPosition: { exists: true } })
  return docs
    .filter((c) => c.featuredPosition)
    .sort((a, b) => Number(a.featuredPosition) - Number(b.featuredPosition))
    .slice(0, 3)
})

export const getCaseBySlug = cache(async (slug: string): Promise<Case | null> => {
  const payload = await getPayloadClient()
  const res = await payload.find({
    collection: 'cases',
    where: { slug: { equals: slug } },
    limit: 1,
    depth: 2,
    ...(await readOpts()),
  })
  return res.docs[0] ?? null
})

export const getInstitutionalMetrics = cache(async (): Promise<InstitutionalMetric[]> => {
  const payload = await getPayloadClient()
  const res = await payload.find({
    collection: 'institutionalMetrics',
    where: { validated: { equals: true } },
    sort: 'order',
    limit: 12,
    overrideAccess: false,
  })
  return res.docs
})

export const getPartners = cache(async (): Promise<Partner[]> => {
  const payload = await getPayloadClient()
  const res = await payload.find({ collection: 'partners', sort: 'order', limit: 3, depth: 1, overrideAccess: false })
  return res.docs
})

export const getTeam = cache(async (): Promise<TeamMember[]> => {
  const payload = await getPayloadClient()
  const res = await payload.find({ collection: 'teamMembers', sort: 'order', limit: 100, depth: 1, overrideAccess: false })
  return res.docs
})

export const getTestimonials = cache(async (): Promise<Testimonial[]> => {
  const payload = await getPayloadClient()
  const res = await payload.find({
    collection: 'testimonials',
    where: { authorized: { equals: true } },
    sort: 'order',
    limit: 30,
    depth: 1,
    overrideAccess: false,
  })
  return res.docs
})

export const getArticleCategories = cache(async (): Promise<ArticleCategory[]> => {
  const payload = await getPayloadClient()
  const res = await payload.find({ collection: 'articleCategories', sort: 'order', limit: 50, overrideAccess: false })
  return res.docs
})

export const getArticles = cache(async (where?: Where, limit = 200): Promise<Article[]> => {
  const payload = await getPayloadClient()
  const res = await payload.find({
    collection: 'articles',
    where,
    sort: '-publishedAt',
    limit,
    depth: 1,
    ...(await readOpts()),
  })
  return res.docs
})

export const getArticleBySlug = cache(async (slug: string): Promise<Article | null> => {
  const payload = await getPayloadClient()
  const res = await payload.find({
    collection: 'articles',
    where: { slug: { equals: slug } },
    limit: 1,
    depth: 2,
    ...(await readOpts()),
  })
  return res.docs[0] ?? null
})

export const getPressMentions = cache(async (): Promise<PressMention[]> => {
  const payload = await getPayloadClient()
  const res = await payload.find({
    collection: 'pressMentions',
    sort: '-publishedAt',
    limit: 200,
    depth: 1,
    ...(await readOpts()),
  })
  return res.docs
})

export const findRedirect = cache(async (from: string) => {
  const payload = await getPayloadClient()
  const res = await payload.find({
    collection: 'redirects',
    where: { from: { equals: from } },
    limit: 1,
    depth: 0,
    overrideAccess: true,
  })
  return res.docs[0] ?? null
})
