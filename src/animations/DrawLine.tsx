'use client'

import { motion } from 'motion/react'

import { useReducedMotion } from './useReducedMotion'

type Props = {
  orientation?: 'horizontal' | 'vertical'
  className?: string
  delay?: number
}

/** Linha que se desenha no scroll. Decorativa (aria-hidden). */
export function DrawLine({ orientation = 'horizontal', className = '', delay = 0 }: Props) {
  const reduced = useReducedMotion()
  const isH = orientation === 'horizontal'
  const base = `${isH ? 'h-px w-full origin-left' : 'w-px h-full origin-top'} bg-line ${className}`
  if (reduced) return <span aria-hidden="true" className={`block ${base}`} />
  return (
    <motion.span
      aria-hidden="true"
      className={`block ${base}`}
      initial={isH ? { scaleX: 0 } : { scaleY: 0 }}
      whileInView={isH ? { scaleX: 1 } : { scaleY: 1 }}
      viewport={{ once: true, margin: '0px 0px -15% 0px' }}
      transition={{ duration: 1.2, delay, ease: [0.16, 1, 0.3, 1] }}
    />
  )
}
