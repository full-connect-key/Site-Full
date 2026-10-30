/**
 * Ecossistema: o nó do diagrama acompanha a solução selecionada (cor, linha e foco).
 * Produtos com vídeo (ADR-022) abrem o vídeo ao clicar no nome ou no nó.
 */
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
  // Produto com vídeo de apresentação: o clique (ou Enter/Espaço) abre o vídeo, pelo mesmo link do painel.
  el.querySelectorAll('[data-eco-video]').forEach((tab) => {
    tab.addEventListener('click', () => document.getElementById(tab.getAttribute('aria-controls'))?.querySelector('.eco__more')?.click())
  })
  if (el.querySelector('[data-eco-video]')) nodes.forEach((n) => { n.style.cursor = 'pointer' })
  if (stage) stage.dataset.product = nodes[0]?.dataset.product || ''
}
