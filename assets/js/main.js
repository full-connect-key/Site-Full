/**
 * Full Connect Key — carregador de interações.
 * Cada componente declara data-module="nome"; o módulo correspondente em
 * ./modules/ é importado sob demanda. Sem JS, o conteúdo continua legível.
 */
document.body.classList.remove('no-js')
document.body.classList.add('js')

const loaded = new Map()

async function boot(root = document) {
  const els = root.querySelectorAll('[data-module]')
  for (const el of els) {
    if (el.dataset.moduleReady) continue
    el.dataset.moduleReady = '1'
    for (const name of el.dataset.module.split(/\s+/)) {
      try {
        if (!loaded.has(name)) loaded.set(name, import(`./modules/${name}.js`))
        const mod = await loaded.get(name)
        mod.default?.(el)
      } catch (err) {
        console.error(`[fck] módulo ${name}`, err)
      }
    }
  }
}

boot()
import('./modules/reveal.js').then((m) => m.default())
import('./modules/route-line.js').then((m) => m.default())

export { boot }
