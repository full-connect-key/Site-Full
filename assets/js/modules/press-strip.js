/**
 * Full na Mídia — faixa horizontal imersiva. No desktop a matéria sob o
 * cursor (ou com foco) se expande e revela a capa por máscara; a manchete
 * ativa acompanha levemente o cursor. No toque, rolagem horizontal com
 * snap. Anterior/próximo e setas ← → navegam entre as matérias.
 */
import { reducedMotion } from './motion.js'

export default function pressStrip(el) {
  const track = el.querySelector('[data-strip-track]')
  const items = [...el.querySelectorAll('[data-strip-item]')]
  if (!track || !items.length) return
  const wide = matchMedia('(min-width: 64rem) and (hover: hover)')
  let active = 0

  const activate = (i, { scroll = false, focus = false } = {}) => {
    active = (i + items.length) % items.length
    items.forEach((it, k) => it.classList.toggle('is-active', k === active))
    if (scroll && !wide.matches) {
      track.scrollTo({ left: items[active].offsetLeft - track.offsetLeft, behavior: reducedMotion() ? 'auto' : 'smooth' })
    }
    if (focus) items[active].querySelector('a')?.focus({ preventScroll: !wide.matches })
  }

  items.forEach((it, i) => {
    it.addEventListener('pointerenter', (e) => e.pointerType === 'mouse' && activate(i))
    it.addEventListener('focusin', () => activate(i))
    if (!reducedMotion()) {
      const h = it.querySelector('[data-headline]')
      it.addEventListener('pointermove', (e) => {
        if (e.pointerType !== 'mouse' || !h) return
        const r = it.getBoundingClientRect()
        h.style.setProperty('--mx', (((e.clientX - r.left) / r.width) * 2 - 1).toFixed(3))
        h.style.setProperty('--my', (((e.clientY - r.top) / r.height) * 2 - 1).toFixed(3))
      })
      it.addEventListener('pointerleave', () => {
        h?.style.setProperty('--mx', 0)
        h?.style.setProperty('--my', 0)
      })
    }
  })

  el.querySelector('[data-strip-prev]')?.addEventListener('click', () => activate(active - 1, { scroll: true }))
  el.querySelector('[data-strip-next]')?.addEventListener('click', () => activate(active + 1, { scroll: true }))
  track.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
      e.preventDefault()
      activate(active + (e.key === 'ArrowRight' ? 1 : -1), { scroll: true, focus: true })
    }
  })

  // No toque, a matéria mais visível vira a ativa (para os botões seguirem dali).
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver(
      (entries) => {
        if (wide.matches) return
        const best = entries.filter((e) => e.isIntersecting && e.intersectionRatio > 0.6)[0]
        if (best) activate(items.indexOf(best.target))
      },
      { root: track, threshold: [0.6] },
    )
    items.forEach((it) => io.observe(it))
  }
}
