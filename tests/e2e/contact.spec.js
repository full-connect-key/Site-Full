import { expect, test } from '@playwright/test'

import { admin, login, trackErrors } from './helpers.js'

// O servidor descarta envios feitos menos de 3 s após abrir a página (antirrobô).
const humanPause = (page) => page.waitForTimeout(3200)

test.describe('Contato', () => {
  test('etapas com foco gerenciado, validação acessível e sucesso só após o servidor @mobile', async ({ page }) => {
    const errors = trackErrors(page)
    await page.goto('/contato')
    const step1 = page.locator('[data-step="1"]')
    const step2 = page.locator('[data-step="2"]')
    await expect(step1).toBeVisible()
    await expect(step2).toBeHidden()

    // Continuar sem assunto: erro anunciado e ligado ao campo.
    await page.getByRole('button', { name: 'Continuar' }).click()
    await expect(page.locator('#cf-subject-error')).toHaveText('Preencha este campo para continuar.')
    await expect(page.locator('input[name="subject"]').first()).toHaveAttribute('aria-invalid', 'true')

    await page.getByRole('radio', { name: /Quero desenvolver um projeto/ }).check()
    await page.getByRole('button', { name: 'Continuar' }).click()
    await expect(step2).toBeVisible()
    await expect(page.locator('#contact-step-2-title')).toBeFocused()

    // Voltar devolve o foco ao assunto escolhido.
    await page.getByRole('button', { name: 'Voltar' }).click()
    await expect(page.locator('input[value="projeto"]')).toBeFocused()
    await page.getByRole('button', { name: 'Continuar' }).click()

    // Envio vazio: erros por campo e foco no primeiro inválido.
    await page.getByRole('button', { name: 'Enviar mensagem' }).click()
    await expect(page.locator('#cf-name')).toBeFocused()
    await expect(page.locator('#cf-name')).toHaveAttribute('aria-invalid', 'true')
    await expect(page.locator('#cf-name')).toHaveAccessibleDescription('Preencha este campo para continuar.')
    await expect(page.locator('#cf-consent-error')).toBeVisible()

    await page.fill('#cf-name', 'Pessoa Teste E2E')
    await page.fill('#cf-email', 'email-invalido')
    await page.fill('#cf-message', 'Mensagem enviada pelo teste automatizado.')
    await page.check('#cf-consent')
    await page.getByRole('button', { name: 'Enviar mensagem' }).click()
    await expect(page.locator('#cf-email')).toBeFocused()
    await expect(page.locator('#cf-email-error')).toHaveText('Confira se o endereço de e-mail está correto.')
    await expect(page.locator('[data-success]')).toBeHidden()

    await page.fill('#cf-email', 'pessoa-teste@localhost.test')
    await humanPause(page)
    await page.getByRole('button', { name: 'Enviar mensagem' }).click()
    await expect(page.locator('[data-success]')).toBeVisible()
    await expect(page.locator('[data-success]')).toBeFocused()
    await expect(page.locator('.contact-success__title')).toHaveText('Sua conexão com a Full começou.')
    expect(errors).toEqual([])
  })

  test('assunto pré-selecionado por ?assunto=imprensa e mensagem gravada no painel', async ({ page, browser }) => {
    const visitor = await browser.newPage()
    await visitor.goto('/contato?assunto=imprensa')
    await expect(visitor.locator('input[value="imprensa"]')).toBeChecked()
    await visitor.getByRole('button', { name: 'Continuar' }).click()
    const name = `Jornalista Teste ${Date.now()}`
    await visitor.fill('#cf-name', name)
    await visitor.fill('#cf-email', 'imprensa-teste@localhost.test')
    await visitor.fill('#cf-message', 'Pedido de entrevista (teste).')
    await visitor.check('#cf-consent')
    await humanPause(visitor)
    await visitor.getByRole('button', { name: 'Enviar mensagem' }).click()
    await expect(visitor.locator('[data-success]')).toBeVisible()
    await visitor.close()

    await login(page, admin)
    await page.goto('/admin/submissions')
    await expect(page.locator('.adm-table')).toContainText(name)
  })

  test('sem JavaScript: as duas etapas aparecem e o envio funciona', async ({ browser }) => {
    const ctx = await browser.newContext({ javaScriptEnabled: false })
    const page = await ctx.newPage()
    await page.goto('/contato')
    await expect(page.locator('[data-step="1"]')).toBeVisible()
    await expect(page.locator('[data-step="2"]')).toBeVisible()
    await expect(page.getByRole('button', { name: 'Continuar' })).toBeHidden()
    await page.locator('input[value="outro"]').check({ force: true })
    await page.fill('#cf-name', 'Pessoa Sem JS')
    await page.fill('#cf-email', 'sem-js@localhost.test')
    await page.fill('#cf-message', 'Envio sem JavaScript (teste).')
    await page.check('#cf-consent')
    await humanPause(page)
    await page.getByRole('button', { name: 'Enviar mensagem' }).click()
    await expect(page).toHaveURL(/contato=sucesso/)
    await expect(page.locator('.contact-success__title')).toBeVisible()
    await ctx.close()
  })

  test('honeypot preenchido não grava a mensagem', async ({ page, request }) => {
    await page.goto('/contato')
    const token = await page.locator('input[name="_token"]').inputValue()
    await humanPause(page)
    const res = await request.post('/contato/enviar', {
      headers: { Accept: 'application/json' },
      form: { _token: token, website: 'http://spam.test', subject: 'outro', name: 'Robô Spam', email: 'robo@spam.test', message: 'spam', consent: '1' },
    })
    expect((await res.json()).ok).toBe(true)
    await login(page, admin)
    await page.goto('/admin/submissions')
    await expect(page.locator('.adm-table')).not.toContainText('Robô Spam')
  })
})
