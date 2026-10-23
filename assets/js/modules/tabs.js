/**
 * Abas acessíveis (WAI-ARIA): clique, setas ↑/↓/←/→, Home/End.
 * Ao passar o mouse numa manchete (desktop), a matéria também troca.
 */
export default function tabs(el) {
  const list = [...el.querySelectorAll('[role="tab"]')]
  if (!list.length) return
  const select = (tab, focus = false) => {
    list.forEach((t) => {
      const on = t === tab
      t.setAttribute('aria-selected', String(on))
      t.tabIndex = on ? 0 : -1
      const panel = document.getElementById(t.getAttribute('aria-controls'))
      if (panel) panel.hidden = !on
    })
    if (focus) tab.focus()
  }
  const hover = window.matchMedia('(hover: hover)').matches
  list.forEach((t, i) => {
    t.addEventListener('click', () => select(t))
    if (hover) t.addEventListener('mouseenter', () => select(t))
    t.addEventListener('keydown', (e) => {
      const keys = { ArrowDown: 1, ArrowRight: 1, ArrowUp: -1, ArrowLeft: -1 }
      if (e.key in keys) { e.preventDefault(); select(list[(i + keys[e.key] + list.length) % list.length], true) }
      if (e.key === 'Home') { e.preventDefault(); select(list[0], true) }
      if (e.key === 'End') { e.preventDefault(); select(list[list.length - 1], true) }
    })
  })
}
