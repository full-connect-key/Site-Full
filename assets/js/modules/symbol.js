/**
 * Hero da Home — símbolo Full (espiral de pontos no ângulo áureo) que
 * "floresce" do centro para fora, depois gira devagar e respira; perto do
 * cursor os pontos se afastam e acendem (proposta A aprovada).
 * - Desenhado em canvas a partir das cores oficiais (tokens), nítido em
 *   qualquer densidade de tela.
 * - Pausa fora da viewport e com a aba oculta.
 * - Movimento reduzido: símbolo completo e parado, sem reação ao cursor.
 * Decorativo: o canvas é aria-hidden.
 */
import { reducedMotion, whenVisible } from './motion.js'

const N = 420
const GA = Math.PI * (3 - Math.sqrt(5))

const hex = (v, fallback) => {
  const m = String(v || '').trim().match(/^#?([0-9a-f]{6})$/i)
  const h = m ? m[1] : fallback
  return [0, 2, 4].map((i) => parseInt(h.slice(i, i + 2), 16))
}
const mix = (a, b, k) => a.map((v, i) => Math.round(v + (b[i] - v) * k))
const ease = (x) => 1 - Math.pow(1 - Math.min(1, Math.max(0, x)), 3)

export default function symbol(el) {
  const canvas = document.createElement('canvas')
  canvas.setAttribute('aria-hidden', 'true')
  canvas.className = 'symbol__canvas'
  el.prepend(canvas)
  const ctx = canvas.getContext('2d')
  if (!ctx) return

  const root = getComputedStyle(document.documentElement)
  const cyan = hex(root.getPropertyValue('--brand-cyan'), '68ccf2')
  const dark = hex(root.getPropertyValue('--brand-cyan-dark'), '1b6f98')
  const white = hex(root.getPropertyValue('--white'), 'ffffff')
  const center = mix(white, cyan, 0.12) // centro quase branco
  const edge = mix(dark, cyan, 0.3) // borda: azul do símbolo

  const still = reducedMotion()
  const dots = []
  for (let i = 1; i <= N; i++) {
    const k = Math.sqrt(i / N)
    dots.push({
      i,
      k,
      a: i * GA,
      ring: (i * 7919) % 11 === 0 && i > 25, // alguns anéis vazados, como no símbolo
      col: (k < 0.45 ? mix(center, cyan, k / 0.45) : mix(cyan, edge, (k - 0.45) / 0.55)).join(','),
      size: 0.7 + 9.6 * Math.pow(k, 1.2),
    })
  }

  let w = 0, h = 0, dpr = 1, cx = 0, cy = 0, R = 0, t0 = 0, raf = 0, running = false
  const pointer = { x: -9999, y: -9999 }

  const resize = () => {
    const r = el.getBoundingClientRect()
    dpr = Math.min(window.devicePixelRatio || 1, 2)
    w = r.width
    h = r.height
    canvas.width = Math.round(w * dpr)
    canvas.height = Math.round(h * dpr)
    canvas.style.width = w + 'px'
    canvas.style.height = h + 'px'
    const mobile = w < 768
    R = mobile ? Math.min(w * 0.6, 340) : Math.min(h * 0.42, w * 0.28, 560)
    cx = mobile ? w * 0.66 : w * 0.74
    cy = mobile ? h * 0.32 : h * 0.47
    if (!running) draw(performance.now())
  }

  const draw = (now) => {
    const t = still ? 99 : (now - t0) / 1000
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0)
    ctx.clearRect(0, 0, w, h)
    const unit = (R / Math.sqrt(N)) * 0.98
    const spin = still ? 0 : t * 0.04
    const breathe = still ? 1 : 1 + Math.sin(t * 0.8) * 0.012
    const scale = R / 520
    for (const d of dots) {
      const r = unit * Math.sqrt(d.i) * breathe
      let x = cx + Math.cos(d.a + spin) * r
      let y = cy + Math.sin(d.a + spin) * r
      const appear = still ? 1 : ease(t * 1.5 - d.k * 2.2) // nasce do centro para fora
      let s = d.size * scale * appear
      let alpha = appear
      if (!still) {
        const dx = x - pointer.x, dy = y - pointer.y, dist = Math.hypot(dx, dy)
        if (dist < 140) {
          const f = 1 - dist / 140
          x += (dx / (dist || 1)) * f * 18
          y += (dy / (dist || 1)) * f * 18
          alpha = Math.min(1, alpha + f)
        }
      }
      if (s < 0.2) continue
      ctx.globalAlpha = alpha
      ctx.beginPath()
      ctx.arc(x, y, s, 0, Math.PI * 2)
      if (d.ring) {
        ctx.lineWidth = Math.max(1, s * 0.3)
        ctx.strokeStyle = `rgb(${d.col})`
        ctx.stroke()
      } else {
        ctx.fillStyle = `rgb(${d.col})`
        ctx.fill()
      }
    }
    ctx.globalAlpha = 1
    if (running) raf = requestAnimationFrame(draw)
  }

  const start = () => {
    if (running || still) return
    running = true
    raf = requestAnimationFrame(draw)
  }
  const stop = () => {
    running = false
    cancelAnimationFrame(raf)
  }

  if (!still) {
    el.addEventListener('pointermove', (e) => {
      if (e.pointerType !== 'mouse') return
      const r = el.getBoundingClientRect()
      pointer.x = e.clientX - r.left
      pointer.y = e.clientY - r.top
    })
    el.addEventListener('pointerleave', () => {
      pointer.x = pointer.y = -9999
    })
    whenVisible(el, (v) => (v && !document.hidden ? start() : stop()))
    document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()))
  }
  window.addEventListener('resize', resize, { passive: true })
  t0 = performance.now()
  resize()
}
