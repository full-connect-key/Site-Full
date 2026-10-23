/** Ecossistema: o nó do diagrama acompanha a solução selecionada (cor, linha e foco). */
export default function eco(el) {
  const nodes = [...el.querySelectorAll('[data-eco-node]')]
  const stage = el.querySelector('.eco__stage')
  el.addEventListener('fck:tab', (e) => {
    const i = e.detail.index
    nodes.forEach((n, k) => n.classList.toggle('is-active', k === i))
    if (stage) stage.dataset.product = nodes[i]?.dataset.product || ''
  })
  // Clicar num nó também seleciona a aba correspondente.
  nodes.forEach((n, i) => n.addEventListener('click', () => el.querySelector(`[data-eco-index="${i}"]`)?.click()))
  if (stage) stage.dataset.product = nodes[0]?.dataset.product || ''
}
