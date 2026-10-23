/* Painel Full Connect Key — campos, mídia, texto rico e utilidades (sem dependências). */
(function () {
  'use strict'
  const script = document.getElementById('adm-script')
  const CSRF = script ? script.dataset.csrf : ''
  const BASE = script ? script.dataset.base.replace(/\/$/, '') : ''
  const $$ = (sel, root = document) => [...root.querySelectorAll(sel)]

  // Contadores de caracteres
  function initCounters(root) {
    $$('[data-count]', root).forEach((el) => {
      if (el._count) return
      el._count = true
      const max = parseInt(el.getAttribute('maxlength'), 10)
      const out = document.createElement('span')
      out.className = 'fck-count'
      out.setAttribute('aria-live', 'polite')
      const label = el.closest('.fck-field, .fck-sub, form')?.querySelector(`label[for="${el.id}"]`)
      ;(label || el.parentNode).appendChild(out)
      const upd = () => {
        out.textContent = `${el.value.length}/${max}`
        out.classList.toggle('is-over', el.value.length > max)
      }
      el.addEventListener('input', upd)
      upd()
    })
  }

  // Links: mostra página ou URL conforme o destino
  function initLinks(root) {
    $$('[data-link]', root).forEach((box) => {
      const sel = box.querySelector('[data-link-type]')
      const sync = () => $$('[data-show]', box).forEach((s) => (s.hidden = s.dataset.show !== sel.value))
      sel.addEventListener('change', sync)
      sync()
    })
  }

  // Texto rico (Quill) sincronizado com a textarea
  function initRichtext(root) {
    if (!window.Quill) return
    $$('[data-richtext]', root).forEach((box) => {
      if (box._q) return
      box._q = true
      const ta = box.querySelector('textarea')
      ta.hidden = true
      const holder = document.createElement('div')
      holder.innerHTML = ta.value
      box.appendChild(holder)
      const q = new window.Quill(holder, {
        theme: 'snow',
        modules: {
          toolbar: {
            container: [[{ header: [2, 3, 4, false] }], ['bold', 'italic', 'underline', 'strike'], [{ list: 'ordered' }, { list: 'bullet' }], ['blockquote', 'link'], ['image', 'video'], ['clean']],
            handlers: {
              image: () => openMediaPicker({ library: 'image' }, (item) => {
                const r = q.getSelection(true)
                q.insertEmbed(r ? r.index : 0, 'image', item.wide || item.url, 'user')
              }),
            },
          },
        },
      })
      const sync = () => (ta.value = q.root.innerHTML === '<p><br></p>' ? '' : q.root.innerHTML)
      q.on('text-change', sync)
      ta.form?.addEventListener('submit', sync)
    })
  }

  function renumber(rep) {
    $$(':scope > .fck-rows > .fck-row', rep).forEach((row, i) => {
      const n = row.querySelector(':scope > .fck-row-bar .fck-row-num')
      if (n) n.textContent = i + 1
    })
  }

  function init(root) {
    initCounters(root)
    initLinks(root)
    initRichtext(root)
  }

  document.addEventListener('click', (e) => {
    const t = e.target.closest('button, a')
    if (!t) return

    if (t.matches('[data-add-row]')) {
      e.preventDefault()
      const rep = t.closest('[data-repeater]')
      const rows = rep.querySelector(':scope > .fck-rows')
      const max = parseInt(rep.dataset.max, 10) || 50
      if (rows.children.length >= max) return alert(`Limite de ${max} itens.`)
      const ph = rep.dataset.placeholder
      const html = rep.querySelector(':scope > template').innerHTML.split(ph).join(String(Date.now()))
      const tmp = document.createElement('div')
      tmp.innerHTML = html
      const row = tmp.firstElementChild
      rows.appendChild(row)
      renumber(rep)
      init(row)
      row.querySelector('input, textarea, select')?.focus()
    }
    if (t.matches('[data-del]')) {
      e.preventDefault()
      const li = t.closest('li')
      if (li.classList.contains('fck-row') && !confirm('Remover este item?')) return
      const rep = li.closest('[data-repeater]')
      li.remove()
      if (rep) renumber(rep)
    }
    if (t.matches('[data-up], [data-down]')) {
      e.preventDefault()
      const li = t.closest('li')
      if (t.matches('[data-up]') && li.previousElementSibling) li.parentNode.insertBefore(li, li.previousElementSibling)
      if (t.matches('[data-down]') && li.nextElementSibling) li.parentNode.insertBefore(li.nextElementSibling, li)
      const rep = li.closest('[data-repeater]')
      if (rep) renumber(rep)
      t.focus()
    }
    if (t.matches('[data-media-select]')) {
      e.preventDefault()
      const box = t.closest('[data-media]')
      openMediaPicker({ library: box.dataset.library }, (item) => {
        box.querySelector('input[type=hidden]').value = item.id
        const prev = box.querySelector('.fck-media-preview')
        prev.innerHTML = ''
        if (item.thumb) {
          const img = document.createElement('img')
          img.src = item.thumb
          img.alt = ''
          prev.appendChild(img)
        } else {
          prev.textContent = item.name
        }
        box.querySelector('[data-media-remove]').hidden = false
      })
    }
    if (t.matches('[data-media-remove]')) {
      e.preventDefault()
      const box = t.closest('[data-media]')
      box.querySelector('input[type=hidden]').value = ''
      box.querySelector('.fck-media-preview').innerHTML = ''
      t.hidden = true
    }
    if (t.matches('[data-confirm]') && !confirm(t.dataset.confirm)) e.preventDefault()
    if (t.matches('[data-adm-menu]')) {
      const open = document.body.classList.toggle('adm-menu-open')
      t.setAttribute('aria-expanded', String(open))
    }
  })

  // Lista ordenável de relações
  document.addEventListener('change', (e) => {
    const sel = e.target
    if (sel.matches('[data-posts-add]') && sel.value) {
      const list = sel.parentNode.querySelector('.fck-posts-list')
      if (!list.querySelector(`input[value="${sel.value}"]`)) {
        const li = document.createElement('li')
        const inp = Object.assign(document.createElement('input'), { type: 'hidden', name: list.dataset.name, value: sel.value })
        const span = Object.assign(document.createElement('span'), { textContent: sel.selectedOptions[0].textContent })
        li.append(inp, span)
        li.insertAdjacentHTML('beforeend', ' <button type="button" class="btn-link" data-up aria-label="Mover para cima">↑</button> <button type="button" class="btn-link" data-down aria-label="Mover para baixo">↓</button> <button type="button" class="btn-link" data-del aria-label="Remover">×</button>')
        list.appendChild(li)
      }
      sel.value = ''
    }
    if (sel.matches('[data-autosubmit]')) sel.form.submit()
  })

  // Seletor de mídia (modal)
  const modal = document.getElementById('media-modal')
  let onPick = null
  let library = ''
  async function loadMedia(q = '') {
    const grid = modal.querySelector('[data-media-grid]')
    const res = await fetch(`${BASE}/admin/media/list.json?type=${encodeURIComponent(library)}&q=${encodeURIComponent(q)}`, { headers: { Accept: 'application/json' } })
    const data = await res.json()
    grid.innerHTML = ''
    data.items.forEach((item) => {
      const li = document.createElement('li')
      const b = document.createElement('button')
      b.type = 'button'
      b.className = 'adm-media-pick'
      if (item.thumb) {
        const img = Object.assign(document.createElement('img'), { src: item.thumb, alt: '' })
        b.appendChild(img)
      }
      b.appendChild(Object.assign(document.createElement('span'), { className: 'adm-media-name', textContent: item.name }))
      b.addEventListener('click', () => {
        onPick?.(item)
        modal.close()
      })
      li.appendChild(b)
      grid.appendChild(li)
    })
    modal.querySelector('[data-media-status]').textContent = data.items.length ? '' : 'Nenhum arquivo encontrado.'
  }
  function openMediaPicker(opts, cb) {
    if (!modal) return
    onPick = cb
    library = opts.library || ''
    modal.showModal()
    loadMedia()
  }
  if (modal) {
    modal.querySelector('[data-modal-close]').addEventListener('click', () => modal.close())
    let tm
    modal.querySelector('[data-media-search]').addEventListener('input', (e) => {
      clearTimeout(tm)
      tm = setTimeout(() => loadMedia(e.target.value), 250)
    })
    modal.querySelector('[data-media-upload]').addEventListener('change', async (e) => {
      const fd = new FormData()
      ;[...e.target.files].forEach((f) => fd.append('files[]', f))
      fd.append('_csrf', CSRF)
      const status = modal.querySelector('[data-media-status]')
      status.textContent = 'Enviando…'
      const res = await fetch(`${BASE}/admin/media/upload`, { method: 'POST', body: fd, headers: { Accept: 'application/json', 'X-CSRF-Token': CSRF } })
      const data = await res.json().catch(() => ({ errors: ['Falha no envio.'] }))
      status.textContent = (data.errors || []).join(' ') || 'Enviado. Lembre de preencher o texto alternativo na Biblioteca de mídia.'
      e.target.value = ''
      loadMedia()
    })
  }

  // Ponto focal: clique na imagem
  const focalImg = document.querySelector('[data-focal-image]')
  if (focalImg) {
    focalImg.addEventListener('click', (e) => {
      const r = focalImg.getBoundingClientRect()
      const x = Math.round(((e.clientX - r.left) / r.width) * 100)
      const y = Math.round(((e.clientY - r.top) / r.height) * 100)
      document.getElementById('focal').value = `${x}% ${y}%`
    })
  }

  // Avisa ao sair com alterações não salvas
  const form = document.querySelector('[data-edit-form]')
  if (form) {
    let dirty = false
    form.addEventListener('input', () => (dirty = true))
    form.addEventListener('submit', () => (dirty = false))
    window.addEventListener('beforeunload', (e) => {
      if (dirty) {
        e.preventDefault()
        e.returnValue = ''
      }
    })
  }

  init(document)
})()
