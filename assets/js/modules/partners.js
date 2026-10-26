/**
 * Sócios (proposta B): em repouso todos os retratos ficam coloridos. O
 * selecionado fica em preto e branco e revela a bio. Funciona com hover
 * (desktop), foco e clique/toque; ao sair da seção, todos voltam ao repouso.
 * No celular (uma coluna) todas as bios ficam visíveis.
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
  const canHover = window.matchMedia('(hover: hover) and (min-width: 40rem)').matches
  items.forEach((item) => {
    const btn = item.querySelector('.partner__toggle')
    btn.addEventListener('click', () => activate(item.classList.contains('is-active') ? null : item))
    btn.addEventListener('focus', () => activate(item))
    if (canHover) item.addEventListener('mouseenter', () => activate(item))
  })
  if (canHover) el.addEventListener('mouseleave', () => { if (!el.contains(document.activeElement)) activate(null) })
  el.addEventListener('focusout', (e) => { if (!el.contains(e.relatedTarget)) activate(null) })
}
