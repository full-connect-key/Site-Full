/**
 * Contato: formulário progressivo em duas etapas. Foco gerenciado entre
 * etapas e nos erros (aria-invalid + aria-describedby), validação no
 * cliente espelhando a do servidor, envio em JSON com estado "enviando" e
 * mensagem de sucesso SOMENTE após confirmação do servidor.
 */
const EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

export default function contactForm(form) {
  const steps = [...form.querySelectorAll('[data-step]')]
  const alert = form.querySelector('[data-alert]')
  const status = form.querySelector('[data-status]')
  const submit = form.querySelector('[data-submit]')
  const success = document.querySelector('[data-success]')
  const msg = form.dataset

  form.classList.add('is-progressive')
  form.querySelectorAll('[data-js-only]').forEach((el) => (el.hidden = false))

  const show = (n) => {
    steps.forEach((s) => (s.hidden = s.dataset.step !== String(n)))
    const title = steps[n - 1].querySelector('.contact-step__title')
    title?.focus()
  }
  steps.forEach((s) => (s.hidden = s.dataset.step !== '1'))

  const setError = (name, text) => {
    const wrap = form.querySelector(`[data-field="${name}"]`)
    if (!wrap) return
    const err = wrap.querySelector('.field__error')
    wrap.querySelectorAll('input, textarea').forEach((i) => (text ? i.setAttribute('aria-invalid', 'true') : i.removeAttribute('aria-invalid')))
    wrap.classList.toggle('has-error', !!text)
    if (err) {
      err.textContent = text || ''
      err.hidden = !text
    }
  }
  const focusFirstError = () => form.querySelector('[aria-invalid="true"]')?.focus()

  const validateStep1 = () => {
    const ok = !!form.querySelector('input[name="subject"]:checked')
    setError('subject', ok ? '' : msg.msgRequired)
    return ok
  }
  const validateStep2 = () => {
    let ok = true
    for (const name of ['name', 'email', 'company', 'message']) {
      const el = form.elements[name]
      const v = el.value.trim()
      let text = ''
      if (el.required && !v) text = msg.msgRequired
      else if (name === 'email' && v && !EMAIL.test(v)) text = msg.msgEmail
      else if (v.length > Number(el.maxLength) && el.maxLength > 0) text = msg.msgTooLong.replace('{max}', el.maxLength)
      setError(name, text)
      ok &&= !text
    }
    const consent = form.elements.consent.checked
    setError('consent', consent ? '' : msg.msgConsent)
    return ok && consent
  }

  form.querySelector('[data-next]')?.addEventListener('click', () => {
    if (validateStep1()) show(2)
    else form.querySelector('input[name="subject"]')?.focus()
  })
  form.querySelector('[data-back]')?.addEventListener('click', () => {
    show(1)
    ;(form.querySelector('input[name="subject"]:checked') || form.querySelector('input[name="subject"]'))?.focus()
  })
  form.querySelectorAll('input[name="subject"]').forEach((r) => r.addEventListener('change', () => setError('subject', '')))
  form.querySelectorAll('input, textarea').forEach((el) =>
    el.addEventListener('input', () => el.getAttribute('aria-invalid') && setError(el.closest('[data-field]')?.dataset.field, '')),
  )

  form.addEventListener('submit', async (e) => {
    e.preventDefault()
    alert.hidden = true
    if (!validateStep1()) {
      show(1)
      form.querySelector('input[name="subject"]')?.focus()
      return
    }
    if (!validateStep2()) return focusFirstError()

    submit.disabled = true
    form.setAttribute('aria-busy', 'true')
    status.textContent = msg.msgSending
    try {
      const res = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' }, credentials: 'same-origin' })
      const data = await res.json().catch(() => ({}))
      if (res.ok && data.ok) {
        form.hidden = true
        success.hidden = false
        success.focus()
        return
      }
      if (data.errors) {
        Object.entries(data.errors).forEach(([k, v]) => setError(k, v))
        if (data.errors.subject) show(1)
        focusFirstError()
      }
      alert.textContent = data.message || msg.msgError
      alert.hidden = false
    } catch {
      alert.textContent = msg.msgError
      alert.hidden = false
    } finally {
      submit.disabled = false
      form.removeAttribute('aria-busy')
      status.textContent = ''
    }
  })
}
