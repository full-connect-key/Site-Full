/**
 * Setores: grupo de botões com aria-pressed. Estado inicial sem seleção
 * (mostra a introdução); selecionar troca texto e imagem; clicar de novo
 * desseleciona.
 */
export default function sectors(el) {
  const buttons = [...el.querySelectorAll('[data-sector]')]
  const panels = [...el.querySelectorAll('[data-sector-panel]')]
  const intro = el.querySelector('[data-sector-intro]')

  const select = (key) => {
    buttons.forEach((b) => b.setAttribute('aria-pressed', String(b.dataset.sector === key)))
    panels.forEach((p) => (p.hidden = p.dataset.sectorPanel !== key))
    if (intro) intro.hidden = key !== null
    el.dataset.active = key ?? ''
  }

  buttons.forEach((b) =>
    b.addEventListener('click', () => select(b.getAttribute('aria-pressed') === 'true' ? null : b.dataset.sector)),
  )
  select(null)
}
