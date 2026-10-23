/**
 * Consentimento de cookies. A escolha fica em localStorage (nenhum cookie é
 * gravado). Nenhuma ferramenta opcional está aprovada: OPTIONAL_TOOLS vazio.
 * Scripts futuros devem ser carregados com loadIfConsented().
 */
export const CONSENT_KEY = 'fck:cookie-consent'
export const CONSENT_VERSION = 1
export const OPTIONAL_TOOLS = [] // ex.: { name: 'Analytics', category: 'analytics', src: '...' }

export function readConsent() {
  try {
    const c = JSON.parse(localStorage.getItem(CONSENT_KEY) || 'null')
    return c && c.version === CONSENT_VERSION ? c : null
  } catch {
    return null
  }
}

function writeConsent(choice) {
  const c = { version: CONSENT_VERSION, ...choice, updatedAt: new Date().toISOString() }
  try {
    localStorage.setItem(CONSENT_KEY, JSON.stringify(c))
  } catch {
    /* armazenamento indisponível: vale só nesta visita */
  }
  window.dispatchEvent(new CustomEvent('fck:consent', { detail: c }))
  return c
}

export function hasConsent(category) {
  return category === 'necessary' || Boolean(readConsent()?.[category])
}

export default function cookies(el) {
  const banner = el.querySelector('[data-cookie-banner]')
  const dialog = el.querySelector('[data-cookie-dialog]')
  const form = el.querySelector('[data-cookie-form]')
  const mode = el.dataset.mode || 'auto'
  const shouldShow = mode === 'always' || (mode === 'auto' && OPTIONAL_TOOLS.length > 0)

  const syncForm = () => {
    const c = readConsent()
    form.analytics.checked = Boolean(c?.analytics)
    form.marketing.checked = Boolean(c?.marketing)
  }
  const decide = (choice) => {
    writeConsent(choice)
    banner.hidden = true
    if (dialog.open) dialog.close()
  }

  if (!readConsent() && shouldShow) banner.hidden = false

  document.addEventListener('click', (e) => {
    const t = e.target.closest('[data-cookie-open], [data-cookie-accept], [data-cookie-reject]')
    if (!t) return
    if (t.matches('[data-cookie-open]')) {
      syncForm()
      dialog.showModal()
    } else if (t.matches('[data-cookie-accept]')) decide({ analytics: true, marketing: true })
    else decide({ analytics: false, marketing: false })
  })
  form.addEventListener('submit', (e) => {
    e.preventDefault()
    decide({ analytics: form.analytics.checked, marketing: form.marketing.checked })
  })
}
