/**
 * Galeria horizontal: arraste com o mouse, rolagem nativa no toque,
 * setas do teclado quando focada e botões anterior/próximo.
 */
import { reducedMotion } from './motion.js'

export default function dragScroll(el) {
  const step = () => Math.max(240, el.clientWidth * 0.7)
  const behavior = () => (reducedMotion() ? 'auto' : 'smooth')
  const scrollBy = (dir) => el.scrollBy({ left: dir * step(), behavior: behavior() })

  document.querySelectorAll(`[aria-controls="${el.id}"]`).forEach((btn) => {
    btn.addEventListener('click', () => scrollBy(btn.matches('[data-scroll-prev]') ? -1 : 1))
  })
  el.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight') { e.preventDefault(); scrollBy(1) }
    if (e.key === 'ArrowLeft') { e.preventDefault(); scrollBy(-1) }
  })

  let down = false, startX = 0, startLeft = 0, moved = false
  el.addEventListener('pointerdown', (e) => {
    if (e.pointerType !== 'mouse') return
    down = true; moved = false; startX = e.clientX; startLeft = el.scrollLeft
    el.classList.add('is-dragging')
  })
  window.addEventListener('pointermove', (e) => {
    if (!down) return
    const dx = e.clientX - startX
    if (Math.abs(dx) > 4) moved = true
    el.scrollLeft = startLeft - dx
  })
  window.addEventListener('pointerup', () => { down = false; el.classList.remove('is-dragging') })
  el.addEventListener('click', (e) => { if (moved) { e.preventDefault(); e.stopPropagation() } }, true)
}
