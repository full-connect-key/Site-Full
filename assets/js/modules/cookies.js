/**
 * Consentimento de cookies (LGPD). A escolha fica em localStorage. Ferramentas
 * opcionais só carregam depois do "sim" da categoria delas:
 * - Google Analytics 4 (análise), ID em Configurações › Cookies (data-ga4).
 *   Usa o Consent Mode v2: padrão negado; ao aceitar, carrega gtag.js; ao
 *   recusar depois, avisa o Google e para de medir.
 */
export const CONSENT_KEY = 'fck:cookie-consent'
export const CONSENT_VERSION = 1
let ga4Loaded = false

/** Carrega o GA4 só com consentimento de análise; atualiza o Consent Mode se mudar. */
function applyGa4(id, consent) {
  if (!id) return
  const granted = Boolean(consent?.analytics)
  window.dataLayer = window.dataLayer || []
  window.gtag = window.gtag || function gtag() { window.dataLayer.push(arguments) }
  if (!ga4Loaded) {
    if (!granted) return
    window.gtag('consent', 'default', { analytics_storage: 'denied', ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied' })
    window.gtag('consent', 'update', { analytics_storage: 'granted' })
    window.gtag('js', new Date())
    window.gtag('config', id, { anonymize_ip: true })
    const s = document.createElement('script')
    s.async = true
    s.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(id)}`
    document.head.appendChild(s)
    ga4Loaded = true
    // Vídeos de apresentação (Ecossistema): evento quando alguém abre um.
    document.addEventListener('click', (e) => {
      const a = e.target.closest('a[href$=".mp4"], a[href$=".webm"]')
      if (a && hasConsent('analytics')) window.gtag('event', 'video_open', { video: a.pathname.split('/').pop() })
    })
    return
  }
  window.gtag('consent', 'update', { analytics_storage: granted ? 'granted' : 'denied' })
}

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
  const ga4 = el.dataset.ga4 || ''
  const shouldShow = mode === 'always' || (mode === 'auto' && Boolean(ga4))
  applyGa4(ga4, readConsent())
  window.addEventListener('fck:consent', (e) => applyGa4(ga4, e.detail))

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
