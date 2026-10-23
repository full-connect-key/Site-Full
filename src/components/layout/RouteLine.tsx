'use client'

import { usePathname } from 'next/navigation'
import { useEffect, useRef, useState } from 'react'

import { useReducedMotion } from '@/animations/useReducedMotion'

/**
 * Transição de página por "linha de conexão": uma linha atravessa o topo
 * quando a rota muda. Não bloqueia nem atrasa a navegação; some com
 * movimento reduzido.
 */
export function RouteLine() {
  const pathname = usePathname()
  const reduced = useReducedMotion()
  const first = useRef(true)
  const [key, setKey] = useState(0)

  useEffect(() => {
    if (first.current) {
      first.current = false
      return
    }
    setKey((k) => k + 1)
  }, [pathname])

  if (reduced || key === 0) return null
  return (
    <span
      key={key}
      aria-hidden="true"
      className="pointer-events-none fixed left-0 top-0 z-[70] h-0.5 w-full origin-left bg-accent"
      style={{ animation: 'route-line 900ms cubic-bezier(0.16,1,0.3,1) forwards' }}
    />
  )
}
