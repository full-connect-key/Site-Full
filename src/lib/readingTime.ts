type LexicalNode = { type?: string; text?: string; children?: LexicalNode[]; fields?: Record<string, unknown> }

/** Extrai o texto de um estado Lexical (inclui blocos de citação). */
export function lexicalPlainText(node: unknown): string {
  if (!node || typeof node !== 'object') return ''
  const n = node as LexicalNode & { root?: LexicalNode }
  if (n.root) return lexicalPlainText(n.root)
  const parts: string[] = []
  if (typeof n.text === 'string') parts.push(n.text)
  if (n.fields && typeof n.fields.quote === 'string') parts.push(n.fields.quote)
  if (Array.isArray(n.children)) for (const child of n.children) parts.push(lexicalPlainText(child))
  return parts.join(' ')
}

export const WORDS_PER_MINUTE = 200

export function readingMinutes(...texts: (string | null | undefined)[]): number {
  const words = texts
    .filter(Boolean)
    .join(' ')
    .split(/\s+/)
    .filter((w) => /[\p{L}\p{N}]/u.test(w)).length
  return Math.max(1, Math.round(words / WORDS_PER_MINUTE))
}
