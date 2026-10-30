/**
 * Janela de vídeo (ADR-022): qualquer link para um vídeo do site (.mp4/.webm
 * em /uploads) abre aqui. O nome do link vira o nome da janela; Esc, o botão
 * ou um clique fora fecham, o vídeo para e o foco volta ao link.
 * Um link [data-video-autoopen] na página abre sozinho, uma vez por visita.
 */
// Nome acessível do link: aria-label, ou o texto visível com o alt das imagens (ex.: logo da plataforma).
const linkName = (a) => {
  if (a.getAttribute('aria-label')) return a.getAttribute('aria-label')
  const c = a.cloneNode(true)
  c.querySelectorAll('[aria-hidden="true"]').forEach((n) => n.remove())
  c.querySelectorAll('img').forEach((img) => img.replaceWith(` ${img.alt} `))
  return c.textContent.replace(/\s+/g, ' ').trim()
}

export default function videoModal(dialog) {
  const video = dialog.querySelector('video')
  const unmute = dialog.querySelector('[data-video-unmute]')
  let opener = null
  const syncUnmute = () => { if (unmute) unmute.hidden = !video.muted }
  video.addEventListener('volumechange', syncUnmute)
  unmute?.addEventListener('click', () => { video.muted = false; video.play().catch(() => {}); video.focus() })
  const close = () => dialog.open && dialog.close()
  dialog.addEventListener('close', () => {
    video.pause()
    video.removeAttribute('src')
    video.load()
    opener?.focus()
  })
  dialog.querySelector('[data-video-close]').addEventListener('click', close)
  dialog.addEventListener('click', (e) => { if (e.target === dialog) close() })
  const open = (a, auto = false) => {
    opener = auto ? document.activeElement : a
    dialog.setAttribute('aria-label', linkName(a))
    video.src = new URL(a.href, location.href).href
    video.muted = false
    syncUnmute()
    document.querySelectorAll('.mobile-menu[open]').forEach((m) => m.close())
    dialog.showModal()
    // Navegadores bloqueiam autoplay com som sem um clique: no popup automático, tenta com som e cai para mudo.
    video.play().catch(() => {
      if (!auto) return
      video.muted = true
      video.play().catch(() => {})
    })
  }
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href]')
    if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey) return
    const url = new URL(a.href, location.href)
    if (url.origin !== location.origin || !/\/uploads\/.+\.(mp4|webm)$/i.test(url.pathname)) return
    e.preventDefault()
    open(a)
  })
  // Vídeo de abertura da página: abre sozinho uma vez por visita (sessão do navegador).
  const intro = document.querySelector('a[data-video-autoopen]')
  if (intro) {
    const key = `fck:intro:${new URL(intro.href, location.href).pathname}`
    let seen = false
    try { seen = sessionStorage.getItem(key) === '1'; sessionStorage.setItem(key, '1') } catch { /* sem storage: abre */ }
    if (!seen) setTimeout(() => open(intro, true), 600)
  }
}
