/**
 * Cena guiada pela rolagem (bloco "Como funciona" das plataformas).
 * O trilho tem ~1 altura de tela por passo e o palco fica fixo (sticky).
 * A posição da rolagem vira progresso: data-step (1..N), classes cumulativas
 * at-1..at-N, --p (cena toda) e --sp (dentro do passo); elementos com
 * data-at="0..1" ganham .is-on quando o progresso passa daquele ponto.
 * Rolar para cima desfaz. O seletor acompanha os passos e, clicado, leva a
 * rolagem até o passo. Com movimento reduzido (ou animação desligada no
 * painel): sem pin, visual completo e seletor como controle manual.
 */
import { reducedMotion } from './motion.js'

export default function scene(el) {
  const total = Number(el.dataset.steps) || 1
  const track = el.querySelector('.scene__track')
  const tabs = [...el.querySelectorAll('[data-scene-go]')]
  const steps = [...el.querySelectorAll('[data-scene-step]')]
  const marks = [...el.querySelectorAll('[data-at]')]
  const profiles = [...el.querySelectorAll('[data-cluster].match-profile')]
  let current = 0

  const apply = (step, p, sp) => {
    el.style.setProperty('--p', p.toFixed(4))
    el.style.setProperty('--sp', sp.toFixed(4))
    marks.forEach((m) => m.classList.toggle('is-on', p >= Number(m.dataset.at)))
    el.classList.toggle('is-done', p >= 0.98)
    const sub = String(Math.min(2, Math.floor(sp * 3)))
    if (el.dataset.sub !== sub) el.dataset.sub = sub
    if (step === current) return
    current = step
    el.dataset.step = String(step)
    for (let k = 1; k <= total; k++) el.classList.toggle(`at-${k}`, k <= step)
    tabs.forEach((t, k) => t.setAttribute('aria-current', k + 1 === step ? 'step' : 'false'))
    steps.forEach((s, k) => s.classList.toggle('is-active', k + 1 === step))
  }

  // Perfis do FullMatch: clicar fixa o perfil (pausa a automação até sair da cena).
  profiles.forEach((b) =>
    b.addEventListener('click', () => {
      const pinned = el.dataset.pin === b.dataset.cluster
      if (pinned) delete el.dataset.pin
      else el.dataset.pin = b.dataset.cluster
      profiles.forEach((o) => o.setAttribute('aria-pressed', String(!pinned && o === b)))
    }),
  )

  const live = el.classList.contains('is-motion') && !reducedMotion() && 'IntersectionObserver' in window
  if (!live) {
    el.classList.add('is-static')
    apply(total, 1, 1)
    tabs.forEach((t) => t.addEventListener('click', () => apply(Number(t.dataset.sceneGo), 1, 1)))
    return
  }
  el.classList.add('is-live')

  let ticking = false
  let inView = false
  const update = () => {
    ticking = false
    const r = track.getBoundingClientRect()
    const span = Math.max(1, r.height - innerHeight)
    const p = Math.min(1, Math.max(0, -r.top / span))
    const x = p * total
    const step = Math.min(total, Math.floor(x) + 1)
    apply(step, p, Math.min(1, Math.max(0, x - (step - 1))))
  }
  const onScroll = () => {
    if (inView && !ticking) {
      ticking = true
      requestAnimationFrame(update)
    }
  }
  new IntersectionObserver(([e]) => {
    inView = e.isIntersecting
    if (!inView && el.dataset.pin) {
      delete el.dataset.pin
      profiles.forEach((o) => o.setAttribute('aria-pressed', 'false'))
    }
    onScroll()
  }).observe(el)
  addEventListener('scroll', onScroll, { passive: true })
  addEventListener('resize', onScroll, { passive: true })

  tabs.forEach((t) =>
    t.addEventListener('click', () => {
      const k = Number(t.dataset.sceneGo)
      const r = track.getBoundingClientRect()
      const span = Math.max(1, r.height - innerHeight)
      const top = scrollY + r.top + ((k - 0.5) / total) * span
      scrollTo({ top, behavior: 'smooth' })
    }),
  )

  // Hover no gráfico do FullDash: guia vertical com rótulo conceitual do passo (sem valores).
  const svg = el.querySelector('[data-crosshair]')
  const cross = svg?.querySelector('.viz-cross')
  const label = svg?.querySelector('[data-cross-label]')
  if (svg && cross) {
    const names = (label?.dataset.labels || '').split('|')
    svg.addEventListener('pointermove', (e) => {
      if (e.pointerType !== 'mouse') return
      const r = svg.getBoundingClientRect()
      const x = ((e.clientX - r.left) / r.width) * 420
      cross.setAttribute('transform', `translate(${Math.max(20, Math.min(404, x)).toFixed(1)} 0)`)
      cross.classList.add('is-on')
      if (label) label.textContent = names[Math.min(names.length, current) - 1] || ''
    })
    svg.addEventListener('pointerleave', () => cross.classList.remove('is-on'))
  }
  update()
}
