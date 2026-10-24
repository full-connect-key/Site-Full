/**
 * Sprint ECO (docs/BRIEFING_ECOSSISTEMA.md): estrutura de 7 blocos, nomes por
 * token, claims, contato com plataforma, motion guiado pela rolagem.
 */
import { spawn } from 'node:child_process'

import { expect, test } from '@playwright/test'

import { admin, login, trackErrors } from './helpers.js'

const platforms = [
  { slug: 'fullmix', name: 'FullMix', verb: 'Planejar' },
  { slug: 'fulldash', name: 'FullDash', verb: 'Otimizar' },
  { slug: 'fullleads', name: 'FullLeads', verb: 'Converter' },
  { slug: 'fullmatch', name: 'FullMatch', verb: 'Fidelizar' },
  { slug: 'fullhub', name: 'FullHub', verb: 'Organizar' },
]

/** Rola só com a roda do mouse (sem clicar nem passar o mouse) até o fim da cena. */
async function scrollThroughScene(page) {
  const seen = new Set()
  const scene = page.locator('.scene')
  await page.evaluate(() => {
    const r = document.querySelector('.scene__track').getBoundingClientRect()
    scrollTo({ top: scrollY + r.top - 10, behavior: 'instant' })
  })
  for (let i = 0; i < 60; i++) {
    await page.mouse.wheel(0, 140)
    await page.waitForTimeout(40)
    seen.add(await scene.getAttribute('data-step'))
    const done = await page.evaluate(() => document.querySelector('.scene__track').getBoundingClientRect().bottom < innerHeight)
    if (done) break
  }
  return seen
}

