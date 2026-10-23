import type {
  CollectionAfterChangeHook,
  CollectionAfterDeleteHook,
  GlobalAfterChangeHook,
  PayloadRequest,
} from 'payload'

/**
 * Revalidação sob demanda. O site é pequeno e as páginas se referenciam
 * (menus, listagens, "próximo case", sitemap), então revalidar o layout
 * inteiro é o caminho mais simples e correto.
 * Fora do runtime do Next (seed, scripts, testes) a chamada é ignorada.
 */
const revalidateSite = async (req: PayloadRequest, reason: string) => {
  if (req.context?.disableRevalidate) return
  try {
    const { revalidatePath } = await import('next/cache')
    revalidatePath('/', 'layout')
    req.payload.logger.debug(`revalidate: ${reason}`)
  } catch {
    // Executando fora do Next.js — nada a revalidar.
  }
}

type Versioned = { _status?: 'draft' | 'published' | null }

export const revalidateCollection: CollectionAfterChangeHook = async ({
  doc,
  previousDoc,
  req,
  collection,
}) => {
  const current = doc as Versioned
  const previous = previousDoc as Versioned | undefined
  // Rascunho que nunca foi publicado não muda o site público.
  const affectsPublic =
    current._status === undefined ||
    current._status === 'published' ||
    previous?._status === 'published'
  if (affectsPublic) await revalidateSite(req, `${collection.slug}:${String((doc as { id?: unknown }).id)}`)
  return doc
}

export const revalidateCollectionDelete: CollectionAfterDeleteHook = async ({ doc, req, collection }) => {
  await revalidateSite(req, `${collection.slug}:delete`)
  return doc
}

export const revalidateGlobal: GlobalAfterChangeHook = async ({ doc, req, global }) => {
  const current = doc as Versioned
  if (current._status === undefined || current._status === 'published')
    await revalidateSite(req, `global:${global.slug}`)
  return doc
}
