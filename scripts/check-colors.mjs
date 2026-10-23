#!/usr/bin/env node
/**
 * Falha se a paleta antiga (#091B2D / #8ED2E5) aparecer no código-fonte.
 * A pasta docs/ é ignorada porque cita essas cores como proibidas.
 */
import { readdirSync, readFileSync, statSync } from 'node:fs'
import { join, relative } from 'node:path'

const root = process.cwd()
const forbidden = [/#?091B2D/i, /#?8ED2E5/i, /rgba?\(\s*9\s*,\s*27\s*,\s*45/i, /rgba?\(\s*142\s*,\s*210\s*,\s*229/i]
const ignoreDirs = new Set(['node_modules', '.next', '.git', 'docs', 'media', 'playwright-report', 'test-results', '.lighthouseci', 'backups'])
const exts = /\.(ts|tsx|js|jsx|mjs|cjs|css|scss|json|md|html|svg|yml|yaml)$/i
const selfFiles = new Set(['scripts/check-colors.mjs', 'src/styles/tokens.ts', 'tests/unit/colors.spec.ts'])

const hits = []
const walk = (dir) => {
  for (const name of readdirSync(dir)) {
    if (ignoreDirs.has(name)) continue
    const full = join(dir, name)
    const st = statSync(full)
    if (st.isDirectory()) walk(full)
    else if (exts.test(name)) {
      const rel = relative(root, full)
      if (selfFiles.has(rel) || rel === 'pnpm-lock.yaml') continue
      const lines = readFileSync(full, 'utf8').split('\n')
      lines.forEach((line, i) => {
        if (forbidden.some((re) => re.test(line))) hits.push(`${rel}:${i + 1}: ${line.trim()}`)
      })
    }
  }
}
walk(root)

if (hits.length) {
  console.error('✗ Paleta antiga encontrada (proibida pelo briefing, seção 1.2):')
  for (const h of hits) console.error('  ' + h)
  process.exit(1)
}
console.log('✓ check:colors — nenhuma cor da paleta antiga no código-fonte.')
