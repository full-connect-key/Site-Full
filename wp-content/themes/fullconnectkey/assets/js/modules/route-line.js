/**
 * Transição de página por "linha de conexão": ao clicar num link interno a
 * linha atravessa o topo. Não bloqueia nem atrasa a navegação.
 */
import { reducedMotion } from './motion.js'

export default function routeLine() {
  const line = document.querySelector('[data-route-line]')
  if (!line) return
  document.addEventListener('click', (e) => {
    if (reducedMotion() || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey) return
    const a = e.target.closest('a[href]')
    if (!a || a.target === '_blank' || a.hasAttribute('download')) return
    const url = new URL(a.href, location.href)
    if (url.origin !== location.origin || (url.pathname === location.pathname && url.hash)) return
    line.classList.remove('is-active')
    void line.offsetWidth
    line.classList.add('is-active')
  })
}
