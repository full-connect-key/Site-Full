import type { CollectionBeforeChangeHook } from 'payload'

import { pathForDoc } from '../../lib/paths'

/**
 * Quando o slug de um documento publicado muda, cria um redirect permanente
 * do endereço antigo para o novo (coleção "redirects").
 */
export const redirectOnSlugChange =
  (collection: 'pages' | 'platforms' | 'cases' | 'articles'): CollectionBeforeChangeHook =>
  async ({ data, originalDoc, req, operation }) => {
    if (operation !== 'update' || !originalDoc) return data
    const oldSlug = originalDoc.slug as string | undefined
    const newSlug = (data.slug ?? oldSlug) as string | undefined
    if (!oldSlug || !newSlug || oldSlug === newSlug) return data
    // Só existe endereço público antigo se o documento já foi publicado.
    if (originalDoc._status && originalDoc._status !== 'published') return data

    try {
      const from = await pathForDoc(req.payload, collection, { ...originalDoc, slug: oldSlug })
      const to = await pathForDoc(req.payload, collection, { ...originalDoc, ...data, slug: newSlug })
      if (!from || !to || from === to) return data

      // Evita cadeias: redirects que apontavam para "from" passam a apontar para "to".
      await req.payload.update({
        collection: 'redirects',
        where: { to: { equals: from } },
        data: { to },
        req,
        overrideAccess: true,
      })
      // Remove redirect que agora conflitaria com o novo endereço.
      await req.payload.delete({
        collection: 'redirects',
        where: { from: { equals: to } },
        req,
        overrideAccess: true,
      })
      const existing = await req.payload.find({
        collection: 'redirects',
        where: { from: { equals: from } },
        limit: 1,
        req,
        overrideAccess: true,
      })
      if (existing.docs[0]) {
        await req.payload.update({
          collection: 'redirects',
          id: existing.docs[0].id,
          data: { to },
          req,
          overrideAccess: true,
        })
      } else {
        await req.payload.create({
          collection: 'redirects',
          data: { from, to, type: '301', automatic: true },
          req,
          overrideAccess: true,
        })
      }
    } catch (error) {
      req.payload.logger.error({ err: error, msg: 'Falha ao criar redirect automático' })
    }
    return data
  }
