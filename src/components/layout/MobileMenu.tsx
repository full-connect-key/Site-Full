'use client'

import { useEffect, useRef } from 'react'

import type { ResolvedLink } from '@/lib/links'

import { buttonClass } from '../ui/Button'
import { SmartLink } from '../ui/SmartLink'
import type { HeaderLabels } from './Header'
import type { NavItem } from './nav'

type Props = {
  open: boolean
  onClose: () => void
  items: NavItem[]
  cta: ResolvedLink | null
  labels: HeaderLabels
  pathname: string
}

/**
 * Menu mobile em tela cheia usando <dialog> modal: o restante da página fica
 * inerte (trap de foco nativo), Esc fecha e o foco volta ao botão do menu.
 */
export function MobileMenu({ open, onClose, items, cta, labels, pathname }: Props) {
  const ref = useRef<HTMLDialogElement>(null)

  useEffect(() => {
    const dialog = ref.current
    if (!dialog) return
    if (open && !dialog.open) {
      dialog.showModal()
      document.documentElement.style.overflow = 'hidden'
    } else if (!open && dialog.open) {
      dialog.close()
    }
    if (!open) {
      document.documentElement.style.overflow = ''
    }
  }, [open])

  useEffect(() => {
    const dialog = ref.current
    if (!dialog) return
    const onCloseEvt = () => {
      document.documentElement.style.overflow = ''
      onClose()
      document.querySelector<HTMLButtonElement>('[data-menu-toggle]')?.focus()
    }
    dialog.addEventListener('close', onCloseEvt)
    return () => dialog.removeEventListener('close', onCloseEvt)
  }, [onClose])

  return (
    <dialog
      ref={ref}
      id="mobile-menu"
      aria-label={labels.mainNav}
      data-theme="hero"
      className="m-0 h-dvh max-h-none w-screen max-w-none overflow-y-auto bg-hero-black p-0 text-fg backdrop:bg-hero-black open:flex open:flex-col"
    >
      <div className="container-site flex h-header shrink-0 items-center justify-end">
        <button
          type="button"
          onClick={onClose}
          aria-label={labels.closeMenu}
          className="inline-flex size-11 items-center justify-center text-fg"
        >
          <svg aria-hidden="true" viewBox="0 0 24 24" className="size-6" fill="none" stroke="currentColor" strokeWidth="1.5">
            <path d="M5 5l14 14M19 5L5 19" />
          </svg>
        </button>
      </div>
      <nav aria-label={labels.mainNav} className="container-site flex flex-1 flex-col justify-between gap-10 pb-10 pt-4">
        <ul className="flex flex-col gap-1">
          {items.map((item) => (
            <li key={item.href} className="border-b border-line">
              <SmartLink
                href={item.href}
                externalNotice={labels.externalLink}
                onClick={onClose}
                aria-current={pathname === item.href ? 'page' : undefined}
                className="flex min-h-14 items-center py-3 font-display text-display-s text-fg aria-[current=page]:text-accent"
              >
                {item.label}
              </SmartLink>
              {item.children.length > 0 ? (
                <ul className="grid grid-cols-2 gap-x-4 pb-4">
                  {item.children.map((child) => (
                    <li key={child.href}>
                      <SmartLink
                        href={child.href}
                        externalNotice={labels.externalLink}
                        onClick={onClose}
                        aria-current={pathname === child.href ? 'page' : undefined}
                        className="flex min-h-11 items-center text-small text-fg-2 aria-[current=page]:text-accent"
                      >
                        {child.label}
                      </SmartLink>
                    </li>
                  ))}
                </ul>
              ) : null}
            </li>
          ))}
        </ul>
        {cta ? (
          <SmartLink href={cta.href} onClick={onClose} externalNotice={labels.externalLink} className={buttonClass('primary', 'w-full')}>
            {cta.label}
          </SmartLink>
        ) : null}
      </nav>
    </dialog>
  )
}
