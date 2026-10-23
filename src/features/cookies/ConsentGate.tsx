'use client'

import { useEffect, useState, type ReactNode } from 'react'

import { CONSENT_EVENT, hasConsent, type ConsentCategory } from './consent'

/** Renderiza os filhos (ex.: <Script>) somente após consentimento da categoria. */
export function ConsentGate({ category, children }: { category: ConsentCategory; children: ReactNode }) {
  const [allowed, setAllowed] = useState(false)
  useEffect(() => {
    const update = () => setAllowed(hasConsent(category))
    update()
    window.addEventListener(CONSENT_EVENT, update)
    return () => window.removeEventListener(CONSENT_EVENT, update)
  }, [category])
  return allowed ? <>{children}</> : null
}
