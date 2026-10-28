/**
 * Janela de vídeo (ADR-022): qualquer link para um vídeo do site (.mp4/.webm
 * em /uploads) abre aqui. O nome do link vira o nome da janela; Esc, o botão
 * ou um clique fora fecham, o vídeo para e o foco volta ao link.
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
  let opener = null
  const close = () => dialog.open && dialog.close()
  dialog.addEventListener('close', () => {
    video.pause()
    video.removeAttribute('src')
    video.load()
    opener?.focus()
  })
  dialog.querySelector('[data-video-close]').addEventListener('click', close)
  dialog.addEventListener('click', (e) => { if (e.target === dialog) close() })
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href]')
    if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey) return
    const url = new URL(a.href, location.href)
    if (url.origin !== location.origin || !/\/uploads\/.+\.(mp4|webm)$/i.test(url.pathname)) return
    e.preventDefault()
    opener = a
    dialog.setAttribute('aria-label', linkName(a))
    video.src = url.href
    document.querySelectorAll('.mobile-menu[open]').forEach((m) => m.close())
    dialog.showModal()
    video.play().catch(() => {})
  })
}
