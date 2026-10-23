import type { ConsentCategory } from './consent'

/**
 * Ferramentas opcionais aprovadas (análise/marketing). VAZIO até aprovação.
 * Ao aprovar uma ferramenta: adicione aqui e carregue o script via <ConsentGate>.
 */
export const optionalTools: { name: string; category: Exclude<ConsentCategory, 'necessary'> }[] = []

export const hasOptionalTools = () => optionalTools.length > 0
