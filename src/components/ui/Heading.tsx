import type { ReactNode } from 'react'

type Level = 1 | 2 | 3 | 4
type Size = 'xl' | 'l' | 'm' | 's'

const sizes: Record<Size, string> = {
  xl: 'text-display-xl',
  l: 'text-display-l',
  m: 'text-display-m',
  s: 'text-display-s',
}

type Props = { level?: Level; size?: Size; id?: string; className?: string; children: ReactNode }

/** Nível semântico e tamanho visual são independentes. */
export function Heading({ level = 2, size = 'm', id, className = '', children }: Props) {
  const Tag = `h${level}` as const
  return (
    <Tag id={id} className={`${sizes[size]} font-display ${className}`}>
      {children}
    </Tag>
  )
}

export function Eyebrow({ children, className = '' }: { children: ReactNode; className?: string }) {
  return <p className={`eyebrow text-fg-3 ${className}`}>{children}</p>
}
