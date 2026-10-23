/**
 * Tokens oficiais da marca Full Connect Key — FONTE DE VERDADE.
 * Os mesmos valores estão em src/styles/globals.css (@theme). O teste
 * tests/unit/tokens.spec.ts garante que os dois arquivos não divergem e que
 * todos os pares texto/fundo declarados atingem WCAG 2.2 AA.
 * Não editar sem aprovação da marca. Não expor para edição no CMS.
 */
export const brand = {
  cyan: '#68CCF2', // destaque, botões e links sobre fundo escuro
  logoCyan: '#5FC5EF', // somente nos arquivos oficiais de logo — não usar na UI
  cyanDark: '#1B6F98', // links e destaques sobre fundo claro
} as const

export const neutral = {
  heroBlack: '#05080A',
  heroBlackAlt: '#080B0D',
  graphite: '#0E1113',
  darkCard: '#161B1F',
  darkBorder: '#262D33',
  darkText2: '#C9CFD4',
  darkText3: '#9AA3AB',
  paper: '#F5F6F4',
  white: '#FFFFFF',
  lightText2: '#3F474E',
  lightText3: '#4A525A',
  lightBorder: '#E2E5E1',
} as const

export const products = {
  hub: { name: 'Full Hub', official: '#0267C1', onDark: '#4C9BE8', onLight: '#0267C1' },
  dash: { name: 'Full Dash', official: '#F43156', onDark: '#FF6B85', onLight: '#C8183D' },
  leads: { name: 'Full Leads', official: '#09E85E', onDark: '#09E85E', onLight: '#087A35' },
  mix: { name: 'FullMix', official: '#FFDD21', onDark: '#FFDD21', onLight: '#7A6400' },
  match: { name: 'FullMatch', official: '#FF7E70', onDark: '#FF7E70', onLight: '#B4402F' },
} as const

export type ProductKey = keyof typeof products

export type ContrastPair = {
  name: string
  fg: string
  bg: string
  /** 'text' = 4,5:1; 'large' = texto grande ou componente de UI = 3:1 */
  kind: 'text' | 'large'
}

/** Todos os pares texto/fundo usados no site. */
export const contrastPairs: ContrastPair[] = [
  // Escuro
  { name: 'paper on graphite', fg: neutral.paper, bg: neutral.graphite, kind: 'text' },
  { name: 'paper on heroBlack', fg: neutral.paper, bg: neutral.heroBlack, kind: 'text' },
  { name: 'paper on darkCard', fg: neutral.paper, bg: neutral.darkCard, kind: 'text' },
  { name: 'darkText2 on graphite', fg: neutral.darkText2, bg: neutral.graphite, kind: 'text' },
  { name: 'darkText2 on darkCard', fg: neutral.darkText2, bg: neutral.darkCard, kind: 'text' },
  { name: 'darkText3 on graphite', fg: neutral.darkText3, bg: neutral.graphite, kind: 'text' },
  { name: 'darkText3 on heroBlack', fg: neutral.darkText3, bg: neutral.heroBlack, kind: 'text' },
  { name: 'darkText3 on darkCard', fg: neutral.darkText3, bg: neutral.darkCard, kind: 'text' },
  { name: 'cyan on graphite', fg: brand.cyan, bg: neutral.graphite, kind: 'text' },
  { name: 'cyan on heroBlack', fg: brand.cyan, bg: neutral.heroBlack, kind: 'text' },
  { name: 'cyan on darkCard', fg: brand.cyan, bg: neutral.darkCard, kind: 'text' },
  { name: 'graphite on cyan (botão)', fg: neutral.graphite, bg: brand.cyan, kind: 'text' },
  { name: 'darkBorder vs graphite (UI)', fg: neutral.darkText3, bg: neutral.graphite, kind: 'large' },
  // Claro
  { name: 'graphite on paper', fg: neutral.graphite, bg: neutral.paper, kind: 'text' },
  { name: 'graphite on white', fg: neutral.graphite, bg: neutral.white, kind: 'text' },
  { name: 'lightText2 on paper', fg: neutral.lightText2, bg: neutral.paper, kind: 'text' },
  { name: 'lightText2 on white', fg: neutral.lightText2, bg: neutral.white, kind: 'text' },
  { name: 'lightText3 on paper', fg: neutral.lightText3, bg: neutral.paper, kind: 'text' },
  { name: 'lightText3 on white', fg: neutral.lightText3, bg: neutral.white, kind: 'text' },
  { name: 'cyanDark on paper', fg: brand.cyanDark, bg: neutral.paper, kind: 'text' },
  { name: 'cyanDark on white', fg: brand.cyanDark, bg: neutral.white, kind: 'text' },
  { name: 'paper on cyanDark (botão claro)', fg: neutral.white, bg: brand.cyanDark, kind: 'text' },
  // Produtos — texto
  ...(Object.entries(products) as [ProductKey, (typeof products)[ProductKey]][]).flatMap(([key, p]) => [
    { name: `${key} onDark on graphite`, fg: p.onDark, bg: neutral.graphite, kind: 'text' as const },
    { name: `${key} onDark on darkCard`, fg: p.onDark, bg: neutral.darkCard, kind: 'text' as const },
    { name: `${key} onLight on paper`, fg: p.onLight, bg: neutral.paper, kind: 'text' as const },
    { name: `${key} onLight on white`, fg: p.onLight, bg: neutral.white, kind: 'text' as const },
  ]),
]

/** Cores proibidas (paleta antiga). Verificadas por scripts/check-colors.mjs. */
export const forbiddenColors = ['#091B2D', '#8ED2E5'] as const

// --- Utilitários de contraste (WCAG 2.x) ---
const channel = (c: number) => {
  const s = c / 255
  return s <= 0.03928 ? s / 12.92 : ((s + 0.055) / 1.055) ** 2.4
}

export function luminance(hex: string): number {
  const v = hex.replace('#', '')
  const r = parseInt(v.slice(0, 2), 16)
  const g = parseInt(v.slice(2, 4), 16)
  const b = parseInt(v.slice(4, 6), 16)
  return 0.2126 * channel(r) + 0.7152 * channel(g) + 0.0722 * channel(b)
}

export function contrastRatio(a: string, b: string): number {
  const [l1, l2] = [luminance(a), luminance(b)].sort((x, y) => y - x)
  return (l1 + 0.05) / (l2 + 0.05)
}
