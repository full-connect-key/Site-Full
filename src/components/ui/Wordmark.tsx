import { CmsMedia } from './CmsMedia'
import type { MediaRef } from '@/lib/media'
import { asMedia } from '@/lib/media'

/**
 * Logo oficial quando existir no CMS; senão, wordmark tipográfico neutro
 * com o nome — nunca um logo inventado (briefing 1.2).
 */
export function Wordmark({
  name,
  logo,
  className = '',
  imgClassName = 'h-7 w-auto',
}: {
  name: string
  logo?: MediaRef
  className?: string
  imgClassName?: string
}) {
  const m = asMedia(logo)
  if (m?.url) {
    return (
      <span className={`relative inline-flex ${className}`}>
        <span className="sr-only">{name}</span>
        <span aria-hidden="true" className={`relative block ${imgClassName}`} style={{ aspectRatio: m.width && m.height ? `${m.width}/${m.height}` : '4/1' }}>
          <CmsMedia media={m} decorative sizes="240px" />
        </span>
      </span>
    )
  }
  return <span className={`font-display font-semibold tracking-tight ${className}`}>{name}</span>
}
