import { expect, test } from '@playwright/test'

test.describe('Segurança', () => {
  for (const path of ['/app/config.php', '/app/bootstrap.php', '/storage/', '/docs/BRIEFING.md', '/scripts/fck.php', '/tests/e2e/helpers.js', '/.git/config']) {
    test(`bloqueia ${path}`, async ({ request }) => {
      expect((await request.get(path)).status()).toBe(403)
    })
  }

  test('cabeçalhos de segurança no site', async ({ request }) => {
    const h = (await request.get('/')).headers()
    expect(h['content-security-policy']).toContain("default-src 'self'")
    expect(h['x-content-type-options']).toBe('nosniff')
    expect(h['x-frame-options']).toBe('SAMEORIGIN')
  })

  test('POST no painel sem CSRF é recusado', async ({ request }) => {
    const res = await request.post('/admin/login', { form: { email: 'x@y.z', password: 'abc' } })
    expect(res.status()).toBe(419)
  })
})
