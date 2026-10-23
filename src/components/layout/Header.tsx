'use client'

import Link from 'next/link'
import { usePathname } from 'next/navigation'
import { useCallback, useEffect, useId, useRef, useState, type ReactNode } from 'react'

import { fill } from '@/lib/format'
import type { ResolvedLink } from '@/lib/links'

import { buttonClass } from '../ui/Button'
import { SmartLink } from '../ui/SmartLink'
import { MobileMenu } from './MobileMenu'
import type { NavItem } from './nav'

export type HeaderLabels = {
  homeLink: string
  mainNav: string
  openMenu: string
  closeMenu: string
  openSubmenu: string
  externalLink: string
}

type Props = {
  brand: ReactNode
  items: NavItem[]
  cta: ResolvedLink | null
  labels: HeaderLabels
}

const isActive = (pathname: string, href: string) =>
  href !== '/' && (pathname === href || pathname.startsWith(`${href}/`))

export function Header({ brand, items, cta, labels }: Props) {
  const pathname = usePathname()
  const [hidden, setHidden] = useState(false)
  const [scrolled, setScrolled] = useState(false)
  const [menuOpen, setMenuOpen] = useState(false)
  const [openSub, setOpenSub] = useState<number | null>(null)
  const headerRef = useRef<HTMLElement>(null)
  const focusInside = useRef(false)
  const lastY = useRef(0)

  // Esconde ao descer, mostra ao subir. Nunca esconde com foco dentro do header.
  useEffect(() => {
    const onScroll = () => {
      const y = window.scrollY
      setScrolled(y > 8)
      if (focusInside.current || menuOpen || openSub !== null) {
        setHidden(false)
      } else if (y > lastY.current + 6 && y > 160) {
        setHidden(true)
      } else if (y < lastY.current - 6 || y < 160) {
        setHidden(false)
      }
      lastY.current = y
    }
    onScroll()
    window.addEventListener('scroll', onScroll, { passive: true })
    return () => window.removeEventListener('scroll', onScroll)
  }, [menuOpen, openSub])

  // Fecha submenu/menu ao navegar.
  useEffect(() => {
    setOpenSub(null)
    setMenuOpen(false)
  }, [pathname])

  const closeSub = useCallback((returnFocus?: HTMLElement | null) => {
    setOpenSub(null)
    returnFocus?.focus()
  }, [])

  useEffect(() => {
    if (openSub === null) return
    const onKey = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        const btn = headerRef.current?.querySelector<HTMLButtonElement>(`[data-sub-toggle="${openSub}"]`)
        closeSub(btn)
      }
    }
    const onClick = (e: MouseEvent) => {
      if (!headerRef.current?.contains(e.target as Node)) setOpenSub(null)
    }
    document.addEventListener('keydown', onKey)
    document.addEventListener('mousedown', onClick)
    return () => {
      document.removeEventListener('keydown', onKey)
      document.removeEventListener('mousedown', onClick)
    }
  }, [openSub, closeSub])

  return (
    <header
      ref={headerRef}
      data-theme="dark"
      onFocus={() => {
        focusInside.current = true
        setHidden(false)
      }}
      onBlur={(e) => {
        if (!e.currentTarget.contains(e.relatedTarget as Node)) {
          focusInside.current = false
          setOpenSub(null)
        }
      }}
      className={`fixed inset-x-0 top-0 z-50 h-header !bg-transparent transition-transform duration-500 ease-out-expo motion-reduce:transition-none ${
        hidden ? '-translate-y-full' : 'translate-y-0'
      }`}
    >
      <div
        aria-hidden="true"
        className={`absolute inset-0 border-b transition-[background-color,border-color,backdrop-filter] duration-300 ${
          scrolled || openSub !== null
            ? 'border-line bg-graphite/85 backdrop-blur-md'
            : 'border-transparent bg-transparent'
        }`}
      />
      <div className="container-site relative flex h-full items-center justify-between gap-6">
        <Link href="/" aria-label={labels.homeLink} className="relative z-10 inline-flex min-h-11 items-center text-lg text-fg">
          {brand}
        </Link>

        <nav aria-label={labels.mainNav} className="hidden lg:block">
          <ul className="flex items-center gap-1">
            {items.map((item, index) => {
              const active = isActive(pathname, item.href)
              const hasChildren = item.children.length > 0
              const subId = `submenu-${index}`
              return (
                <li key={`${item.href}-${index}`} className="relative" onMouseLeave={() => hasChildren && openSub === index && setOpenSub(null)}>
                  <div className="flex items-center" onMouseEnter={() => hasChildren && setOpenSub(index)}>
                    <SmartLink
                      href={item.href}
                      externalNotice={labels.externalLink}
                      aria-current={active ? 'page' : undefined}
                      className={`relative inline-flex min-h-11 items-center px-3 text-small font-medium transition-colors hover:text-accent ${
                        active ? 'text-accent' : 'text-fg'
                      }`}
                    >
                      {item.label}
                    </SmartLink>
                    {hasChildren ? (
                      <button
                        type="button"
                        data-sub-toggle={index}
                        aria-expanded={openSub === index}
                        aria-controls={subId}
                        aria-label={fill(labels.openSubmenu, { label: item.label })}
                        onClick={() => setOpenSub(openSub === index ? null : index)}
                        className="-ml-2 inline-flex size-11 items-center justify-center text-fg-2 hover:text-accent"
                      >
                        <svg aria-hidden="true" viewBox="0 0 12 12" className={`size-3 transition-transform ${openSub === index ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" strokeWidth="1.6">
                          <path d="M2 4l4 4 4-4" />
                        </svg>
                      </button>
                    ) : null}
                  </div>
                  {hasChildren ? (
                    <div
                      id={subId}
                      hidden={openSub !== index}
                      className="absolute left-0 top-full min-w-64 border border-line bg-graphite/95 p-2 backdrop-blur-md"
                    >
                      <ul>
                        {item.children.map((child) => (
                          <li key={child.href}>
                            <SmartLink
                              href={child.href}
                              externalNotice={labels.externalLink}
                              aria-current={pathname === child.href ? 'page' : undefined}
                              className="flex min-h-11 items-center px-3 text-small text-fg-2 transition-colors hover:bg-dark-card hover:text-accent aria-[current=page]:text-accent"
                            >
                              {child.label}
                            </SmartLink>
                          </li>
                        ))}
                      </ul>
                    </div>
                  ) : null}
                </li>
              )
            })}
          </ul>
        </nav>

        <div className="flex items-center gap-2">
          {cta ? (
            <SmartLink href={cta.href} externalNotice={labels.externalLink} className={buttonClass('primary', 'hidden min-h-11 px-5 sm:inline-flex')}>
              {cta.label}
            </SmartLink>
          ) : null}
          <MenuButton open={menuOpen} onToggle={() => setMenuOpen((v) => !v)} labels={labels} />
        </div>
      </div>
      <MobileMenu open={menuOpen} onClose={() => setMenuOpen(false)} items={items} cta={cta} labels={labels} pathname={pathname} />
    </header>
  )
}

function MenuButton({ open, onToggle, labels }: { open: boolean; onToggle: () => void; labels: HeaderLabels }) {
  const id = useId()
  return (
    <button
      type="button"
      id={id}
      data-menu-toggle
      aria-expanded={open}
      aria-controls="mobile-menu"
      aria-label={open ? labels.closeMenu : labels.openMenu}
      onClick={onToggle}
      className="relative z-[60] inline-flex size-11 items-center justify-center text-fg lg:hidden"
    >
      <span aria-hidden="true" className="relative block h-3 w-6">
        <span className={`absolute left-0 top-0 h-px w-6 bg-current transition-transform duration-300 ${open ? 'translate-y-1.5 rotate-45' : ''}`} />
        <span className={`absolute bottom-0 left-0 h-px w-6 bg-current transition-transform duration-300 ${open ? '-translate-y-1.5 -rotate-45' : ''}`} />
      </span>
    </button>
  )
}
