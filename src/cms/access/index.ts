import type { Access, FieldAccess, PayloadRequest } from 'payload'

export type Role = 'admin' | 'editor'

type UserLike = { roles?: Role[] | null } | null | undefined

export const hasRole = (user: UserLike, role: Role): boolean =>
  Boolean(user?.roles?.includes(role))

export const isAdminUser = (user: UserLike): boolean => hasRole(user, 'admin')

export const isStaffUser = (user: UserLike): boolean =>
  hasRole(user, 'admin') || hasRole(user, 'editor')

const userOf = (req: PayloadRequest): UserLike => req.user as UserLike

/** Somente administradores. */
export const adminOnly: Access = ({ req }) => isAdminUser(userOf(req))

/** Administradores e editores (equipe de conteúdo). */
export const staffOnly: Access = ({ req }) => isStaffUser(userOf(req))

/** Público lê; equipe edita. Para coleções sem rascunho (ex.: mídia). */
export const anyone: Access = () => true

/**
 * Público só enxerga documentos publicados; equipe enxerga rascunhos.
 * Usado em toda coleção com versions.drafts.
 */
export const publishedOrStaff: Access = ({ req }) => {
  if (isStaffUser(userOf(req))) return true
  return { _status: { equals: 'published' } }
}

export const adminFieldOnly: FieldAccess = ({ req }) => isAdminUser(userOf(req))
