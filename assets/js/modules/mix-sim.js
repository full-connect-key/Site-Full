/**
 * Simulador ILUSTRATIVO do FullMix: ao mover um canal, os demais se ajustam
 * proporcionalmente para manter 100%. Não calcula vendas, ROI nem recomenda.
 */
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
}
