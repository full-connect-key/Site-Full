import localFont from 'next/font/local'

/**
 * Tipografia PROVISÓRIA (OFL) — confirmar com a marca (docs/CONTENT_GAPS.md).
 * Trocar a fonte oficial aqui atualiza o site inteiro (tokens --font-display / --font-sans).
 */
export const displayFont = localFont({
  src: [{ path: './fonts/BricolageGrotesque-latin-variable.woff2', weight: '200 800', style: 'normal' }],
  variable: '--font-display-family',
  display: 'swap',
  preload: true,
  fallback: ['Arial Narrow', 'system-ui', 'sans-serif'],
})

export const textFont = localFont({
  src: [
    { path: './fonts/Inter-latin-variable.woff2', weight: '100 900', style: 'normal' },
    { path: './fonts/Inter-latin-variable-italic.woff2', weight: '100 900', style: 'italic' },
  ],
  variable: '--font-text-family',
  display: 'swap',
  preload: true,
  fallback: ['system-ui', '-apple-system', 'Segoe UI', 'sans-serif'],
})
