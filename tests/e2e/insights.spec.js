import { expect, test } from '@playwright/test'

import { trackErrors } from './helpers.js'

test.describe('Insights', () => {
  test('listagem: destaque com abas e grid sem repetir os artigos do destaque', async ({ page }) => {
    const errors = trackErrors(page)
    await page.goto('/insights')
    await expect(page.locator('.feature__panel:visible .feature__title')).toContainText('Artigo de teste 1')
    await page.locator('.feature__tab').nth(1).click()
    await expect(page.locator('.feature__panel:visible .feature__title')).toContainText('Artigo de teste 2')

    const featured = await page.locator('.feature__tab-title').allTextContents()
    const grid = await page.locator('.post-grid__item:visible .post-card__title').allTextContents()
    expect(grid.length).toBeGreaterThan(0)
    for (const t of grid) expect(featured).not.toContain(t)
    expect(errors).toEqual([])
  })

  test('listagem: filtro por categoria reflete na URL e funciona sem JS @mobile', async ({ page }) => {
    await page.goto('/insights')
    const items = page.locator('.post-grid__item:visible')
    const total = await items.count()
    const chip = page.locator('.insights-grid__filters [data-filter]').nth(1)
    const slug = await chip.getAttribute('data-filter')
    await chip.click()
    await expect(page).toHaveURL(new RegExp(`\\?categoria=${slug}$`))
    const filtered = await items.count()
    expect(filtered).toBeLessThan(total)
    for (const k of await items.evaluateAll((els) => els.map((e) => e.dataset.key))) expect(k).toBe(slug)
    await page.goto(`/insights?categoria=${slug}`)
    await expect(items).toHaveCount(filtered)
  })

  test('artigo: leitura estimada, autor, compartilhar, relacionados e JSON-LD Article', async ({ page, context }) => {
    const errors = trackErrors(page)
    await page.goto('/insights/artigo-teste-1')
    await expect(page.locator('h1')).toContainText('Artigo de teste 1')
    await expect(page.locator('.article-head__meta')).toContainText('min de leitura')
    await expect(page.locator('.article-head__meta')).toContainText('Autoria Teste')
    await expect(page.locator('.prose h2')).toHaveCount(1)

    const linkedin = page.locator('[data-share="linkedin"]')
    await expect(linkedin).toHaveAttribute('href', /linkedin\.com\/sharing\/share-offsite\/\?url=.*artigo-teste-1/)
    await expect(linkedin).toHaveAttribute('rel', 'noopener')
    await expect(page.locator('[data-share="email"]')).toHaveAttribute('href', /^mailto:\?subject=/)

    await context.grantPermissions(['clipboard-read', 'clipboard-write'])
    await page.locator('[data-copy]').click()
    await expect(page.locator('.share__status')).toHaveText('Link copiado.')

    const related = page.locator('.article-related .post-card')
    await expect(related).toHaveCount(3)
    await expect(related.filter({ hasText: 'Artigo de teste 1:' })).toHaveCount(0)

    const ld = await page.locator('script[type="application/ld+json"]').allTextContents()
    const article = ld.map((s) => JSON.parse(s)).flat().find((j) => j['@type'] === 'Article')
    expect(article.headline).toContain('Artigo de teste 1')
    expect(article.author.name).toBe('Autoria Teste')
    expect(article.datePublished).toBeTruthy()
    expect(errors).toEqual([])
  })
})
