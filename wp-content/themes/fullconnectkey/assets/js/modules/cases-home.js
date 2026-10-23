/** Cases da Home: os indicadores alternam conforme o case em foco/hover. */
export default function casesHome(el) {
  const cards = el.querySelectorAll('[data-case-index]')
  const panels = el.querySelectorAll('[data-metrics-for]')
  const show = (i) => panels.forEach((p) => (p.hidden = p.dataset.metricsFor !== String(i)))
  cards.forEach((card) => {
    const i = card.dataset.caseIndex
    card.addEventListener('mouseenter', () => show(i))
    card.addEventListener('focus', () => show(i))
  })
}
