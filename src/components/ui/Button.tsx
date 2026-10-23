import type { ButtonHTMLAttributes, ReactNode } from 'react'

import type { ResolvedLink } from '@/lib/links'

import { SmartLink } from './SmartLink'

export type ButtonVariant = 'primary' | 'secondary' | 'ghost'

const base =
  'group/btn relative inline-flex min-h-12 items-center justify-center gap-3 overflow-hidden px-6 py-3 text-small font-semibold tracking-wide transition-colors duration-300 focus-visible:outline-offset-4 disabled:opacity-60'

const variants: Record<ButtonVariant, string> = {
  primary: 'bg-accent text-accent-contrast hover:bg-fg hover:text-surface',
  secondary: 'border border-fg/40 text-fg hover:border-accent hover:text-accent',
  ghost: 'px-0 text-fg underline decoration-accent decoration-2 underline-offset-8 hover:text-accent',
}

export const buttonClass = (variant: ButtonVariant = 'primary', extra = '') =>
  `${base} ${variants[variant]} ${extra}`

const Arrow = () => (
  <svg
    aria-hidden="true"
    viewBox="0 0 24 24"
    className="size-4 shrink-0 transition-transform duration-300 group-hover/btn:translate-x-1 motion-reduce:transition-none"
    fill="none"
    stroke="currentColor"
    strokeWidth="1.8"
  >
    <path d="M4 12h15M13 6l6 6-6 6" />
  </svg>
)

type ButtonProps = ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: ButtonVariant
  arrow?: boolean
  children: ReactNode
}

export function Button({ variant = 'primary', arrow = false, className = '', children, ...rest }: ButtonProps) {
  return (
    <button className={buttonClass(variant, className)} {...rest}>
      <span>{children}</span>
      {arrow ? <Arrow /> : null}
    </button>
  )
}

type ButtonLinkProps = {
  link: ResolvedLink | null
  variant?: ButtonVariant
  arrow?: boolean
  className?: string
  externalNotice?: string | null
}

/** Botão-link a partir de um link resolvido do CMS. Sem link → não renderiza. */
export function ButtonLink({ link, variant = 'primary', arrow = true, className = '', externalNotice }: ButtonLinkProps) {
  if (!link) return null
  return (
    <SmartLink href={link.href} className={buttonClass(variant, className)} externalNotice={externalNotice}>
      <span>{link.label}</span>
      {arrow ? <Arrow /> : null}
    </SmartLink>
  )
}
