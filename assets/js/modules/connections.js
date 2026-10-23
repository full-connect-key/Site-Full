/**
 * Fundo de pontos e linhas que reagem ao cursor (canvas 2D leve).
 * - Pausa fora da viewport e com a aba oculta.
 * - Em telas de toque reage à rolagem; com movimento reduzido fica estático.
 * Decorativo: o canvas é aria-hidden.
 */
import { reducedMotion, whenVisible } from './motion.js'

export default function connections(el) {
  const canvas = document.createElement('canvas')
  canvas.setAttribute('aria-hidden', 'true')
  canvas.className = 'connections__canvas'
  el.prepend(canvas)
  const ctx = canvas.getContext('2d')
  if (!ctx) return

  const style = getComputedStyle(el)
  const accent = style.getPropertyValue('--accent').trim() || '#68ccf2'
  const fg = style.getPropertyValue('--fg-3').trim() || '#9aa3ab'
  const density = Number(el.dataset.density || 1)
  const touch = window.matchMedia('(hover: none)').matches

  let w = 0, h = 0, dpr = 1, points = [], running = false, visible = false, raf = 0
  const pointer = { x: -9999, y: -9999, active: false }

  const resize = () => {
    const r = el.getBoundingClientRect()
    dpr = Math.min(window.devicePixelRatio || 1, 2)
    w = r.width
    h = r.height
    canvas.width = Math.round(w * dpr)
    canvas.height = Math.round(h * dpr)
    canvas.style.width = w + 'px'
    canvas.style.height = h + 'px'
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0)
    const count = Math.round(Math.min(140, (w * h) / 11000) * density)
    let seed = 7
    const rand = () => ((seed = (seed * 16807) % 2147483647) / 2147483647)
    points = Array.from({ length: count }, () => ({
      x: rand() * w,
      y: rand() * h,
      vx: (rand() - 0.5) * 0.25,
      vy: (rand() - 0.5) * 0.25,
      r: rand() * 1.4 + 0.6,
    }))
    draw()
  }

  const linkDist = () => Math.min(160, Math.max(90, w / 10))

  const draw = () => {
    ctx.clearRect(0, 0, w, h)
    const max = linkDist()
    for (let i = 0; i < points.length; i++) {
      const p = points[i]
      for (let j = i + 1; j < points.length; j++) {
        const q = points[j]
        const dx = p.x - q.x, dy = p.y - q.y
        const d = Math.hypot(dx, dy)
        if (d < max) {
          ctx.globalAlpha = (1 - d / max) * 0.22
          ctx.strokeStyle = fg
          ctx.lineWidth = 1
          ctx.beginPath()
          ctx.moveTo(p.x, p.y)
          ctx.lineTo(q.x, q.y)
          ctx.stroke()
        }
      }
      if (pointer.active) {
        const d = Math.hypot(p.x - pointer.x, p.y - pointer.y)
        if (d < max * 1.6) {
          ctx.globalAlpha = (1 - d / (max * 1.6)) * 0.85
          ctx.strokeStyle = accent
          ctx.beginPath()
          ctx.moveTo(p.x, p.y)
          ctx.lineTo(pointer.x, pointer.y)
          ctx.stroke()
        }
      }
    }
    for (const p of points) {
      const near = pointer.active && Math.hypot(p.x - pointer.x, p.y - pointer.y) < max * 1.6
      ctx.globalAlpha = near ? 1 : 0.55
      ctx.fillStyle = near ? accent : fg
      ctx.beginPath()
      ctx.arc(p.x, p.y, near ? p.r + 0.8 : p.r, 0, Math.PI * 2)
      ctx.fill()
    }
    ctx.globalAlpha = 1
  }

  const step = () => {
    for (const p of points) {
      p.x += p.vx
      p.y += p.vy
      if (pointer.active) {
        const dx = pointer.x - p.x, dy = pointer.y - p.y
        const d = Math.hypot(dx, dy)
        if (d < 200 && d > 1) {
          p.x += (dx / d) * 0.35
          p.y += (dy / d) * 0.35
        }
      }
      if (p.x < -10) p.x = w + 10
      if (p.x > w + 10) p.x = -10
      if (p.y < -10) p.y = h + 10
      if (p.y > h + 10) p.y = -10
    }
    draw()
    raf = requestAnimationFrame(step)
  }

  const start = () => {
    if (running || reducedMotion() || !visible || document.hidden) return
    running = true
    raf = requestAnimationFrame(step)
  }
  const stop = () => {
    running = false
    cancelAnimationFrame(raf)
  }

  if (!touch) {
    el.addEventListener('pointermove', (e) => {
      const r = el.getBoundingClientRect()
      pointer.x = e.clientX - r.left
      pointer.y = e.clientY - r.top
      pointer.active = true
    })
    el.addEventListener('pointerleave', () => (pointer.active = false))
  } else {
    // Toque: a rolagem desloca suavemente a rede.
    let last = window.scrollY
    window.addEventListener(
      'scroll',
      () => {
        if (!visible || reducedMotion()) return
        const dy = window.scrollY - last
        last = window.scrollY
        for (const p of points) p.y -= dy * 0.15 * p.r
        if (!running) draw()
      },
      { passive: true },
    )
  }

  whenVisible(el, (v) => {
    visible = v
    v ? start() : stop()
  })
  document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()))
  new ResizeObserver(resize).observe(el)
  resize()
}
