/**
 * Alternância vertical entre dois títulos do hero. Ambos ficam no DOM e
 * acessíveis (o leitor de tela lê os dois uma vez; sem aria-live). Com
 * movimento reduzido, os dois aparecem empilhados e estáticos.
 */
import { reducedMotion, whenVisible } from './motion.js'

export default function headlineSwap(el) {
  const items = [...el.querySelectorAll('[data-swap-item]')]
  if (items.length < 2 || reducedMotion()) {
    el.classList.add('is-static')
    return
  }
  el.classList.add('is-animated')
  let i = 0, timer = 0, visible = true
  const show = (n) => items.forEach((it, k) => it.classList.toggle('is-current', k === n))
  const tick = () => {
    i = (i + 1) % items.length
    show(i)
  }
  const start = () => {
    clearInterval(timer)
    if (visible && !document.hidden) timer = setInterval(tick, 4200)
  }
  show(0)
  whenVisible(el, (v) => {
    visible = v
    start()
  })
  document.addEventListener('visibilitychange', start)
}
