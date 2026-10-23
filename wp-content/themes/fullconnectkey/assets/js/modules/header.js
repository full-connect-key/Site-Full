/**
 * Cabeçalho: esconde ao descer, aparece ao subir (nunca com foco dentro),
 * submenu acessível por teclado (Esc fecha e devolve o foco) e menu mobile
 * em <dialog> modal (trap de foco nativo).
 */
export default function header(el) {
  let lastY = window.scrollY
  let focusInside = false

  const update = () => {
    const y = window.scrollY
    el.classList.toggle('is-scrolled', y > 8)
    const subOpen = el.classList.contains('has-open-sub')
    if (focusInside || subOpen || menu.open) el.classList.remove('is-hidden')
    else if (y > lastY + 6 && y > 160) el.classList.add('is-hidden')
    else if (y < lastY - 6 || y < 160) el.classList.remove('is-hidden')
    lastY = y
  }
  window.addEventListener('scroll', update, { passive: true })

  el.addEventListener('focusin', () => {
    focusInside = true
    el.classList.remove('is-hidden')
  })
  el.addEventListener('focusout', (e) => {
    if (!el.contains(e.relatedTarget)) {
      focusInside = false
      closeAll()
    }
  })

  // Submenus
  const toggles = el.querySelectorAll('.main-nav__toggle')
  const closeAll = (except) => {
    toggles.forEach((t) => {
      if (t === except) return
      t.setAttribute('aria-expanded', 'false')
      document.getElementById(t.getAttribute('aria-controls'))?.setAttribute('hidden', '')
    })
    el.classList.toggle('has-open-sub', !!except)
  }
  const open = (t) => {
    closeAll(t)
    t.setAttribute('aria-expanded', 'true')
    document.getElementById(t.getAttribute('aria-controls'))?.removeAttribute('hidden')
    el.classList.add('has-open-sub')
  }
  toggles.forEach((t) => {
    t.addEventListener('click', () => (t.getAttribute('aria-expanded') === 'true' ? closeAll() : open(t)))
    const item = t.closest('.main-nav__item')
    if (window.matchMedia('(hover: hover)').matches) {
      item.addEventListener('mouseenter', () => open(t))
      item.addEventListener('mouseleave', () => closeAll())
    }
  })
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return
    const opened = [...toggles].find((t) => t.getAttribute('aria-expanded') === 'true')
    if (opened) {
      closeAll()
      opened.focus()
    }
  })
  document.addEventListener('click', (e) => {
    if (!el.contains(e.target)) closeAll()
  })

  // Menu mobile
  const menu = el.querySelector('#mobile-menu')
  const toggle = el.querySelector('[data-menu-toggle]')
  const closeBtn = el.querySelector('[data-menu-close]')
  const setOpen = (isOpen) => {
    toggle.setAttribute('aria-expanded', String(isOpen))
    toggle.setAttribute('aria-label', isOpen ? toggle.dataset.labelClose : toggle.dataset.labelOpen)
  }
  toggle?.addEventListener('click', () => {
    if (menu.open) menu.close()
    else {
      menu.showModal()
      document.documentElement.style.overflow = 'hidden'
      setOpen(true)
    }
  })
  closeBtn?.addEventListener('click', () => menu.close())
  menu?.addEventListener('close', () => {
    document.documentElement.style.overflow = ''
    setOpen(false)
    toggle.focus()
  })
  menu?.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => menu.close()))

  update()
}
