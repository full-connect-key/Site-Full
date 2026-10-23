import Link from 'next/link'
import type { AnchorHTMLAttributes, ReactNode } from 'react'

import { isExternalHref } from '@/lib/links'

type Props = Omit<AnchorHTMLAttributes<HTMLAnchorElement>, 'href'> & {
  href: string
  children: ReactNode
  /** Texto para leitor de tela indicando nova aba (vem de uiCopy.a11y.externalLink). */
  externalNotice?: string | null
}

/** Link interno via next/link; externo abre em nova aba com aviso acessível. */
export function SmartLink({ href, children, externalNotice, ...rest }: Props) {
  if (isExternalHref(href)) {
    return (
      <a href={href} target="_blank" rel="noopener noreferrer" {...rest}>
        {children}
        {externalNotice ? <span className="sr-only"> {externalNotice}</span> : null}
      </a>
    )
  }
  return (
    <Link href={href} {...rest}>
      {children}
    </Link>
  )
}
