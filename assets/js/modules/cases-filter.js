/**
 * Cases: filtro por setor sem recarregar (URL atualizada com ?setor=, para
 * compartilhar e voltar), reflow suave e "Carregar mais".
 */
import { reducedMotion } from './motion.js'

/** Ritmo do grid (blocos de 7): 7/5 · 4/4/4 · 5/7; espelha fck_case_span() em pages/cases.php. */
function span(k, n) {
  const r = k % 7
  const start = k - r
  if (r < 2 || r > 4) {
    const first = r < 2 ? start : start + 5
    if (Math.min(2, n - first) === 1) return 'is-full'
    return r === 0 || r === 6 ? 'is-a' : 'is-b'
  }
  const count = Math.min(3, n - (start + 2))
  return count === 1 ? 'is-full' : count === 2 ? 'is-half' : ''
}

export default function casesFilter(el) {
  const items = [...el.querySelectorAll('.case-grid__item')]
  const chips = [...el.querySelectorAll('[data-filter]')]
  const more = el.querySelector('[data-more-wrap]')
  const moreBtn = el.querySelector('[data-load-more]')
  const empty = el.querySelector('[data-empty]')
  const count = el.querySelector('[data-results-count]')
  const size = Number(el.dataset.pageSize || 9)
  let current = new URL(location.href).searchParams.get('setor') || ''
  let shown = Math.max(size, items.filter((i) => !i.hidden).length)

  const render = (animate) => {
    const matching = items.filter((i) => !current || i.dataset.sector === current)
    const first = animate && !reducedMotion() ? new Map(items.map((i) => [i, i.getBoundingClientRect()])) : null
    items.forEach((i) => (i.hidden = true))
    const visible = matching.slice(0, shown)
    visible.forEach((i, k) => {
      i.hidden = false
      i.classList.remove('is-a', 'is-b', 'is-full', 'is-half')
      const cls = span(k, visible.length)
      if (cls) i.classList.add(cls)
    })
    chips.forEach((c) => (c.dataset.filter === current ? c.setAttribute('aria-current', 'true') : c.removeAttribute('aria-current')))
    if (empty) empty.hidden = matching.length > 0
    if (more) more.hidden = matching.length <= shown
    if (count) count.textContent = (count.dataset.template || '').replace('{n}', matching.length)
    if (first) {
      matching.slice(0, shown).forEach((i) => {
        const a = first.get(i)
        const b = i.getBoundingClientRect()
        if (!a || !a.width) {
          i.animate([{ opacity: 0, transform: 'translateY(16px)' }, { opacity: 1, transform: 'none' }], { duration: 450, easing: 'cubic-bezier(.16,1,.3,1)' })
        } else if (a.left !== b.left || a.top !== b.top) {
          i.animate([{ transform: `translate(${a.left - b.left}px, ${a.top - b.top}px)` }, { transform: 'none' }], { duration: 500, easing: 'cubic-bezier(.16,1,.3,1)' })
        }
      })
    }
  }

  render(false)
  chips.forEach((c) =>
    c.addEventListener('click', (e) => {
      e.preventDefault()
      current = c.dataset.filter
      shown = size
      const url = new URL(location.href)
      current ? url.searchParams.set('setor', current) : url.searchParams.delete('setor')
      url.searchParams.delete('mostrar')
      history.pushState({ setor: current }, '', url)
      render(true)
    }),
  )
  moreBtn?.addEventListener('click', (e) => {
    e.preventDefault()
    const firstNew = items.filter((i) => (!current || i.dataset.sector === current) && i.hidden)[0]
    shown += size
    render(true)
    firstNew?.querySelector('a')?.focus()
  })
  window.addEventListener('popstate', () => {
    current = new URL(location.href).searchParams.get('setor') || ''
    shown = size
    render(true)
  })
}
