/** Compartilhar: o botão "Copiar link" só aparece quando a API de área de transferência existe. */
export default function share(el) {
  const btn = el.querySelector('[data-copy]')
  const status = el.querySelector('.share__status')
  if (!btn || !navigator.clipboard) return
  btn.hidden = false
  btn.addEventListener('click', async () => {
    try {
      await navigator.clipboard.writeText(btn.dataset.copy)
      status.textContent = status.dataset.copied || ''
      setTimeout(() => (status.textContent = ''), 4000)
    } catch {
      /* sem permissão: o link continua na barra de endereço */
    }
  })
}
