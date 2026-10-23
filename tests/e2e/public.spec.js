import AxeBuilder from '@axe-core/playwright'
import { expect, test } from '@playwright/test'

import { trackErrors } from './helpers.js'

const pages = ['/', '/sm', '/ecossistema', '/ecossistema/full-hub', '/ecossistema/full-dash', '/ecossistema/full-leads', '/ecossistema/fullmix', '/ecossistema/fullmatch', '/cases', '/cases/case-teste-1', '/insights', '/insights/artigo-teste-1', '/full-na-midia', '/contato']

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

test.describe('Ecossistema e rodapé', () => {
  test('ecossistema: seleção por clique e setas troca painel e nó do diagrama @responsive', async ({ page }) => {
    await page.goto('/ecossistema')
    const tabs = page.locator('.eco__tab')
    await expect(tabs).toHaveCount(5)
    await tabs.nth(2).click()
    await expect(tabs.nth(2)).toHaveAttribute('aria-selected', 'true')
    await expect(page.locator('#eco-panel-2')).toBeVisible()
    await expect(page.locator('[data-eco-node="2"]')).toHaveClass(/is-active/)
    await page.keyboard.press('ArrowDown')
    await expect(tabs.nth(3)).toBeFocused()
    await expect(page.locator('#eco-panel-3')).toBeVisible()
    // S&M é método, não nó do diagrama
    await expect(page.locator('.eco__tab', { hasText: 'S&M' })).toHaveCount(0)
  })

  test('rodapé mostra a versão publicada', async ({ page }) => {
    await page.goto('/')
    await expect(page.locator('.site-footer__version')).toHaveText(/[0-9a-f]{7}/)
  })
})

test.describe('Plataformas', () => {
  test('FullMix: simulador mantém 100% e aviso ilustrativo visível', async ({ page }) => {
    await page.goto('/ecossistema/fullmix')
    await expect(page.locator('.mix-sim__note')).toBeVisible()
    await page.locator('#mix-0').fill('70')
    await expect(page.locator('[data-mix-total]')).toHaveText('100%')
    const sum = await page.$$eval('.mix-sim input[type=range]', (els) => els.reduce((s, e) => s + Number(e.value), 0))
    expect(sum).toBe(100)
  })

  test('Full Dash: seletor por teclado e selo ilustrativo', async ({ page }) => {
    await page.goto('/ecossistema/full-dash')
    const tab = page.locator('.selector__tab').first()
    await tab.focus()
    await page.keyboard.press('ArrowRight')
    await expect(page.locator('#dash-panel-1')).toBeVisible()
    await expect(page.locator('#dash-panel-1 .badge-illustrative')).toBeVisible()
  })

  test('Full Hub e Full Leads: narrativa por rolagem ativa etapas', async ({ page }) => {
    for (const slug of ['full-hub', 'full-leads']) {
      await page.goto('/ecossistema/' + slug)
      const steps = page.locator('[data-story-step]')
      await steps.last().scrollIntoViewIfNeeded()
      await page.mouse.wheel(0, 200)
      await expect(steps.last()).toHaveClass(/is-active/, { timeout: 5000 })
    }
  })

  test('FullMatch: seletor de perfis', async ({ page }) => {
    await page.goto('/ecossistema/fullmatch')
    await page.locator('.selector__tab').nth(2).click()
    await expect(page.locator('#match-panel-2')).toBeVisible()
  })
})
