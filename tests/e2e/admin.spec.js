import { expect, test } from '@playwright/test'

import { admin, editor, login } from './helpers.js'

test.describe('Painel', () => {
  test('login inválido não entra', async ({ page }) => {
    await page.goto('/admin/login')
    await page.fill('#email', admin.email)
    await page.fill('#password', 'senha-errada-123')
    await page.click('button[type=submit]')
    await expect(page.getByRole('alert')).toBeVisible()
    await expect(page).toHaveURL(/\/admin\/login/)
  })

  test('painel exige login e é noindex', async ({ page, request }) => {
    await page.goto('/admin/entries/page')
    await expect(page).toHaveURL(/\/admin\/login/)
    const res = await request.get('/admin/login')
    expect(res.headers()['x-robots-tag']).toContain('noindex')
  })

  test('rascunho é privado; preview mostra; publicar altera o site; histórico restaura', async ({ page, browser }) => {
    await login(page, admin)
    await page.goto('/admin/entries/page')
    await page.locator('.adm-table').getByRole('link', { name: 'Home', exact: true }).click()
    const editUrl = page.url()
    const field = page.locator('input[name="fck[hero][headline]"]')
    const original = await field.inputValue()
    const draft = `Título rascunho ${Date.now()}`
    await page.getByRole('link', { name: 'Histórico de versões' }).click()
    const before = await page.locator('.adm-table tbody tr').count()
    await page.goto(editUrl)

    await field.fill(draft)
    await page.getByRole('button', { name: 'Salvar rascunho' }).click()
    await expect(page.locator('.adm-notice--success')).toContainText('Rascunho salvo')

    // Visitante anônimo não vê o rascunho
    const anon = await browser.newPage()
    await anon.goto('/')
    await expect(anon.locator('h1')).not.toContainText(draft)

    // Preview autenticado mostra o rascunho
    await page.goto('/?preview=1')
    await expect(page.locator('h1')).toContainText(draft)

    // Publicar
    await page.goto(editUrl)
    await page.getByRole('button', { name: /Publicar/ }).click()
    await expect(page.locator('.adm-notice--success')).toContainText('Publicado')
    await anon.reload()
    await expect(anon.locator('h1')).toContainText(draft)

    // Restaurar versão anterior e publicar de novo
    await page.getByRole('link', { name: 'Histórico de versões' }).click()
    // A versão que era a atual no início do teste está "before" linhas acima do fim.
    const after = await page.locator('.adm-table tbody tr').count()
    await page.locator('.adm-table tbody tr').nth(after - before).getByRole('button', { name: 'Restaurar esta versão' }).click()
    await expect(page.locator('input[name="fck[hero][headline]"]')).toHaveValue(original)
    await page.getByRole('button', { name: /Publicar/ }).click()
    await anon.reload()
    await expect(anon.locator('h1')).toContainText(original)
    await anon.close()
  })

  test('texto acima do limite não é salvo (sem truncar)', async ({ page }) => {
    await login(page, admin)
    await page.goto('/admin/entries/page')
    await page.locator('.adm-table').getByRole('link', { name: 'Home', exact: true }).click()
    const field = page.locator('input[name="fck[hero][headline]"]')
    const original = await field.inputValue()
    await field.evaluate((el) => el.removeAttribute('maxlength'))
    await field.fill('x'.repeat(200))
    await page.getByRole('button', { name: 'Salvar rascunho' }).click()
    await expect(page.locator('.adm-notice--error')).toContainText('passou de 120 caracteres')
    await expect(page.locator('input[name="fck[hero][headline]"]')).toHaveValue(original)
  })

  test('trocar slug publicado cria redirect 301', async ({ page, request }) => {
    await login(page, admin)
    await page.goto('/admin/entries/page')
    await page.locator('.adm-table').getByRole('link', { name: 'Full na Mídia', exact: true }).click()
    await page.fill('#slug', 'na-midia')
    await page.getByRole('button', { name: /Publicar/ }).click()
    const res = await request.get('/full-na-midia', { maxRedirects: 0 })
    expect(res.status()).toBe(301)
    expect(res.headers().location).toMatch(/\/na-midia$/)
    // volta ao slug original
    await page.fill('#slug', 'full-na-midia')
    await page.getByRole('button', { name: /Publicar/ }).click()
    expect((await request.get('/full-na-midia')).status()).toBe(200)
  })

  test('upload de imagem na biblioteca', async ({ page }) => {
    await login(page, admin)
    await page.goto('/admin/media')
    // PNG 1x1 válido
    const png = Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==', 'base64')
    await page.setInputFiles('input[type=file][name="files[]"]', { name: 'teste-upload.png', mimeType: 'image/png', buffer: png })
    await expect(page.locator('.adm-notice--success')).toContainText('enviado')
    await expect(page.locator('.adm-media-grid')).toContainText('teste-upload')
  })

  test('arquivo PHP disfarçado é recusado no upload', async ({ page }) => {
    await login(page, admin)
    await page.goto('/admin/media')
    await page.setInputFiles('input[type=file][name="files[]"]', { name: 'shell.png', mimeType: 'image/png', buffer: Buffer.from('<?php echo 1; ?>') })
    await expect(page.locator('.adm-notice--error')).toContainText('não permitido')
  })

  test('editor não acessa usuários, mensagens nem destinatários', async ({ page }) => {
    await login(page, editor)
    for (const path of ['/admin/users', '/admin/submissions', '/admin/contact-settings', '/admin/tools']) {
      const res = await page.goto(path)
      expect(res.status(), path).toBe(403)
    }
    await page.goto('/admin')
    await expect(page.locator('.adm-side')).not.toContainText('Usuários')
  })
})
