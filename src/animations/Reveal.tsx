'use client'

import { motion } from 'motion/react'
import type { ReactNode } from 'react'

import { useReducedMotion } from './useReducedMotion'

type Props = {
  children: ReactNode
  delay?: number
  y?: number
  className?: string
  as?: 'div' | 'li' | 'span'
}

/** Entrada suave ao entrar na viewport. Movimento reduzido = conteúdo estático, sempre visível. */
export function Reveal({ children, delay = 0, y = 24, className, as = 'div' }: Props) {
  const reduced = useReducedMotion()
  if (reduced) {
    const Tag = as
    return <Tag className={className}>{children}</Tag>
  }
  const MotionTag = motion[as]
  return (
    <MotionTag
      className={className}
      initial={{ opacity: 0, y }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, margin: '0px 0px -10% 0px' }}
      transition={{ duration: 0.8, delay, ease: [0.16, 1, 0.3, 1] }}
    >
      {children}
    </MotionTag>
  )
}
