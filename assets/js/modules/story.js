/**
 * Narrativa por rolagem: a etapa mais próxima do centro da tela fica ativa
 * e o visual (fixo ao lado) reage via data-step. Com movimento reduzido,
 * o visual mostra o estado final e as etapas ficam empilhadas.
 */
import { reducedMotion } from './motion.js'

export default function story(el) {
  const steps = [...el.querySelectorAll('[data-story-step]')]
  const total = steps.length
  const apply = (i) => {
    el.dataset.step = String(i)
    steps.forEach((s, k) => s.classList.toggle('is-active', k === i))
    // Hub: cards das colunas já alcançadas se organizam.
    el.querySelectorAll('.hub-card').forEach((c) => c.classList.toggle('is-placed', Number(c.dataset.col) <= i))
    // Leads: grupos já alcançados convergem; anéis acendem.
    el.querySelectorAll('.lead-dot').forEach((d) => d.classList.toggle('is-in', Number(d.dataset.group) <= i))
    el.querySelectorAll('.lead-ring').forEach((r) => r.classList.toggle('is-on', Number(r.dataset.ring) <= i))
    el.classList.toggle('is-complete', i === total - 1)
  }
  if (reducedMotion() || !('IntersectionObserver' in window)) {
    el.classList.add('is-static')
    apply(total - 1)
    return
  }
  el.classList.add('is-live')
  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) apply(Number(e.target.dataset.storyStep))
      })
    },
    { rootMargin: '-45% 0px -45% 0px' },
  )
  steps.forEach((s) => io.observe(s))
  apply(0)
}
