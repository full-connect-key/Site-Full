#!/usr/bin/env node
/**
 * Falha se a paleta antiga (#091B2D / #8ED2E5) aparecer no código-fonte.
 * A pasta docs/ é ignorada (lá essas cores são citadas como proibidas).
 */
import { readdirSync, readFileSync, statSync } from 'node:fs'
import { join, relative } from 'node:path'

const root = process.cwd()
const forbidden = [/091B2D/i, /8ED2E5/i, /rgba?\(\s*9\s*,\s*27\s*,\s*45\b/i, /rgba?\(\s*142\s*,\s*210\s*,\s*229\b/i]
const ignore = new Set(['node_modules', '.git', 'docs', '.wp-local', 'dist', 'test-results', 'playwright-report'])
const self = new Set(['scripts/check-colors.mjs'])
const hits = []
const walk = (dir) => {
  for (const name of readdirSync(dir)) {
    if (ignore.has(name)) continue
    const full = join(dir, name)
    if (statSync(full).isDirectory()) walk(full)
    else if (/\.(php|css|js|mjs|json|svg|html|md|yml|sh)$/i.test(name)) {
      const rel = relative(root, full)
      if (self.has(rel)) continue
      readFileSync(full, 'utf8').split('\n').forEach((line, i) => {
        if (forbidden.some((re) => re.test(line))) hits.push(`${rel}:${i + 1}: ${line.trim().slice(0, 120)}`)
      })
    }
  }
}
walk(root)
if (hits.length) {
  console.error('✗ Paleta antiga encontrada (proibida — briefing 1.2):\n  ' + hits.join('\n  '))
  process.exit(1)
}
console.log('✓ check:colors — nenhuma cor da paleta antiga no código-fonte.')
