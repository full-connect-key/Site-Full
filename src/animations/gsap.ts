'use client'

/**
 * Carregamento sob demanda de GSAP + ScrollTrigger (narrativas de scroll).
 * Regra do projeto: GSAP anima narrativas de scroll; Motion anima
 * microinterações. Nunca os dois na mesma propriedade do mesmo elemento.
 */
let loaded: Promise<{ gsap: typeof import('gsap').gsap; ScrollTrigger: typeof import('gsap/ScrollTrigger').ScrollTrigger }> | null = null

export function loadGsap() {
  if (!loaded) {
    loaded = Promise.all([import('gsap'), import('gsap/ScrollTrigger')]).then(([g, st]) => {
      g.gsap.registerPlugin(st.ScrollTrigger)
      return { gsap: g.gsap, ScrollTrigger: st.ScrollTrigger }
    })
  }
  return loaded
}
