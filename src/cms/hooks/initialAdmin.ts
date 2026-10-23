import type { Payload } from 'payload'

/**
 * Cria o primeiro administrador a partir de variáveis de ambiente locais,
 * somente se ainda não existir nenhum usuário. A senha nunca fica no código.
 */
export async function ensureInitialAdmin(payload: Payload): Promise<void> {
  const email = process.env.PAYLOAD_INITIAL_ADMIN_EMAIL
  const password = process.env.PAYLOAD_INITIAL_ADMIN_PASSWORD
  if (!email || !password) return
  try {
    const { totalDocs } = await payload.count({ collection: 'users', overrideAccess: true })
    if (totalDocs > 0) return
    await payload.create({
      collection: 'users',
      data: { email, password, name: 'Administrador', roles: ['admin'] },
      overrideAccess: true,
    })
    payload.logger.info(`Primeiro administrador criado: ${email}`)
  } catch (error) {
    payload.logger.error({ err: error, msg: 'Não foi possível criar o primeiro administrador' })
  }
}
