import { expect } from '@playwright/test'

export const admin = { email: 'e2e-admin@localhost.test', password: 'e2e-admin-pass-123' }
export const editor = { email: 'e2e-editor@localhost.test', password: 'e2e-editor-pass-123' }

export async function login(page, user) {
  await page.goto('/admin/login')
  await page.fill('#email', user.email)
  await page.fill('#password', user.password)
  await page.click('button[type=submit]')
  await expect(page).toHaveURL(/\/admin\/?$/)
}

/** Coleta erros de console/página para garantir "sem erros no console". */
export function trackErrors(page) {
  const errors = []
  page.on('pageerror', (e) => errors.push(e.message))
  page.on('console', (m) => m.type() === 'error' && errors.push(m.text()))
  return errors
}
