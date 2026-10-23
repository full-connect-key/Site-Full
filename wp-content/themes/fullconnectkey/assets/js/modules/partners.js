/**
 * Sócios: o retrato selecionado expande e revela a bio. Funciona com hover
 * (desktop), foco e clique/toque. Um sempre aberto.
 */
export default function partners(el) {
  const items = [...el.querySelectorAll('[data-partner]')]
  const activate = (item) => {
    items.forEach((it) => {
      const on = it === item
      it.classList.toggle('is-active', on)
      it.querySelector('.partner__toggle').setAttribute('aria-expanded', String(on))
    })
  }
  const canHover = window.matchMedia('(hover: hover) and (min-width: 64rem)').matches
  items.forEach((item) => {
    const btn = item.querySelector('.partner__toggle')
    btn.addEventListener('click', () => activate(item))
    btn.addEventListener('focus', () => activate(item))
    if (canHover) item.addEventListener('mouseenter', () => activate(item))
  })
}
