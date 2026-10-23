/* Campos do painel Full Connect Key: mídia, repetidores, links, listas e contadores. */
(function ($) {
  'use strict'

  function initCounters(root) {
    $(root).find('[data-fck-count]').each(function () {
      var el = this
      if (el._fckCount) return
      el._fckCount = true
      var max = parseInt(el.getAttribute('maxlength'), 10)
      var counter = $('<span class="fck-count" aria-live="polite"></span>')
      $(el).closest('.fck-field, .fck-sub').find('label').first().append(counter)
      var update = function () {
        var n = el.value.length
        counter.text(n + '/' + max)
        counter.toggleClass('is-over', n > max)
      }
      $(el).on('input', update)
      update()
    })
  }

  function initLinks(root) {
    $(root).find('[data-fck-link]').each(function () {
      var box = $(this)
      var select = box.find('[data-fck-link-type]')
      var sync = function () {
        var v = select.val()
        box.find('[data-fck-show]').each(function () {
          $(this).toggle($(this).attr('data-fck-show') === v)
        })
      }
      select.off('change.fck').on('change.fck', sync)
      sync()
    })
  }

  function renumber(rep) {
    rep.children('.fck-rows').children('.fck-row').each(function (i) {
      $(this).find('> .fck-row-bar .fck-row-num').text(i + 1)
    })
  }

  function init(root) {
    initCounters(root)
    initLinks(root)
  }

  $(document).on('click', '.fck-media-select', function (e) {
    e.preventDefault()
    var box = $(this).closest('.fck-media')
    var frame = wp.media({
      title: 'Selecionar arquivo',
      button: { text: 'Usar este arquivo' },
      library: box.data('library') ? { type: box.data('library') } : {},
      multiple: false,
    })
    frame.on('select', function () {
      var att = frame.state().get('selection').first().toJSON()
      box.find('input[type=hidden]').val(att.id).trigger('change')
      var thumb = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.icon
      var alt = att.alt ? att.alt : ''
      box.find('.fck-media-preview').html(
        att.type === 'image' ? $('<img>').attr({ src: thumb, alt: alt }) : $('<span class="fck-file">').text(att.filename)
      )
      box.find('.fck-media-remove').prop('hidden', false)
    })
    frame.open()
  })

  $(document).on('click', '.fck-media-remove', function (e) {
    e.preventDefault()
    var box = $(this).closest('.fck-media')
    box.find('input[type=hidden]').val('')
    box.find('.fck-media-preview').empty()
    $(this).prop('hidden', true)
  })

  $(document).on('click', '.fck-add-row', function (e) {
    e.preventDefault()
    var rep = $(this).closest('[data-fck-repeater]')
    var rows = rep.children('.fck-rows')
    var max = parseInt(rep.data('max'), 10) || 50
    if (rows.children('.fck-row').length >= max) {
      window.alert('Limite de ' + max + ' itens.')
      return
    }
    var index = Date.now()
    var html = rep.children('template').html().split('__i__').join(String(index))
    var row = $(html)
    rows.append(row)
    renumber(rep)
    init(row)
    row.find('input, textarea, select').filter(':visible').first().trigger('focus')
  })

  $(document).on('click', '.fck-row > .fck-row-bar .fck-del, .fck-posts-list .fck-del', function (e) {
    e.preventDefault()
    var item = $(this).closest('li')
    var rep = item.closest('[data-fck-repeater]')
    if (item.hasClass('fck-row') && !window.confirm('Remover este item?')) return
    item.remove()
    if (rep.length) renumber(rep)
  })

  $(document).on('click', '.fck-up, .fck-down', function (e) {
    e.preventDefault()
    var item = $(this).closest('li')
    if ($(this).hasClass('fck-up')) item.prev('li').before(item)
    else item.next('li').after(item)
    var rep = item.closest('[data-fck-repeater]')
    if (rep.length) renumber(rep)
    $(this).trigger('focus')
  })

  $(document).on('change', '.fck-posts-add', function () {
    var sel = $(this)
    var id = sel.val()
    if (!id) return
    var list = sel.siblings('.fck-posts-list')
    if (list.find('input[value="' + id + '"]').length === 0) {
      var li = $('<li>')
      li.append($('<input type="hidden">').attr({ name: list.data('name'), value: id }))
      li.append($('<span>').text(sel.find('option:selected').text()))
      li.append(' <button type="button" class="button-link fck-up" aria-label="Mover para cima">↑</button>')
      li.append(' <button type="button" class="button-link fck-down" aria-label="Mover para baixo">↓</button>')
      li.append(' <button type="button" class="button-link fck-del" aria-label="Remover">×</button>')
      list.append(li)
    }
    sel.val('')
  })

  $(function () {
    init(document)
  })
})(jQuery)
