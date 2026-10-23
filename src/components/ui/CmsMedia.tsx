import Image from 'next/image'

import { altOf, asMedia, focalPosition, isVideo, type MediaRef } from '@/lib/media'

type Props = {
  media: MediaRef
  /** Preenche o contêiner (pai precisa ter position relative e tamanho). */
  fill?: boolean
  sizes?: string
  priority?: boolean
  className?: string
  /** Força alt vazio (quando o texto ao lado já descreve a imagem). */
  decorative?: boolean
  /** Vídeos: exibe controles (padrão). Nunca autoplay com som. */
  videoControls?: boolean
  videoLabel?: string
}

/** Imagem/vídeo do CMS com focal point, tamanhos responsivos e lazy loading. */
export function CmsMedia({
  media,
  fill = true,
  sizes = '100vw',
  priority = false,
  className = '',
  decorative = false,
  videoControls = true,
  videoLabel,
}: Props) {
  const m = asMedia(media)
  if (!m?.url) return null
  const alt = decorative ? '' : altOf(m)
  const position = focalPosition(m)

  if (isVideo(m)) {
    const poster = asMedia(m.poster as MediaRef)
    return (
      <video
        className={`${fill ? 'absolute inset-0 size-full object-cover' : 'h-auto w-full'} ${className}`}
        style={{ objectPosition: position }}
        src={m.url}
        poster={poster?.sizes?.wide?.url ?? poster?.url ?? undefined}
        controls={videoControls}
        preload="none"
        playsInline
        aria-label={videoLabel || alt || undefined}
      />
    )
  }

  if (m.mimeType === 'image/svg+xml') {
    return (
      // eslint-disable-next-line @next/next/no-img-element
      <img
        src={m.url}
        alt={alt}
        className={`${fill ? 'absolute inset-0 size-full object-contain' : 'h-auto w-full'} ${className}`}
        loading={priority ? 'eager' : 'lazy'}
        decoding="async"
      />
    )
  }

  if (fill) {
    return (
      <Image
        src={m.url}
        alt={alt}
        fill
        sizes={sizes}
        priority={priority}
        className={`object-cover ${className}`}
        style={{ objectPosition: position }}
      />
    )
  }
  return (
    <Image
      src={m.url}
      alt={alt}
      width={m.width ?? 1600}
      height={m.height ?? 900}
      sizes={sizes}
      priority={priority}
      className={`h-auto w-full ${className}`}
    />
  )
}
