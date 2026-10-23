'use client'

import Link from 'next/link'
import { useCallback, useEffect, useId, useRef, useState } from 'react'

import { Button } from '@/components/ui/Button'

import { OPEN_PREFERENCES_EVENT, readConsent, writeConsent } from './consent'
import { hasOptionalTools } from './registry'

type Copy = Partial<
  Record<
    | 'title'
    | 'text'
    | 'acceptAll'
    | 'rejectOptional'
    | 'customize'
    | 'preferencesTitle'
    | 'necessary'
    | 'necessaryDescription'
    | 'analytics'
    | 'analyticsDescription'
    | 'marketing'
    | 'marketingDescription'
    | 'alwaysOn'
    | 'save'
    | 'policyLink',
    string | null
  >
>

type Props = { copy: Copy; policyHref: string; mode: 'auto' | 'always' | 'never' }

/**
 * Banner + central de preferências. Nada opcional é carregado sem consentimento.
 * A central pode ser aberta pelo link do rodapé mesmo com o banner oculto.
 */
export function CookieBanner({ copy, policyHref, mode }: Props) {
  const [bannerOpen, setBannerOpen] = useState(false)
  const [prefsOpen, setPrefsOpen] = useState(false)
  const [analytics, setAnalytics] = useState(false)
  const [marketing, setMarketing] = useState(false)
  const dialogRef = useRef<HTMLDialogElement>(null)
  const titleId = useId()
  const prefsTitleId = useId()

  useEffect(() => {
    const existing = readConsent()
    const shouldShow = mode === 'always' || (mode === 'auto' && hasOptionalTools())
    if (!existing && shouldShow) setBannerOpen(true)
    if (existing) {
      setAnalytics(existing.analytics)
      setMarketing(existing.marketing)
    }
    const open = () => {
      const c = readConsent()
      setAnalytics(Boolean(c?.analytics))
      setMarketing(Boolean(c?.marketing))
      setPrefsOpen(true)
    }
    window.addEventListener(OPEN_PREFERENCES_EVENT, open)
    return () => window.removeEventListener(OPEN_PREFERENCES_EVENT, open)
  }, [mode])

  useEffect(() => {
    const d = dialogRef.current
    if (!d) return
    if (prefsOpen && !d.open) d.showModal()
    if (!prefsOpen && d.open) d.close()
  }, [prefsOpen])

  const decide = useCallback((choice: { analytics: boolean; marketing: boolean }) => {
    writeConsent(choice)
    setAnalytics(choice.analytics)
    setMarketing(choice.marketing)
    setBannerOpen(false)
    setPrefsOpen(false)
  }, [])

  if (!copy.title && !copy.text) return null

  return (
    <>
      {bannerOpen ? (
        <section
          role="region"
          aria-labelledby={titleId}
          data-theme="dark"
          data-testid="cookie-banner"
          className="fixed inset-x-3 bottom-3 z-[80] max-w-xl border border-line bg-dark-card p-6 shadow-2xl sm:left-6 sm:right-auto sm:bottom-6"
        >
          <h2 id={titleId} className="font-display text-xl">
            {copy.title}
          </h2>
          <p className="mt-3 text-small text-fg-2">
            {copy.text}{' '}
            {copy.policyLink ? (
              <Link href={policyHref} className="text-accent underline underline-offset-4">
                {copy.policyLink}
              </Link>
            ) : null}
          </p>
          <div className="mt-5 flex flex-wrap gap-2">
            <Button onClick={() => decide({ analytics: true, marketing: true })}>{copy.acceptAll}</Button>
            <Button variant="secondary" onClick={() => decide({ analytics: false, marketing: false })}>
              {copy.rejectOptional}
            </Button>
            <Button variant="ghost" className="px-2" onClick={() => setPrefsOpen(true)}>
              {copy.customize}
            </Button>
          </div>
        </section>
      ) : null}

      <dialog
        ref={dialogRef}
        aria-labelledby={prefsTitleId}
        data-theme="dark"
        onClose={() => setPrefsOpen(false)}
        className="m-auto w-[min(36rem,calc(100vw-1.5rem))] border border-line bg-dark-card p-0 text-fg backdrop:bg-hero-black/80"
      >
        <form
          method="dialog"
          className="p-6"
          onSubmit={(e) => {
            e.preventDefault()
            decide({ analytics, marketing })
          }}
        >
          <h2 id={prefsTitleId} className="font-display text-2xl">
            {copy.preferencesTitle}
          </h2>
          <fieldset className="mt-6 space-y-4">
            <legend className="sr-only">{copy.preferencesTitle}</legend>
            <PrefRow label={copy.necessary} description={copy.necessaryDescription} checked disabled badge={copy.alwaysOn} />
            <PrefRow label={copy.analytics} description={copy.analyticsDescription} checked={analytics} onChange={setAnalytics} />
            <PrefRow label={copy.marketing} description={copy.marketingDescription} checked={marketing} onChange={setMarketing} />
          </fieldset>
          <div className="mt-6 flex flex-wrap gap-2">
            <Button type="submit">{copy.save}</Button>
            <Button type="button" variant="secondary" onClick={() => decide({ analytics: true, marketing: true })}>
              {copy.acceptAll}
            </Button>
            <Button type="button" variant="ghost" className="px-2" onClick={() => decide({ analytics: false, marketing: false })}>
              {copy.rejectOptional}
            </Button>
          </div>
        </form>
      </dialog>
    </>
  )
}

function PrefRow({
  label,
  description,
  checked,
  disabled,
  badge,
  onChange,
}: {
  label?: string | null
  description?: string | null
  checked: boolean
  disabled?: boolean
  badge?: string | null
  onChange?: (v: boolean) => void
}) {
  const id = useId()
  return (
    <div className="flex items-start justify-between gap-4 border-t border-line pt-4">
      <div>
        <label htmlFor={id} className="font-semibold">
          {label}
        </label>
        {description ? <p className="mt-1 text-small text-fg-3">{description}</p> : null}
      </div>
      <div className="flex shrink-0 items-center gap-2">
        {disabled && badge ? <span className="text-small text-fg-3">{badge}</span> : null}
        <input
          id={id}
          type="checkbox"
          role="switch"
          className="size-6 accent-[var(--accent)]"
          checked={checked}
          disabled={disabled}
          aria-checked={checked}
          onChange={(e) => onChange?.(e.target.checked)}
        />
      </div>
    </div>
  )
}
