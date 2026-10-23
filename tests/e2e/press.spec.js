import { expect, test } from '@playwright/test'

import { admin, login, trackErrors } from './helpers.js'

test.describe('Full na Mídia', () => {
  test('faixa imersiva: expande no cursor e no foco, setas navegam, links externos seguros', async ({ page }) => {
    const errors = trackErrors(page)
    await page.goto('/full-na-midia')
    const items = page.locator('.press-item')
    await expect(items.first()).toHaveClass(/is-active/)

    await items.nth(2).hover()
    await expect(items.nth(2)).toHaveClass(/is-active/)
    await expect(items.first()).not.toHaveClass(/is-active/)

    await items.nth(0).locator('a').focus()
    await expect(items.nth(0)).toHaveClass(/is-active/)
    await page.keyboard.press('ArrowRight')
    await expect(items.nth(1)).toHaveClass(/is-active/)
    await expect(items.nth(1).locator('a')).toBeFocused()

    const link = items.nth(1).locator('a')
    await expect(link).toHaveAttribute('target', '_blank')
    await expect(link).toHaveAttribute('rel', 'noopener noreferrer')
    await expect(link).toHaveAccessibleName(/abre em nova aba/)

    await expect(page.locator('.cta-band a[href*="assunto=imprensa"]')).toHaveCount(1)
    expect(errors).toEqual([])
  })

  test('faixa no toque: botões anterior/próximo @mobile', async ({ page }) => {
    await page.goto('/full-na-midia')
    await page.locator('[data-strip-next]').click()
    await expect(page.locator('.press-item').nth(1)).toHaveClass(/is-active/)
  })

  test('arquivo e filtros por tipo aparecem acima do limiar e somem abaixo', async ({ page, browser }) => {
    const anon = await browser.newPage()
    await anon.goto('/full-na-midia')
    await expect(anon.locator('#arquivo')).toBeVisible()
    const rows = anon.locator('.press-list__item:visible')
    const total = await rows.count()
    await anon.locator('.press-archive__filters [data-filter="interview"]').click()
    await expect(anon).toHaveURL(/\?tipo=interview/)
    expect(await rows.count()).toBeLessThan(total)

    // Limiar acima do volume → sem arquivo nem filtros.
    await login(page, admin)
    await page.goto('/admin/entries/page')
    await page.locator('.adm-table').getByRole('link', { name: 'Full na Mídia', exact: true }).click()
    const field = page.locator('input[name="fck[archive][threshold]"]')
    const original = await field.inputValue()
    await field.fill('50')
    await page.getByRole('button', { name: /Publicar/ }).click()
    await expect(page.locator('.adm-notice--success')).toContainText('Publicado')
    await anon.goto('/full-na-midia')
    await expect(anon.locator('#arquivo')).toHaveCount(0)
    await expect(anon.locator('.press-item')).toHaveCount(8)

    await page.locator('input[name="fck[archive][threshold]"]').fill(original)
    await page.getByRole('button', { name: /Publicar/ }).click()
    await expect(page.locator('.adm-notice--success')).toContainText('Publicado')
    await anon.close()
  })
})
