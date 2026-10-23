'use client'

/**
 * Consentimento de cookies.
 * - A escolha fica em localStorage (não grava cookie algum).
 * - Nenhuma ferramenta de análise/marketing está aprovada hoje: o registro
 *   (registry.ts) está vazio e nada opcional é carregado.
 * - Scripts opcionais futuros devem passar por <ConsentGate>.
 */
export type ConsentCategory = 'necessary' | 'analytics' | 'marketing'
export type Consent = { version: number; analytics: boolean; marketing: boolean; updatedAt: string }

export const CONSENT_KEY = 'fck:cookie-consent'
export const CONSENT_VERSION = 1
export const CONSENT_EVENT = 'fck:consent-change'
export const OPEN_PREFERENCES_EVENT = 'fck:open-cookie-preferences'

export function readConsent(): Consent | null {
  try {
    const raw = window.localStorage.getItem(CONSENT_KEY)
    if (!raw) return null
    const parsed = JSON.parse(raw) as Consent
    return parsed.version === CONSENT_VERSION ? parsed : null
  } catch {
    return null
  }
}

export function writeConsent(choice: { analytics: boolean; marketing: boolean }): Consent {
  const consent: Consent = { version: CONSENT_VERSION, ...choice, updatedAt: new Date().toISOString() }
  try {
    window.localStorage.setItem(CONSENT_KEY, JSON.stringify(consent))
  } catch {
    // Armazenamento indisponível: a escolha vale só nesta visita.
  }
  window.dispatchEvent(new CustomEvent(CONSENT_EVENT, { detail: consent }))
  return consent
}

export function hasConsent(category: ConsentCategory): boolean {
  if (category === 'necessary') return true
  const c = readConsent()
  return Boolean(c?.[category])
}

export const openCookiePreferences = () => window.dispatchEvent(new Event(OPEN_PREFERENCES_EVENT))
