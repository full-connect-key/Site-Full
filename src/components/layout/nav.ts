import type { SiteSetting } from '@/payload-types'
import { resolveLink, type ResolvedLink } from '@/lib/links'
import type { RoutePaths } from '@/lib/paths'

export type NavItem = ResolvedLink & { children: ResolvedLink[] }

export function buildMainNav(settings: SiteSetting, paths: RoutePaths): NavItem[] {
  return (settings.mainNav ?? [])
    .map((item) => {
      const link = resolveLink(item.link, paths)
      if (!link) return null
      const children = (item.children ?? [])
        .map((c) => resolveLink(c.link, paths))
        .filter((c): c is ResolvedLink => Boolean(c))
      return { ...link, children }
    })
    .filter((i): i is NavItem => Boolean(i))
}

export function buildLinkList(items: { link?: unknown }[] | null | undefined, paths: RoutePaths): ResolvedLink[] {
  return (items ?? [])
    .map((i) => resolveLink(i.link as Parameters<typeof resolveLink>[0], paths))
    .filter((l): l is ResolvedLink => Boolean(l))
}
