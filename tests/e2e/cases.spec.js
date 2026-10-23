import { expect, test } from '@playwright/test'

import { admin, login, trackErrors } from './helpers.js'

test.describe('Cases', () => {
  test('listagem: filtro por setor reflete na URL, funciona sem recarregar e volta com o histórico @mobile', async ({ page }) => {
    const errors = trackErrors(page)
    await page.goto('/cases')
    const items = page.locator('.case-grid__item:visible')
    const total = await items.count()
    expect(total).toBeGreaterThan(2)

    const chip = page.locator('.cases-list__filters [data-filter]').nth(1)
    const slug = await chip.getAttribute('data-filter')
    await chip.click()
    await expect(page).toHaveURL(new RegExp(`\\?setor=${slug}$`))
    await expect(chip).toHaveAttribute('aria-current', 'true')
    const filtered = await items.count()
    expect(filtered).toBeLessThan(total)
    for (const s of await items.evaluateAll((els) => els.map((e) => e.dataset.key))) expect(s).toBe(slug)

    await page.goBack()
    await expect(page).toHaveURL(/\/cases$/)
    await expect(items).toHaveCount(total)

    // Sem JS (link direto) o servidor aplica o mesmo filtro.
    await page.goto(`/cases?setor=${slug}`)
    await expect(items).toHaveCount(filtered)
    expect(errors).toEqual([])
  })

  test('listagem: nome e categoria do case acessíveis no card', async ({ page }) => {
    await page.goto('/cases')
    const card = page.locator('.case-card').first()
    await expect(card).toHaveAccessibleName(/Case Teste 1/)
    await card.focus()
    await expect(card.locator('.case-card__info')).toBeVisible()
  })

  test('página do case: KV, narrativa, galeria, resultados e próximo case cíclico', async ({ page }) => {
    const errors = trackErrors(page)
    await page.goto('/cases/case-teste-1')
    await expect(page.locator('h1')).toHaveText('Case Teste 1')
    await expect(page.locator('.case-kv__headline')).toContainText('Headline conceitual')
    await expect(page.locator('.case-story__label')).toHaveText(['O desafio', 'A ideia', 'Nossa atuação'])
    await expect(page.locator('.case-gallery__item')).toHaveCount(3)
    await expect(page.locator('.case-results .metrics__item')).toHaveCount(1)
    await expect(page.locator('.case-next__title')).toHaveText('Case Teste 2')

    // Último case: próximo volta ao primeiro; sem resultados, o bloco não existe.
    await page.goto('/cases/case-teste-5')
    await expect(page.locator('.case-results')).toHaveCount(0)
    await expect(page.locator('.case-next__title')).toHaveText('Case Teste 1')
    await page.locator('.case-next__link').click()
    await expect(page).toHaveURL(/\/cases\/case-teste-1$/)
    expect(errors).toEqual([])
  })

  test('publicar um case: aparece na listagem, no filtro, na rota e no sitemap', async ({ page, browser }) => {
    const name = `Case Publicado ${Date.now()}`
    await login(page, admin)
    await page.goto('/admin/entries/case/new')
    await page.fill('#title', name)
    await page.getByRole('button', { name: /Criar|Adicionar|Continuar/ }).click()
    await expect(page).toHaveURL(/\/admin\/edit\/\d+/)
    const sector = page.locator('select[name="fck[details][sector]"]')
    const sectorLabel = (await sector.locator('option').nth(1).textContent()).trim()
    await sector.selectOption({ index: 1 })
    await page.fill('input[name="fck[details][category]"]', 'Categoria publicada')
    const slug = await page.locator('#slug').inputValue()

    const anon = await browser.newPage()
    expect((await anon.goto(`/cases/${slug}`)).status()).toBe(404)

    await page.getByRole('button', { name: /Publicar/ }).click()
    await expect(page.locator('.adm-notice--success')).toContainText('Publicado')

    expect((await anon.goto(`/cases/${slug}`)).status()).toBe(200)
    await expect(anon.locator('h1')).toHaveText(name)
    await anon.goto('/cases')
    await expect(anon.locator('.case-card', { hasText: name })).toBeVisible()
    await anon.locator('.cases-list__filters [data-filter]', { hasText: sectorLabel }).click()
    await expect(anon.locator('.case-card', { hasText: name })).toBeVisible()
    const sitemap = await (await anon.request.get('/sitemap.xml')).text()
    expect(sitemap).toContain(`/cases/${slug}`)

    // Limpa: volta a rascunho para não afetar outros testes.
    page.once('dialog', (d) => d.accept())
    await page.getByRole('button', { name: 'Despublicar' }).click()
    await anon.close()
  })
})
