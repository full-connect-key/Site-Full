import type { Media } from '@/payload-types'

export type MediaRef = number | Media | null | undefined

export const asMedia = (value: MediaRef): Media | null =>
  value && typeof value === 'object' && value.url ? value : null

export const isVideo = (m: Media | null) => Boolean(m?.mimeType?.startsWith('video/'))

/** Logos de terceiros/imprensa só aparecem com direito de uso confirmado. */
export const usableThirdPartyMedia = (value: MediaRef): Media | null => {
  const m = asMedia(value)
  return m && m.rightsConfirmed ? m : null
}

export const altOf = (m: Media | null): string => (m && !m.decorative ? m.alt ?? '' : '')

export const focalPosition = (m: Media | null): string =>
  m && typeof m.focalX === 'number' && typeof m.focalY === 'number' ? `${m.focalX}% ${m.focalY}%` : '50% 50%'
