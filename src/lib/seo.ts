import type { Metadata } from 'next'

import type { Media, SiteSetting } from '@/payload-types'

import { asMedia } from './media'

export const siteUrl = () => (process.env.NEXT_PUBLIC_SERVER_URL || 'http://localhost:3000').replace(/\/$/, '')

/** Staging/dev sempre noindex. Só produção pode ser indexada. */
export const isIndexable = () => process.env.SITE_ENV === 'production'

type Seo = { title?: string | null; description?: string | null; image?: number | Media | null; noindex?: boolean | null } | null | undefined

const ogImage = (m: Media | null) => {
  if (!m?.url) return undefined
  const og = m.sizes?.og
  return [{ url: og?.url ?? m.url, width: og?.width ?? m.width ?? undefined, height: og?.height ?? m.height ?? undefined, alt: m.alt ?? '' }]
}

export function buildRootMetadata(settings: SiteSetting): Metadata {
  const template = settings.titleTemplate?.includes('%s') ? settings.titleTemplate : '%s'
  return {
    metadataBase: new URL(siteUrl()),
    title: { default: settings.defaultTitle || settings.siteName, template },
    description: settings.defaultDescription ?? undefined,
    applicationName: settings.siteName,
    openGraph: {
      type: 'website',
      locale: 'pt_BR',
      siteName: settings.siteName,
      images: ogImage(asMedia(settings.defaultOgImage)),
    },
    twitter: { card: 'summary_large_image' },
    robots: isIndexable() ? { index: true, follow: true } : { index: false, follow: false },
    formatDetection: { telephone: false },
  }
}

/**
 * Metadata por página a partir do grupo SEO do CMS.
 * absoluteTitle: o título do SEO já é completo (ex.: "Método S&M | Full Connect Key").
 */
export function buildPageMetadata({
  seo,
  fallbackTitle,
  fallbackDescription,
  path,
  fallbackImage,
  type = 'website',
  publishedTime,
}: {
  seo: Seo
  fallbackTitle?: string | null
  fallbackDescription?: string | null
  path: string
  fallbackImage?: number | Media | null
  type?: 'website' | 'article'
  publishedTime?: string | null
}): Metadata {
  const title = seo?.title || fallbackTitle || undefined
  const description = seo?.description || fallbackDescription || undefined
  const images = ogImage(asMedia(seo?.image) ?? asMedia(fallbackImage))
  const noindex = !isIndexable() || Boolean(seo?.noindex)
  return {
    title: seo?.title ? { absolute: seo.title } : title,
    description,
    alternates: { canonical: path },
    openGraph: {
      title,
      description,
      url: path,
      type,
      images,
      ...(type === 'article' && publishedTime ? { publishedTime } : {}),
    },
    twitter: { card: 'summary_large_image', title, description, images: images?.map((i) => i.url) },
    robots: noindex ? { index: false, follow: !isIndexable() ? false : true } : undefined,
  }
}
