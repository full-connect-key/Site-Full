import { expect, test } from '@playwright/test'

const routes = ['/', '/sm', '/ecossistema', '/ecossistema/fullhub', '/ecossistema/fullmix', '/ecossistema/fulldash', '/ecossistema/fullmatch', '/cases', '/cases/case-teste-1', '/insights', '/insights/artigo-teste-1', '/full-na-midia', '/contato']

test.describe('Responsivo e toque', () => {
  for (const width of [320, 1920]) {
    test(`sem overflow horizontal em ${width}px`, async ({ browser }) => {
      const page = await browser.newPage({ viewport: { width, height: 900 } })
      for (const path of routes) {
        await page.goto(path)
        const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth)
        expect(overflow, `${path} @${width}`).toBeLessThanOrEqual(0)
      }
      await page.close()
    })
  }

  test('alvos de toque com pelo menos 44px (fora de links dentro de texto) @mobile-only @mobile', async ({ page }) => {
    const small = []
    for (const path of routes) {
      await page.goto(path)
      const found = await page.evaluate(() => {
        const out = []
        const els = document.querySelectorAll('a[href], button, input:not([type=hidden]), select, textarea, [role=tab]')
        for (const el of els) {
          if (el.closest('.prose, .check__label, .contact-form__hp, .skip-link, [hidden], [aria-hidden="true"]')) continue
          const r = el.getBoundingClientRect()
          if (!r.width || !r.height || getComputedStyle(el).visibility === 'hidden') continue
          // Radios/checkbox contam a área do rótulo clicável.
          const target = el.matches('input[type=radio], input[type=checkbox]') ? (el.closest('label') || el.parentElement).getBoundingClientRect() : r
          if (target.width < 44 && target.height < 44) out.push(`${el.tagName.toLowerCase()}.${el.className} "${(el.textContent || el.getAttribute('aria-label') || '').trim().slice(0, 30)}" ${Math.round(target.width)}x${Math.round(target.height)}`)
          else if (Math.min(target.width, target.height) < 24) out.push(`${el.tagName.toLowerCase()}.${el.className} "${(el.textContent || '').trim().slice(0, 30)}" ${Math.round(target.width)}x${Math.round(target.height)}`)
        }
        return out
      })
      small.push(...found.map((f) => `${path}: ${f}`))
    }
    expect([...new Set(small)]).toEqual([])
  })
})
