/** Faixa de logos: botão de pausa acessível (além da pausa no hover/foco via CSS). */
export default function marquee(el) {
  const btn = el.querySelector('[data-marquee-toggle]')
  if (!btn) return
  btn.addEventListener('click', () => {
    const paused = el.classList.toggle('is-paused')
    btn.setAttribute('aria-pressed', String(paused))
    btn.setAttribute('aria-label', paused ? btn.dataset.labelPlay : btn.dataset.labelPause)
  })
}
