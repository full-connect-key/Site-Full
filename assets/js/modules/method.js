/**
 * Narrativa do método: a seção fica fixa enquanto a rolagem avança pelos
 * movimentos, com progresso visível. Com movimento reduzido (ou sem JS), os
 * movimentos aparecem empilhados e legíveis.
 */
import { reducedMotion } from './motion.js'

export default function method(el) {
  const panels = [...el.querySelectorAll('[data-step-panel]')]
  const names = [...el.querySelectorAll('[data-step-name]')]
  const lines = [...el.querySelectorAll('[data-step-line]')]
  const nodes = [...el.querySelectorAll('[data-step-node]')]
  const bar = el.querySelector('[data-method-bar]')
  const progress = el.querySelector('[role="progressbar"]')
  const track = el.querySelector('.method__track')
  if (!panels.length || !track) return

  if (reducedMotion()) {
    el.classList.add('is-static')
    return
  }
  el.classList.add('is-pinned')

  let current = -1
  const setStep = (i) => {
    if (i === current) return
    current = i
    panels.forEach((p, k) => p.classList.toggle('is-active', k === i))
    names.forEach((n, k) => n.classList.toggle('is-active', k === i))
    lines.forEach((l, k) => l.classList.toggle('is-active', k <= i))
    nodes.forEach((n, k) => n.classList.toggle('is-active', k <= i))
    progress.setAttribute('aria-valuenow', String(i + 1))
    progress.setAttribute('aria-valuetext', (progress.dataset.progressTemplate || '').replace('{n}', i + 1).replace('{total}', panels.length))
  }

  let ticking = false
  const update = () => {
    ticking = false
    const r = track.getBoundingClientRect()
    const total = r.height - window.innerHeight
    const p = Math.min(1, Math.max(0, -r.top / Math.max(1, total)))
    bar.style.transform = `scaleX(${Math.max(0.02, p)})`
    setStep(Math.min(panels.length - 1, Math.floor(p * panels.length * 0.999)))
  }
  window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(update) } }, { passive: true })
  window.addEventListener('resize', update)

  // Clique (ou Enter) num nome leva à posição correspondente.
  names.forEach((n, i) => {
    n.querySelector('a').addEventListener('click', (e) => {
      e.preventDefault()
      const r = track.getBoundingClientRect()
      const total = r.height - window.innerHeight
      const y = window.scrollY + r.top + ((i + 0.5) / panels.length) * total
      window.scrollTo({ top: y, behavior: 'smooth' })
      panels[i].focus({ preventScroll: true })
    })
  })
  update()
}
