import AxeBuilder from '@axe-core/playwright'
import { expect, test } from '@playwright/test'

import { trackErrors } from './helpers.js'

const pages = ['/', '/sm', '/ecossistema', '/ecossistema/full-hub', '/cases', '/insights', '/full-na-midia', '/contato']

test.describe('Site público', () => {
  for (const path of pages) {
    test(`${path} carrega sem erros e sem violações sérias de acessibilidade @responsive @mobile`, async ({ page }) => {
      const errors = trackErrors(page)
      const res = await page.goto(path)
      expect(res.status()).toBe(200)
      await expect(page.locator('html')).toHaveAttribute('lang', 'pt-BR')
      await expect(page.locator('h1')).toHaveCount(1)
      const overflow = await page.evaluate(() => document.documentElement.scrollWidth > window.innerWidth)
      expect(overflow, 'overflow horizontal').toBe(false)
      // Axe na versão estática (sem transições em andamento, que geram falsos positivos de contraste).
      await page.emulateMedia({ reducedMotion: 'reduce' })
      await page.reload()
      const axe = await new AxeBuilder({ page }).analyze()
      const serious = axe.violations.filter((v) => ['serious', 'critical'].includes(v.impact))
      expect(serious.map((v) => `${v.id}: ${v.help}`)).toEqual([])
      expect(errors).toEqual([])
    })
  }

  test('rascunho (política) não é público e staging é noindex', async ({ page, request }) => {
    expect((await request.get('/politica-de-privacidade')).status()).toBe(404)
    await page.goto('/')
    await expect(page.locator('meta[name=robots]')).toHaveAttribute('content', /noindex/)
    expect(await (await request.get('/robots.txt')).text()).toContain('Disallow: /')
  })

  test('404 com textos do painel', async ({ page }) => {
    const res = await page.goto('/pagina-que-nao-existe')
    expect(res.status()).toBe(404)
    await expect(page.locator('h1')).toBeVisible()
  })

  test('setores: seleção acessível com aria-pressed e desseleção', async ({ page }) => {
    await page.goto('/')
    const btn = page.locator('[data-sector]').first()
    await btn.scrollIntoViewIfNeeded()
    await btn.click()
    await expect(btn).toHaveAttribute('aria-pressed', 'true')
    await expect(page.locator('[data-sector-intro]')).toBeHidden()
    await btn.click()
    await expect(btn).toHaveAttribute('aria-pressed', 'false')
    await expect(page.locator('[data-sector-intro]')).toBeVisible()
  })

  test('menu mobile abre, prende o foco e fecha com Esc @mobile-only @mobile', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 800 })
    await page.goto('/')
    const toggle = page.locator('[data-menu-toggle]')
    await toggle.click()
    await expect(page.locator('#mobile-menu')).toBeVisible()
    await page.keyboard.press('Escape')
    await expect(page.locator('#mobile-menu')).toBeHidden()
    await expect(toggle).toBeFocused()
  })

  test('submenu do Ecossistema acessível por teclado', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 })
    await page.goto('/')
    const toggle = page.locator('.main-nav__toggle').first()
    await toggle.focus()
    await page.keyboard.press('Enter')
    await expect(toggle).toHaveAttribute('aria-expanded', 'true')
    await expect(page.locator('#submenu-1')).toBeVisible()
    await page.keyboard.press('Escape')
    await expect(toggle).toHaveAttribute('aria-expanded', 'false')
  })

  test('carrossel de depoimentos: próximo e pausa', async ({ page }) => {
    await page.goto('/')
    const slider = page.locator('.slider')
    await slider.scrollIntoViewIfNeeded()
    await page.locator('[data-slider-next]').click()
    await expect(page.locator('[data-slide-to="1"]')).toHaveAttribute('aria-current', 'true')
    await page.locator('[data-slider-pause]').click()
    await expect(page.locator('[data-slider-pause]')).toHaveAttribute('aria-pressed', 'true')
  })

  test('movimento reduzido: S&M empilhado e legível', async ({ browser }) => {
    const ctx = await browser.newContext({ reducedMotion: 'reduce' })
    const page = await ctx.newPage()
    await page.goto('/sm')
    await expect(page.locator('.method')).toHaveClass(/is-static/)
    for (const panel of await page.locator('.method__panel').all()) await expect(panel).toBeVisible()
    await ctx.close()
  })
})
