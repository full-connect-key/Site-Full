import type { Metadata, Viewport } from 'next'
import type { ReactNode } from 'react'

import { Footer } from '@/components/layout/Footer'
import { Header } from '@/components/layout/Header'
import { buildMainNav } from '@/components/layout/nav'
import { RouteLine } from '@/components/layout/RouteLine'
import { PreviewBar } from '@/components/layout/PreviewBar'
import { Wordmark } from '@/components/ui/Wordmark'
import { CookieBanner } from '@/features/cookies/CookieBanner'
import { getRoutePaths, getSiteSettings, getUiCopy } from '@/lib/cms'
import { resolveLink } from '@/lib/links'
import { isDraft } from '@/lib/payload'
import { buildRootMetadata } from '@/lib/seo'
import { displayFont, textFont } from '@/styles/fonts'
import '@/styles/globals.css'

export async function generateMetadata(): Promise<Metadata> {
  return buildRootMetadata(await getSiteSettings())
}

export const viewport: Viewport = {
  themeColor: '#05080A',
  colorScheme: 'dark light',
}

export default async function FrontendLayout({ children }: { children: ReactNode }) {
  const [settings, ui, paths, draft] = await Promise.all([getSiteSettings(), getUiCopy(), getRoutePaths(), isDraft()])
  const items = buildMainNav(settings, paths)
  const cta = resolveLink(settings.headerCta, paths)
  const a11y = ui.a11y ?? {}

  return (
    <html lang="pt-BR" className={`${displayFont.variable} ${textFont.variable}`} data-theme="dark">
      <body id="top">
        {a11y.skipToContent ? (
          <a
            href="#conteudo"
            className="sr-only-focusable left-4 top-4 z-[100] bg-accent px-4 py-3 font-semibold text-accent-contrast"
          >
            {a11y.skipToContent}
          </a>
        ) : null}
        <RouteLine />
        <Header
          brand={<Wordmark name={settings.siteName} logo={settings.logoOnDark} imgClassName="h-7 w-auto" />}
          items={items}
          cta={cta}
          labels={{
            homeLink: a11y.homeLink ?? settings.siteName,
            mainNav: a11y.mainNav ?? '',
            openMenu: a11y.openMenu ?? '',
            closeMenu: a11y.closeMenu ?? '',
            openSubmenu: a11y.openSubmenu ?? '',
            externalLink: a11y.externalLink ?? '',
          }}
        />
        <main id="conteudo" tabIndex={-1} className="outline-none">
          {children}
        </main>
        <Footer settings={settings} ui={ui} paths={paths} legalVisible />
        <CookieBanner copy={ui.cookies ?? {}} policyHref={paths.cookies} mode={settings.cookieBannerMode ?? 'auto'} />
        {draft ? <PreviewBar /> : null}
      </body>
    </html>
  )
}
