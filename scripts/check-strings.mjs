#!/usr/bin/env node
/**
 * Auditoria editorial: procura textos fixos nos templates do tema.
 * (app/views — site público; o painel em app/admin é ferramenta interna).
 * Todo texto visível ou lido por leitor de tela deve vir do painel
 * (campos do conteúdo ou "Textos de interface").
 * Detecta: texto entre tags fora de PHP, atributos de texto literais
 * (aria-label, alt, title, placeholder) e strings literais passadas a
 * esc_html/esc_attr/__/_e dentro do PHP.
 */
import { readdirSync, readFileSync, statSync } from 'node:fs'
import { join, relative } from 'node:path'

const root = process.cwd()
const themeDir = join(root, 'app/views')
const allow = new Set()
const hits = []

const files = []
const walk = (dir) => {
  for (const n of readdirSync(dir)) {
    const f = join(dir, n)
    if (statSync(f).isDirectory()) walk(f)
    else if (n.endsWith('.php')) files.push(f)
  }
}
walk(themeDir)

for (const file of files) {
  const rel = relative(themeDir, file)
  if (allow.has(rel)) continue
  const src = readFileSync(file, 'utf8')
  const lineOf = (idx) => src.slice(0, idx).split('\n').length

  // 1) Strings literais com letras em funções de saída de texto.
  const fnRe = /\b(e|ee|esc_html|esc_attr|__|_e)\(\s*(['"])((?:(?!\2).)*\p{L}{2,}(?:(?!\2).)*)\2/gu
  for (const m of src.matchAll(fnRe)) if (!(m[2] === '"' && m[3].includes('$'))) hits.push(`${rel}:${lineOf(m.index)}: ${m[1]}('${m[3]}')`)

  // 2) HTML fora dos blocos PHP.
  let html = ''
  let i = 0
  const positions = []
  const re = /<\?(php|=)[\s\S]*?(\?>|$)/g
  let m
  while ((m = re.exec(src))) {
    html += src.slice(i, m.index)
    positions.push([html.length, i])
    html += ' '.repeat(m[0].length) // preserva posições
    i = m.index + m[0].length
  }
  html += src.slice(i)
  // Código de <script> não é texto editorial.
  html = html.replace(/<script\b[^>]*>[\s\S]*?<\/script>/g, (s) => ' '.repeat(s.length))

  for (const t of html.matchAll(/>([^<>]*\p{L}{2,}[^<>]*)</gu)) {
    const text = t[1].trim()
    if (!text) continue
    hits.push(`${rel}:${lineOf(t.index)}: texto "${text.slice(0, 60)}"`)
  }
  for (const a of html.matchAll(/\b(aria-label|alt|title|placeholder)="([^"]*\p{L}{2,}[^"]*)"/gu)) {
    hits.push(`${rel}:${lineOf(a.index)}: ${a[1]}="${a[2]}"`)
  }
}

if (hits.length) {
  console.error('✗ check:strings — textos fixos encontrados nos templates (devem vir do painel):\n  ' + hits.join('\n  '))
  process.exit(1)
}
console.log(`✓ check:strings — ${files.length} templates sem textos editoriais fixos.`)
