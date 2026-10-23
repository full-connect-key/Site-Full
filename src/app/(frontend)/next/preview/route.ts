import config from '@payload-config'
import { draftMode } from 'next/headers'
import { redirect } from 'next/navigation'
import { getPayload } from 'payload'

import { isStaffUser } from '@/cms/access'
import { hrefFor, loadRoutePaths } from '@/lib/paths'

type Collection = 'pages' | 'platforms' | 'cases' | 'articles'
const collections: Collection[] = ['pages', 'platforms', 'cases', 'articles']

/**
 * Preview autenticado: só ativa o draft mode se a requisição vier de alguém
 * logado no painel (cookie de sessão do Payload). Visitantes recebem 401.
 */
export async function GET(request: Request): Promise<Response> {
  const payload = await getPayload({ config })
  const { user } = await payload.auth({ headers: request.headers })
  if (!user || !isStaffUser(user as never)) {
    return new Response('Unauthorized', { status: 401 })
  }

  const url = new URL(request.url)
  const collection = url.searchParams.get('collection') as Collection | null
  const id = url.searchParams.get('id')
  const global = url.searchParams.get('global')

  let path = '/'
  if (!global && collection && collections.includes(collection) && id) {
    const doc = await payload.findByID({ collection, id, draft: true, depth: 0, overrideAccess: true }).catch(() => null)
    const paths = await loadRoutePaths(payload, true)
    path = (doc && hrefFor(paths, collection, doc)) || '/'
  }

  ;(await draftMode()).enable()
  redirect(path)
}
