import type { SiteSetting, UiCopy } from '@/payload-types'
import { fill, hasText } from '@/lib/format'
import { resolveLink } from '@/lib/links'
import type { RoutePaths } from '@/lib/paths'

import { ButtonLink } from '../ui/Button'
import { SmartLink } from '../ui/SmartLink'
import { Wordmark } from '../ui/Wordmark'
import { CookiePreferencesButton } from '@/features/cookies/CookiePreferencesButton'
import { buildLinkList } from './nav'

type Props = { settings: SiteSetting; ui: UiCopy; paths: RoutePaths; legalVisible: boolean }

export function Footer({ settings, ui, paths, legalVisible }: Props) {
  const groups = (settings.footerGroups ?? [])
    .map((g) => ({ title: g.title, links: buildLinkList(g.links, paths) }))
    .filter((g) => g.links.length > 0)
  const legal = legalVisible ? buildLinkList(settings.legalLinks, paths) : []
  const cta = resolveLink(settings.footerCta, paths)
  const socials = (settings.socials ?? []).filter((s) => s.url && s.label)
  const year = new Date().getFullYear()
  const ext = ui.a11y?.externalLink

  return (
    <footer data-theme="hero" className="relative border-t border-line pb-10 pt-section-sm">
      <div className="container-site">
        <div className="grid-editorial gap-y-12">
          <div className="col-span-4 lg:col-span-6">
            <Wordmark name={settings.siteName} logo={settings.logoOnDark} className="text-2xl" imgClassName="h-9 w-auto" />
            {hasText(settings.footerTagline) ? (
              <p className="mt-8 max-w-xl font-display text-display-s text-fg">{settings.footerTagline}</p>
            ) : null}
            {hasText(settings.footerText) ? <p className="mt-5 max-w-lg text-fg-2">{settings.footerText}</p> : null}
            {cta ? (
              <div className="mt-8">
                <ButtonLink link={cta} externalNotice={ext} />
              </div>
            ) : null}
          </div>

          <nav aria-label={ui.a11y?.footerNav ?? undefined} className="col-span-4 grid grid-cols-2 gap-8 lg:col-span-6 lg:grid-cols-3">
            {groups.map((group, i) => (
              <div key={i}>
                {hasText(group.title) ? <p className="eyebrow mb-4 text-fg-3">{group.title}</p> : null}
                <ul className="space-y-1">
                  {group.links.map((link) => (
                    <li key={link.href}>
                      <SmartLink href={link.href} externalNotice={ext} className="inline-flex min-h-11 items-center text-small text-fg-2 transition-colors hover:text-accent">
                        {link.label}
                      </SmartLink>
                    </li>
                  ))}
                </ul>
              </div>
            ))}
            {settings.email || settings.phone || socials.length > 0 || hasText(settings.address) ? (
              <div>
                <ul className="space-y-1 text-small text-fg-2">
                  {settings.email ? (
                    <li>
                      <a className="inline-flex min-h-11 items-center hover:text-accent" href={`mailto:${settings.email}`}>
                        {settings.email}
                      </a>
                    </li>
                  ) : null}
                  {hasText(settings.phone) ? (
                    <li>
                      <a className="inline-flex min-h-11 items-center hover:text-accent" href={`tel:${settings.phone.replace(/[^\d+]/g, '')}`}>
                        {settings.phone}
                      </a>
                    </li>
                  ) : null}
                  {socials.map((s) => (
                    <li key={s.id ?? s.url}>
                      <SmartLink href={s.url} externalNotice={ext} className="inline-flex min-h-11 items-center hover:text-accent">
                        {s.label}
                      </SmartLink>
                    </li>
                  ))}
                  {hasText(settings.address) ? <li className="whitespace-pre-line pt-2">{settings.address}</li> : null}
                </ul>
              </div>
            ) : null}
          </nav>
        </div>

        <div className="mt-section-sm flex flex-col gap-4 border-t border-line pt-6 text-small text-fg-3 md:flex-row md:items-center md:justify-between">
          {hasText(settings.copyright) ? <p>{fill(settings.copyright, { ano: year })}</p> : <span />}
          <ul className="flex flex-wrap items-center gap-x-6 gap-y-1">
            {legal.map((link) => (
              <li key={link.href}>
                <SmartLink href={link.href} className="inline-flex min-h-11 items-center hover:text-accent">
                  {link.label}
                </SmartLink>
              </li>
            ))}
            <li>
              <CookiePreferencesButton label={ui.cookies?.openPreferences} />
            </li>
            {hasText(ui.a11y?.backToTop) ? (
              <li>
                <a href="#top" className="inline-flex min-h-11 items-center gap-2 hover:text-accent">
                  {ui.a11y.backToTop}
                  <svg aria-hidden="true" viewBox="0 0 16 16" className="size-3.5" fill="none" stroke="currentColor" strokeWidth="1.6">
                    <path d="M8 13V3M3.5 7.5L8 3l4.5 4.5" />
                  </svg>
                </a>
              </li>
            ) : null}
          </ul>
        </div>
      </div>
    </footer>
  )
}
