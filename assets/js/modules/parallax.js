/** Parallax leve na imagem (KV): desloca até 12% enquanto a seção cruza a tela. Desligado com movimento reduzido. */
import { reducedMotion } from './motion.js'

export default function parallax(el) {
  const media = el.querySelector('img, video')
  if (!media || reducedMotion()) return
  let ticking = false
  const update = () => {
    ticking = false
    const r = el.getBoundingClientRect()
    if (r.bottom < 0 || r.top > innerHeight) return
    const p = Math.min(1, Math.max(0, -r.top / r.height))
    media.style.transform = `translate3d(0, ${(p * 12).toFixed(2)}%, 0) scale(1.08)`
  }
  addEventListener('scroll', () => {
    if (!ticking) {
      ticking = true
      requestAnimationFrame(update)
    }
  }, { passive: true })
  update()
}
