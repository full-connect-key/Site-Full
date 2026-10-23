import { expect, test } from '@playwright/test'

import { login } from '../helpers/login'
import { cleanupTestUser, seedTestUser, testAdmin } from '../helpers/seedUser'

test.describe('Painel administrativo', () => {
  test.beforeAll(async () => {
    await seedTestUser(testAdmin)
  })
  test.afterAll(async () => {
    await cleanupTestUser(testAdmin)
  })

  test('admin autentica e acessa o dashboard', async ({ page }) => {
    await login(page, testAdmin)
    await expect(page).toHaveURL(/\/admin\/?$/)
  })

  test('senha errada não autentica', async ({ page }) => {
    await page.goto('/admin/login')
    await page.fill('#field-email', testAdmin.email)
    await page.fill('#field-password', 'senha-incorreta')
    await page.click('button[type="submit"]')
    await expect(page).toHaveURL(/\/admin\/login/)
  })

  test('painel é noindex', async ({ page }) => {
    await page.goto('/admin/login')
    await expect(page.locator('meta[name="robots"]')).toHaveAttribute('content', /noindex/)
  })
})
