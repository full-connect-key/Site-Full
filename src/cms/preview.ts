import type { CollectionConfig, GlobalConfig } from 'payload'

const serverUrl = () => process.env.NEXT_PUBLIC_SERVER_URL || 'http://localhost:3000'

/**
 * URL do preview autenticado. A rota /next/preview confere a sessão do painel
 * antes de ativar o draft mode, então o link não funciona para visitantes.
 */
export const previewUrl = (collection: string, data: { id?: string | number; slug?: string | null }) => {
  const params = new URLSearchParams({ collection, id: String(data.id ?? '') })
  if (data.slug) params.set('slug', data.slug)
  return `${serverUrl()}/next/preview?${params.toString()}`
}

export const livePreview = (collection: string): NonNullable<CollectionConfig['admin']>['livePreview'] => ({
  url: ({ data }) => previewUrl(collection, data as { id?: string; slug?: string }),
  breakpoints: [
    { label: 'Celular', name: 'mobile', width: 375, height: 812 },
    { label: 'Tablet', name: 'tablet', width: 768, height: 1024 },
    { label: 'Desktop', name: 'desktop', width: 1440, height: 900 },
  ],
})

export const globalLivePreview = (global: string): NonNullable<GlobalConfig['admin']>['livePreview'] => ({
  url: () => `${serverUrl()}/next/preview?global=${global}`,
  breakpoints: [
    { label: 'Celular', name: 'mobile', width: 375, height: 812 },
    { label: 'Desktop', name: 'desktop', width: 1440, height: 900 },
  ],
})
