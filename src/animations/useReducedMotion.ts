'use client'

import { useSyncExternalStore } from 'react'

const query = '(prefers-reduced-motion: reduce)'

const subscribe = (cb: () => void) => {
  const mql = window.matchMedia(query)
  mql.addEventListener('change', cb)
  return () => mql.removeEventListener('change', cb)
}

/**
 * true quando o usuário pede movimento reduzido. No servidor assume true
 * (versão estática), evitando animar antes da hidratação.
 */
export function useReducedMotion(): boolean {
  return useSyncExternalStore(
    subscribe,
    () => window.matchMedia(query).matches,
    () => true,
  )
}

export const prefersReducedMotion = () =>
  typeof window === 'undefined' ? true : window.matchMedia(query).matches
