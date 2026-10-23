'use client'

import { openCookiePreferences } from './consent'

export function CookiePreferencesButton({ label }: { label?: string | null }) {
  if (!label) return null
  return (
    <button type="button" onClick={openCookiePreferences} className="inline-flex min-h-11 items-center hover:text-accent">
      {label}
    </button>
  )
}