test.describe('Ecossistema — estrutura e conteúdo', () => {
  for (const p of platforms) {
    test(`${p.name}: 7 blocos na ordem, Interface omitida sem material real`, async ({ page }) => {
      const errors = trackErrors(page)
      await page.emulateMedia({ reducedMotion: 'reduce' })
      const res = await page.goto(`/ecossistema/${p.slug}`)
      expect(res.status()).toBe(200)
      await expect(page.locator('.page-hero__eyebrow')).toHaveText(`${p.name} · ${p.verb}`)
      const order = await page.$$eval('.platform > section', (els) => els.map((e) => e.className.match(/(page-hero|platform-\w+|cta-band)/)[1]))
      expect(order).toEqual(['page-hero', 'platform-problem', 'platform-how', 'platform-benefits', 'platform-connections', 'cta-band'])
      await expect(page.locator('.platform-interface')).toHaveCount(0)
      await expect(page.locator('.page-hero a[href="#como-funciona"]')).toBeVisible()
      await expect(page.locator('.cta-band a.btn')).toHaveAttribute('href', new RegExp(`/contato\\?assunto=projeto&plataforma=${p.slug}$`))
      const html = await page.content()
      expect(html).not.toMatch(/\[VALIDAR|\[PENDENTE|\{platform\}/)
      expect(errors).toEqual([])
    })
  }

  test('Home e Ecossistema: ordem da jornada, verbos, cliente no centro e nota de independência', async ({ page }) => {
    await page.goto('/')
    await expect(page.locator('.eco-home__verb')).toHaveText(platforms.map((p) => p.verb))
    await expect(page.locator('.hero__title')).toContainText('Ative o modo Full.')
    await page.goto('/ecossistema')
    await expect(page.locator('.eco__tab-name')).toHaveText(platforms.map((p) => p.name))
    await expect(page.locator('h1')).toHaveText('Cinco soluções. Infinitas possibilidades de conexão.')
    await expect(page.locator('.page-hero__alt')).toHaveText('Seu negócio no centro. Todas as possibilidades ao redor.')
    await expect(page.locator('.eco__center')).toBeVisible()
    await expect(page.locator('.eco__independence')).toContainText('contratada de forma independente')
    await expect(page.locator('#eco-panel-0 .eco__more')).toHaveText(/Explore o FullMix/)
    const html = await page.content()
    expect(html).not.toMatch(/\[VALIDAR|\[PENDENTE|\{platform\}/)
  })

  test('slugs da v1.1 redirecionam com 301 quando existirem; novos respondem 200', async ({ request }) => {
    for (const p of platforms) expect((await request.get(`/ecossistema/${p.slug}`)).status()).toBe(200)
    // No banco de teste (instalação nova) os slugs antigos nunca existiram: o 301 é coberto por npm run test:migration.
    expect((await request.get('/ecossistema/full-dash', { maxRedirects: 0 })).status()).toBe(404)
  })

  test('FullMix: aviso ilustrativo visível junto ao simulador e total de 100%', async ({ page }) => {
    await page.goto('/ecossistema/fullmix')
    await expect(page.locator('.mix-sim__note')).toBeVisible()
    await expect(page.locator('.mix-sim__note')).toContainText('Esta experiência é ilustrativa')
    await page.locator('#mix-0').fill('70')
    await expect(page.locator('[data-mix-total]')).toHaveText('100%')
  })

  test('CTA final pré-seleciona assunto e plataforma no contato, e a mensagem registra a plataforma', async ({ page, browser }) => {
    await page.goto('/ecossistema/fulldash')
    await page.locator('.cta-band a.btn').click()
    await expect(page).toHaveURL(/assunto=projeto&plataforma=fulldash/)
    await expect(page.locator('input[value="projeto"]')).toBeChecked()
    await expect(page.locator('input[name="plataforma"]')).toHaveValue('fulldash')
    await page.getByRole('button', { name: 'Continuar' }).click()
    const name = `Origem FullDash ${Date.now()}`
    await page.fill('#cf-name', name)
    await page.fill('#cf-email', 'origem@localhost.test')
    await page.fill('#cf-message', 'Teste de origem da plataforma.')
    await page.check('#cf-consent')
    await page.waitForTimeout(3200)
    await page.getByRole('button', { name: 'Enviar mensagem' }).click()
    await expect(page.locator('[data-success]')).toBeVisible()
    const staff = await browser.newPage()
    await login(staff, admin)
    await staff.goto('/admin/submissions')
    await expect(staff.locator('tr', { hasText: name })).toContainText('FullDash')
    await staff.close()
  })

  test('trocar o nome no painel atualiza card, página, CTA e título de SEO', async ({ page, browser }) => {
    await login(page, admin)
    await page.goto('/admin/entries/platform')
    await page.locator('.adm-table').getByRole('link', { name: 'FullDash', exact: true }).click()
    const edit = page.url()
    await page.fill('#title', 'FullDash Novo')
    await page.getByRole('button', { name: /Publicar/ }).click()
    await expect(page.locator('.adm-notice--success')).toContainText('Publicado')
    const anon = await browser.newPage()
    await anon.goto('/ecossistema/fulldash')
    await expect(anon.locator('.page-hero__eyebrow')).toHaveText('FullDash Novo · Otimizar')
    await expect(anon.locator('.cta-band a.btn')).toContainText('Conversar sobre o FullDash Novo')
    await expect(anon).toHaveTitle(/^FullDash Novo \|/)
    await anon.goto('/ecossistema')
    await expect(anon.locator('.eco__tab-name').nth(1)).toHaveText('FullDash Novo')
    await anon.close()
    await page.goto(edit)
    await page.fill('#title', 'FullDash')
    await page.getByRole('button', { name: /Publicar/ }).click()
    await expect(page.locator('.adm-notice--success')).toContainText('Publicado')
  })
})

test.describe('Ecossistema — claims', () => {
  test('fora de produção os itens "a validar" aparecem; o selo só no preview autenticado', async ({ page }) => {
    await page.goto('/ecossistema/fulldash')
    await expect(page.locator('.benefit')).toHaveCount(6)
    await expect(page.locator('.claim-badge')).toHaveCount(0)
    await login(page, admin)
    await page.goto('/ecossistema/fulldash?preview=1')
    await expect(page.locator('.claim-badge').first()).toHaveText('A validar')
  })

  test('com CLAIMS_STRICT os itens "a validar" somem e o layout se reorganiza', async ({ browser }) => {
    const port = 8093
    const server = spawn('php', ['-S', `127.0.0.1:${port}`, '-t', '.', 'scripts/router.php'], {
      env: { ...process.env, FCK_DB_NAME: 'fck_site_test', FCK_URL: `http://localhost:${port}`, FCK_UPLOADS: 'uploads-test', FCK_CLAIMS_STRICT: '1' },
      stdio: 'ignore',
    })
    try {
      const page = await browser.newPage()
      for (let i = 0; i < 30; i++) {
        try {
          await page.goto(`http://localhost:${port}/ecossistema/fulldash`)
          break
        } catch {
          await page.waitForTimeout(200)
        }
      }
      await expect(page.locator('.benefit')).toHaveCount(3)
      await expect(page.locator('.benefits')).toHaveClass(/benefits--3/)
      await expect(page.locator('.scene__step')).toHaveCount(1)
      await expect(page.locator('.platform-benefits')).not.toContainText('Inteligência artificial aplicada')
      await page.goto(`http://localhost:${port}/ecossistema/fullmatch`)
      await expect(page.locator('.platform-benefits__note')).toHaveCount(0)
      await page.close()
    } finally {
      server.kill()
    }
  })

  test('checagem de pré-lançamento acusa claims a validar', async () => {
    const code = await new Promise((resolve) => {
      const p = spawn('php', ['scripts/fck.php', 'claims'], { env: { ...process.env, FCK_DB_NAME: 'fck_site_test' }, stdio: 'ignore' })
      p.on('exit', resolve)
    })
    expect(code).toBe(1)
  })
})

test.describe('Ecossistema — motion guiado pela rolagem', () => {
  for (const slug of ['fulldash', 'fullmix', 'fullmatch']) {
    test(`${slug}: só rolando, as etapas 1 → 3 acontecem e o seletor acompanha @mobile`, async ({ page }) => {
      await page.goto(`/ecossistema/${slug}`)
      await expect(page.locator('.scene')).toHaveClass(/is-live/)
      await expect(page.locator('.scene')).toHaveAttribute('data-step', '1')
      const seen = await scrollThroughScene(page)
      expect([...seen]).toEqual(expect.arrayContaining(['1', '2', '3']))
      await expect(page.locator('.scene__tab').nth(2)).toHaveAttribute('aria-current', 'step')
      await expect(page.locator('.scene__step.is-active')).toHaveAttribute('data-scene-step', '3')
    })
  }

  test('FullHub e FullLeads também avançam pela rolagem', async ({ page }) => {
    for (const slug of ['fullhub', 'fullleads']) {
      await page.goto(`/ecossistema/${slug}`)
      const seen = await scrollThroughScene(page)
      expect(seen.size).toBeGreaterThan(1)
      await expect(page.locator('.scene [data-at].is-on').first()).toBeAttached()
    }
  })

  test('clicar no seletor leva a cena até a etapa', async ({ page }) => {
    await page.goto('/ecossistema/fulldash')
    await page.locator('.scene__tab').nth(2).scrollIntoViewIfNeeded()
    await page.locator('.scene__tab').nth(2).click()
    await expect(page.locator('.scene')).toHaveAttribute('data-step', '3', { timeout: 5000 })
  })

  test('"Pular animação" funciona por teclado', async ({ page }) => {
    await page.goto('/ecossistema/fullmix')
    await page.locator('.skip-scene').focus()
    await expect(page.locator('.skip-scene')).toBeVisible()
    await page.keyboard.press('Enter')
    await expect(page).toHaveURL(/#depois-como-funciona$/)
    const below = await page.evaluate(() => document.querySelector('.scene__track').getBoundingClientRect().bottom <= innerHeight + 2)
    expect(below).toBe(true)
  })

  test('FullMix: a demonstração move os sliders com a rolagem e para ao pegar um slider', async ({ page }) => {
    await page.goto('/ecossistema/fullmix')
    const sim = page.locator('.mix-sim')
    await sim.scrollIntoViewIfNeeded()
    const first = await page.locator('#mix-0').inputValue()
    await page.mouse.wheel(0, 250)
    await page.waitForTimeout(300)
    await expect.poll(() => page.locator('#mix-0').inputValue()).not.toBe(first)
    await page.locator('#mix-1').focus()
    await page.keyboard.press('ArrowRight')
    await expect(sim).toHaveAttribute('data-demo', 'off')
    const held = await page.locator('#mix-0').inputValue()
    await page.mouse.wheel(0, 200)
    await page.waitForTimeout(300)
    expect(await page.locator('#mix-0').inputValue()).toBe(held)
  })

  test('FullMatch: tocar num perfil o fixa @mobile', async ({ page }) => {
    await page.goto('/ecossistema/fullmatch')
    await page.emulateMedia({ reducedMotion: 'reduce' })
    await page.reload()
    const profile = page.locator('.match-profile').nth(1)
    await profile.click()
    await expect(profile).toHaveAttribute('aria-pressed', 'true')
    await expect(page.locator('.scene')).toHaveAttribute('data-pin', '1')
  })

  test('movimento reduzido: sem pin, estado final e todos os textos visíveis', async ({ page }) => {
    await page.emulateMedia({ reducedMotion: 'reduce' })
    for (const slug of ['fulldash', 'fullmix', 'fullmatch']) {
      await page.goto(`/ecossistema/${slug}`)
      const scene = page.locator('.scene')
      await expect(scene).toHaveClass(/is-static/)
      await expect(scene).toHaveAttribute('data-step', '3')
      const sticky = await page.$eval('.scene__sticky', (el) => getComputedStyle(el).position)
      expect(sticky).not.toBe('sticky')
      for (const step of await page.locator('.scene__step').all()) await expect(step).toBeVisible()
      await expect(page.locator('.skip-scene')).toHaveCount(1)
    }
  })
})
