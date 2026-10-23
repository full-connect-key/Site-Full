/**
 * Carrossel acessível: setas, indicadores, arraste/swipe, teclado (←/→),
 * autoplay pausado no hover/foco, botão de pausa e sem autoplay com
 * movimento reduzido. Slides inativos ficam inertes.
 */
import { reducedMotion, whenVisible } from './motion.js'

export default function slider(el) {
  const track = el.querySelector('.slider__track')
  const slides = [...el.querySelectorAll('[data-slide]')]
  const dots = [...el.querySelectorAll('[data-slide-to]')]
  const pauseBtn = el.querySelector('[data-slider-pause]')
  const interval = Number(el.dataset.interval || 6000)
  if (slides.length < 2) return

  let index = 0
  let timer = 0
  let userPaused = reducedMotion()
  let hover = false
  let visible = false

  const go = (n) => {
    index = (n + slides.length) % slides.length
    track.style.transform = `translateX(${-index * 100}%)`
    slides.forEach((s, i) => {
      const on = i === index
      s.toggleAttribute('inert', !on)
      s.setAttribute('aria-hidden', String(!on))
    })
    dots.forEach((d, i) => (i === index ? d.setAttribute('aria-current', 'true') : d.removeAttribute('aria-current')))
  }
  const schedule = () => {
    clearInterval(timer)
    if (!userPaused && !hover && visible && !document.hidden) timer = setInterval(() => go(index + 1), interval)
  }
  const setPaused = (p) => {
    userPaused = p
    if (pauseBtn) {
      pauseBtn.setAttribute('aria-pressed', String(p))
      pauseBtn.setAttribute('aria-label', p ? pauseBtn.dataset.labelPlay : pauseBtn.dataset.labelPause)
      pauseBtn.querySelector('[data-icon-pause]').hidden = p
      pauseBtn.querySelector('[data-icon-play]').hidden = !p
    }
    schedule()
  }

  el.querySelector('[data-slider-prev]')?.addEventListener('click', () => { go(index - 1); schedule() })
  el.querySelector('[data-slider-next]')?.addEventListener('click', () => { go(index + 1); schedule() })
  dots.forEach((d) => d.addEventListener('click', () => { go(Number(d.dataset.slideTo)); schedule() }))
  pauseBtn?.addEventListener('click', () => setPaused(!userPaused))
  el.addEventListener('mouseenter', () => { hover = true; schedule() })
  el.addEventListener('mouseleave', () => { hover = false; schedule() })
  el.addEventListener('focusin', () => { hover = true; schedule() })
  el.addEventListener('focusout', (e) => { if (!el.contains(e.relatedTarget)) { hover = false; schedule() } })
  el.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight') go(index + 1)
    if (e.key === 'ArrowLeft') go(index - 1)
  })

  // Swipe / arraste
  let startX = null
  const viewport = el.querySelector('.slider__viewport')
  viewport.addEventListener('pointerdown', (e) => { startX = e.clientX })
  viewport.addEventListener('pointerup', (e) => {
    if (startX === null) return
    const dx = e.clientX - startX
    if (Math.abs(dx) > 40) { go(index + (dx < 0 ? 1 : -1)); schedule() }
    startX = null
  })

  whenVisible(el, (v) => { visible = v; schedule() })
  document.addEventListener('visibilitychange', schedule)
  if (pauseBtn && userPaused) setPaused(true)
  go(0)
}
