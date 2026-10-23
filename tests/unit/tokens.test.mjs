/**
 * Tokens: o CSS usa exatamente os valores de tokens.json e todos os pares
 * texto/fundo usados no site atingem WCAG 2.2 AA.
 */
import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import { test } from 'node:test'

const theme = new URL('../../', import.meta.url)
const tokens = JSON.parse(readFileSync(new URL('assets/tokens.json', theme), 'utf8'))
const css = readFileSync(new URL('assets/css/main.css', theme), 'utf8')

const channel = (c) => {
  const s = c / 255
  return s <= 0.03928 ? s / 12.92 : ((s + 0.055) / 1.055) ** 2.4
}
const lum = (hex) => {
  const v = hex.replace('#', '')
  const [r, g, b] = [0, 2, 4].map((i) => parseInt(v.slice(i, i + 2), 16))
  return 0.2126 * channel(r) + 0.7152 * channel(g) + 0.0722 * channel(b)
}
const ratio = (a, b) => {
  const [x, y] = [lum(a), lum(b)].sort((m, n) => n - m)
  return (x + 0.05) / (y + 0.05)
}
const cssVar = (name) => {
  const m = css.match(new RegExp(`--${name}:\\s*(#[0-9a-fA-F]{6})`))
  return m ? m[1].toUpperCase() : null
}

test('CSS espelha tokens.json', () => {
  const map = {
    'brand-cyan': tokens.brand.cyan, 'brand-cyan-dark': tokens.brand.cyanDark,
    'hero-black': tokens.neutral.heroBlack, 'hero-black-alt': tokens.neutral.heroBlackAlt,
    graphite: tokens.neutral.graphite, 'dark-card': tokens.neutral.darkCard, 'dark-border': tokens.neutral.darkBorder,
    'dark-text-2': tokens.neutral.darkText2, 'dark-text-3': tokens.neutral.darkText3, paper: tokens.neutral.paper,
    white: tokens.neutral.white, 'light-text-2': tokens.neutral.lightText2, 'light-text-3': tokens.neutral.lightText3,
    'light-border': tokens.neutral.lightBorder,
  }
  for (const [k, p] of Object.entries(tokens.products)) {
    map[k] = p.official
    map[`${k}-on-dark`] = p.onDark
    map[`${k}-on-light`] = p.onLight
  }
  for (const [name, value] of Object.entries(map)) assert.equal(cssVar(name), value.toUpperCase(), `--${name}`)
})

test('logoCyan não é usado na UI', () => {
  assert.ok(!css.toUpperCase().includes(tokens.brand.logoCyan.toUpperCase()))
})

test('pares texto/fundo atingem WCAG 2.2 AA', () => {
  const n = tokens.neutral, b = tokens.brand
  const pairs = [
    [n.paper, n.graphite], [n.paper, n.heroBlack], [n.paper, n.darkCard],
    [n.darkText2, n.graphite], [n.darkText2, n.darkCard], [n.darkText3, n.graphite], [n.darkText3, n.heroBlack], [n.darkText3, n.darkCard],
    [b.cyan, n.graphite], [b.cyan, n.heroBlack], [b.cyan, n.darkCard], [n.graphite, b.cyan],
    [n.graphite, n.paper], [n.graphite, n.white], [n.lightText2, n.paper], [n.lightText2, n.white],
    [n.lightText3, n.paper], [n.lightText3, n.white], [b.cyanDark, n.paper], [b.cyanDark, n.white], [n.white, b.cyanDark],
  ]
  for (const p of Object.values(tokens.products)) {
    pairs.push([p.onDark, n.graphite], [p.onDark, n.darkCard], [p.onDark, n.heroBlack], [p.onLight, n.paper], [p.onLight, n.white])
  }
  // Texto sobre botão na cor oficial do produto (--product-ink).
  pairs.push([n.white, tokens.products.hub.official])
  for (const k of ['dash', 'leads', 'mix', 'match']) pairs.push([n.heroBlack, tokens.products[k].official])
  for (const [fg, bg] of pairs) assert.ok(ratio(fg, bg) >= 4.5, `${fg} sobre ${bg}: ${ratio(fg, bg).toFixed(2)}`)
})

test('ciano da marca nunca é texto sobre fundo claro', () => {
  assert.ok(ratio(tokens.brand.cyan, tokens.neutral.paper) < 4.5)
  const light = css.split("[data-theme='light'] {")[1]?.split('}')[0] ?? ''
  assert.match(light, /--accent:\s*var\(--brand-cyan-dark\)/)
})
