/** Utilitários de movimento: respeitam prefers-reduced-motion. */
const mql = window.matchMedia('(prefers-reduced-motion: reduce)')
export const reducedMotion = () => mql.matches
export const onReducedMotionChange = (cb) => mql.addEventListener('change', () => cb(mql.matches))

/** Observa visibilidade (pausar animações fora da viewport). */
export function whenVisible(el, cb, options = {}) {
  if (!('IntersectionObserver' in window)) {
    cb(true)
    return () => {}
  }
  const io = new IntersectionObserver(([entry]) => cb(entry.isIntersecting), options)
  io.observe(el)
  return () => io.disconnect()
}
