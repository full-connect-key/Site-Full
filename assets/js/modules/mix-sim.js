/**
 * Simulador ILUSTRATIVO do FullMix: ao mover um canal, os demais se ajustam
 * proporcionalmente para manter 100%. Não calcula vendas, ROI nem recomenda.
 * Demonstração automática: enquanto a pessoa rola, a distribuição oscila
 * entre duas composições fixas. Pegar um slider (mouse, toque ou teclado)
 * para a demonstração; ela só volta quando o simulador sai e reentra na tela.
 */
import { reducedMotion } from './motion.js'

export default function mixSim(el) {
  const inputs = [...el.querySelectorAll('input[type=range]')]
  const outs = [...el.querySelectorAll('[data-out]')]
  const segs = [...el.querySelectorAll('[data-seg]')]
  const total = el.querySelector('[data-mix-total]')

  const render = () => {
    inputs.forEach((inp, i) => {
      const v = Number(inp.value)
      outs[i].textContent = `${v}%`
      inp.setAttribute('aria-valuetext', `${v}%`)
      inp.style.setProperty('--fill', `${v}%`)
      if (segs[i]) segs[i].style.flexGrow = String(Math.max(v, 0.0001))
    })
    if (total) total.textContent = `${inputs.reduce((s, i) => s + Number(i.value), 0)}%`
  }

  const rebalance = (changed) => {
    const others = inputs.filter((i) => i !== changed)
    let rest = 100 - Number(changed.value)
    const sum = others.reduce((s, i) => s + Number(i.value), 0)
    others.forEach((inp, k) => {
      const share = sum > 0 ? Number(inp.value) / sum : 1 / others.length
      const v = k === others.length - 1 ? rest : Math.round((100 - Number(changed.value)) * share)
      const clamped = Math.max(0, Math.min(100, v))
      inp.value = String(clamped)
      rest -= clamped
    })
    // Corrige arredondamento no último ajustável.
    const diff = 100 - inputs.reduce((s, i) => s + Number(i.value), 0)
    if (diff && others.length) {
      const target = others.find((i) => Number(i.value) + diff >= 0 && Number(i.value) + diff <= 100)
      if (target) target.value = String(Number(target.value) + diff)
    }
  }

  inputs.forEach((inp) => inp.addEventListener('input', () => { rebalance(inp); render() }))
  el.querySelector('[data-mix-reset]')?.addEventListener('click', () => {
    inputs.forEach((i) => (i.value = i.dataset.initial))
    const diff = 100 - inputs.reduce((s, i) => s + Number(i.value), 0)
    if (diff) inputs[0].value = String(Number(inputs[0].value) + diff)
    render()
  })
  // Garante 100% no início (arredondamentos do servidor).
  const diff = 100 - inputs.reduce((s, i) => s + Number(i.value), 0)
  if (diff) inputs[0].value = String(Number(inputs[0].value) + diff)
  render()

  if (reducedMotion() || el.dataset.demo !== 'on' || inputs.length < 2) {
    el.dataset.demo = 'off'
    return
  }
  const base = inputs.map((i) => Number(i.value))
  const alt = [...base].reverse()
  let visible = false
  let left = false
  let ticking = false
  const demo = () => {
    ticking = false
    if (el.dataset.demo !== 'on' || !visible) return
    const r = el.getBoundingClientRect()
    const q = Math.min(1, Math.max(0, (innerHeight - r.top) / (innerHeight + r.height)))
    const t = (1 - Math.cos(q * Math.PI * 2)) / 2 // vai e volta
    const raw = base.map((b, k) => b + (alt[k] - b) * t)
    const sum = raw.reduce((a, b) => a + b, 0) || 1
    const vals = raw.map((v) => Math.round((v * 100) / sum))
    vals[vals.length - 1] += 100 - vals.reduce((a, b) => a + b, 0)
    inputs.forEach((inp, k) => (inp.value = String(vals[k])))
    render()
  }
  const onScroll = () => {
    if (!ticking) {
      ticking = true
      requestAnimationFrame(demo)
    }
  }
  const stop = () => {
    el.dataset.demo = 'off'
  }
  inputs.forEach((inp) => ['pointerdown', 'keydown', 'touchstart'].forEach((ev) => inp.addEventListener(ev, stop, { passive: true })))
  new IntersectionObserver(([e]) => {
    visible = e.isIntersecting
    if (!visible) left = true
    else if (left && el.dataset.demo === 'off') {
      el.dataset.demo = 'on'
      left = false
    }
    if (visible) onScroll()
  }).observe(el)
  addEventListener('scroll', onScroll, { passive: true })
}
