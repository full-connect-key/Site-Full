import { getPayload } from 'payload'

import config from '../../src/payload.config.js'

export const testAdmin = { email: 'e2e-admin@localhost.test', password: 'e2e-admin-pass-123', roles: ['admin'] as const }
export const testEditor = { email: 'e2e-editor@localhost.test', password: 'e2e-editor-pass-123', roles: ['editor'] as const }

type TestUser = typeof testAdmin | typeof testEditor

/** Cria (ou recria) um usuário de teste. Só usado por testes — nunca pelo seed. */
export async function seedTestUser(user: TestUser = testAdmin): Promise<void> {
  const payload = await getPayload({ config })
  await payload.delete({ collection: 'users', where: { email: { equals: user.email } }, overrideAccess: true })
  await payload.create({
    collection: 'users',
    data: { email: user.email, password: user.password, roles: [...user.roles], name: 'Teste' },
    overrideAccess: true,
  })
}

export async function cleanupTestUser(user: TestUser = testAdmin): Promise<void> {
  const payload = await getPayload({ config })
  await payload.delete({ collection: 'users', where: { email: { equals: user.email } }, overrideAccess: true })
}
