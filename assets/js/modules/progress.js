/**
 * Progresso de entrada de uma seção (0 → 1) em --p, ligado à rolagem: 0 quando
 * o topo entra pela base da tela, 1 quando chega a ~20% do topo. Rolar para
 * cima desfaz. Movimento reduzido: --p = 1 (estado final, estático).
 */
import { reducedMotion } from './motion.js'

export default function progress(el) {
  if (reducedMotion() || !('IntersectionObserver' in window)) {
    el.style.setProperty('--p', '1')
    return
  }
  let ticking = false
  let inView = false
  const update = () => {
    ticking = false
    const r = el.getBoundingClientRect()
    const p = Math.min(1, Math.max(0, (innerHeight - r.top) / (innerHeight * 0.8)))
    el.style.setProperty('--p', p.toFixed(4))
  }
  const onScroll = () => {
    if (inView && !ticking) {
      ticking = true
      requestAnimationFrame(update)
    }
  }
  new IntersectionObserver(([e]) => {
    inView = e.isIntersecting
    onScroll()
  }).observe(el)
  addEventListener('scroll', onScroll, { passive: true })
  update()
}
