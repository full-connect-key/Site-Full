import type { Field, GlobalConfig } from 'payload'

import { staffOnly } from '../access'
import { revalidateGlobal } from '../hooks/revalidate'

type Def = { label: string; max: number; help?: string }

/**
 * Microtextos de interface (rótulos, mensagens, aria-labels). Os valores
 * iniciais estão em src/cms/seed/content.ts — nunca no JSX.
 * Tokens entre chaves ({n}, {total}, {max}) são substituídos pelo site.
 */
export const uiCopyDefinition = {
  a11y: {
    _label: 'Acessibilidade e navegação',
    skipToContent: { label: 'Pular para o conteúdo', max: 60 },
    homeLink: { label: 'Link do logo (leitor de tela)', max: 80 },
    mainNav: { label: 'Nome da navegação principal', max: 60 },
    footerNav: { label: 'Nome da navegação do rodapé', max: 60 },
    openMenu: { label: 'Abrir menu', max: 40 },
    closeMenu: { label: 'Fechar menu', max: 40 },
    openSubmenu: { label: 'Abrir submenu', max: 60, help: 'Use {label} para o nome do item.' },
    backToTop: { label: 'Voltar ao topo', max: 40 },
    nextProject: { label: 'Próximo projeto', max: 40 },
    previousProject: { label: 'Projeto anterior', max: 40 },
    nextArticle: { label: 'Próximo artigo', max: 40 },
    previousArticle: { label: 'Artigo anterior', max: 40 },
    viewAllPublications: { label: 'Ver todas as publicações', max: 60 },
    pause: { label: 'Pausar movimento', max: 40 },
    play: { label: 'Retomar movimento', max: 40 },
    previous: { label: 'Anterior', max: 40 },
    next: { label: 'Próximo', max: 40 },
    goToItem: { label: 'Ir para item', max: 60, help: 'Use {n} e {total}.' },
    externalLink: { label: 'Aviso de link externo', max: 60 },
    loading: { label: 'Carregando', max: 40 },
    breadcrumb: { label: 'Nome da trilha de navegação', max: 60 },
    logosRegion: { label: 'Nome da faixa de logos (leitor de tela)', max: 60 },
    illustrative: { label: 'Selo de conteúdo ilustrativo', max: 40 },
    scrollHint: { label: 'Dica de rolagem/arraste', max: 80 },
    progress: { label: 'Progresso', max: 60, help: 'Use {n} e {total}.' },
  },
  form: {
    _label: 'Formulários',
    required: { label: 'Campo obrigatório', max: 120 },
    invalidEmail: { label: 'E-mail inválido', max: 120 },
    tooLong: { label: 'Texto muito longo', max: 120, help: 'Use {max}.' },
    consentRequired: { label: 'Consentimento obrigatório', max: 120 },
    sendError: { label: 'Falha no envio', max: 160 },
    rateLimited: { label: 'Muitas tentativas', max: 160 },
    sending: { label: 'Enviando', max: 80 },
    errorSummary: { label: 'Resumo de erros', max: 120 },
    requiredMark: { label: 'Indicação de campo obrigatório', max: 40 },
    stepOf: { label: 'Etapa X de Y', max: 40, help: 'Use {n} e {total}.' },
  },
  cookies: {
    _label: 'Banner de cookies',
    title: { label: 'Título', max: 80 },
    text: { label: 'Texto', max: 400 },
    acceptAll: { label: 'Aceitar todos', max: 40 },
    rejectOptional: { label: 'Recusar opcionais', max: 40 },
    customize: { label: 'Personalizar preferências', max: 40 },
    preferencesTitle: { label: 'Título da central de preferências', max: 80 },
    necessary: { label: 'Cookies necessários', max: 60 },
    necessaryDescription: { label: 'Descrição — necessários', max: 300 },
    analytics: { label: 'Cookies de análise', max: 60 },
    analyticsDescription: { label: 'Descrição — análise', max: 300 },
    marketing: { label: 'Cookies de marketing', max: 60 },
    marketingDescription: { label: 'Descrição — marketing', max: 300 },
    alwaysOn: { label: 'Selo "sempre ativos"', max: 40 },
    save: { label: 'Salvar preferências', max: 40 },
    policyLink: { label: 'Texto do link da política', max: 60 },
    openPreferences: { label: 'Link para reabrir preferências (rodapé)', max: 60 },
  },
  notFound: {
    _label: 'Página não encontrada (404)',
    title: { label: 'Título', max: 120 },
    text: { label: 'Texto', max: 300 },
    cta: { label: 'Botão', max: 40 },
  },
  error: {
    _label: 'Erro temporário',
    title: { label: 'Título', max: 120 },
    text: { label: 'Texto', max: 300 },
    retry: { label: 'Botão', max: 40 },
  },
  content: {
    _label: 'Artigos e listagens',
    readingTime: { label: 'Tempo de leitura', max: 40, help: 'Use {n} para os minutos.' },
    by: { label: 'Prefixo do autor', max: 20 },
    shareArticle: { label: 'Compartilhar artigo', max: 40 },
    copyLink: { label: 'Copiar link', max: 40 },
    linkCopied: { label: 'Link copiado', max: 40 },
    shareLinkedIn: { label: 'Compartilhar no LinkedIn', max: 60 },
    shareWhatsApp: { label: 'Compartilhar no WhatsApp', max: 60 },
    shareEmail: { label: 'Compartilhar por e-mail', max: 60 },
    filterBy: { label: 'Nome do grupo de filtros', max: 60 },
    resultsCount: { label: 'Quantidade de resultados', max: 60, help: 'Use {n}.' },
  },
} as const

type Groups = typeof uiCopyDefinition
export type UiCopy = {
  [G in keyof Groups]: { [K in Exclude<keyof Groups[G], '_label'>]: string }
}

const buildFields = (): Field[] =>
  (Object.keys(uiCopyDefinition) as (keyof Groups)[]).map((group) => {
    const { _label, ...defs } = uiCopyDefinition[group]
    return {
      name: group,
      label: _label,
      type: 'group',
      fields: Object.entries(defs as Record<string, Def>).map(([name, def]) => ({
        name,
        label: def.label,
        type: def.max > 160 ? 'textarea' : 'text',
        maxLength: def.max,
        admin: { description: [`Até ${def.max} caracteres.`, def.help].filter(Boolean).join(' ') },
      })) as Field[],
    } satisfies Field
  })

export const UiCopyGlobal: GlobalConfig = {
  slug: 'uiCopy',
  label: 'Textos de interface',
  admin: {
    group: 'Configurações',
    description: 'Rótulos de botões, mensagens de erro, textos de acessibilidade, cookies e 404.',
  },
  access: { read: () => true, update: staffOnly },
  versions: { max: 50 },
  hooks: { afterChange: [revalidateGlobal] },
  fields: buildFields(),
}
