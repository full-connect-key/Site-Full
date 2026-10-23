import type { ReactNode } from 'react'

type Props = {
  children: ReactNode
  theme?: 'dark' | 'light' | 'hero' | null
  product?: string | null
  id?: string
  className?: string
  labelledBy?: string
  label?: string
  spacing?: 'default' | 'sm' | 'none'
  as?: 'section' | 'div' | 'header' | 'article'
}

const spacingClass = { default: 'py-section', sm: 'py-section-sm', none: '' }

/** Seção com variante de tema nomeada (nunca hex). */
export function Section({
  children,
  theme = 'dark',
  product,
  id,
  className = '',
  labelledBy,
  label,
  spacing = 'default',
  as: Tag = 'section',
}: Props) {
  return (
    <Tag
      id={id}
      data-theme={theme ?? 'dark'}
      data-product={product ?? undefined}
      aria-labelledby={labelledBy}
      aria-label={labelledBy ? undefined : label}
      className={`relative ${spacingClass[spacing]} ${className}`}
    >
      {children}
    </Tag>
  )
}

export function Container({ children, className = '' }: { children: ReactNode; className?: string }) {
  return <div className={`container-site ${className}`}>{children}</div>
}
