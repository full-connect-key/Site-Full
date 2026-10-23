/**
 * Full Connect Key — carregador de interações.
 * Cada componente declara data-module="nome"; o módulo correspondente em
 * ./modules/ é importado sob demanda. Sem JS, o conteúdo continua legível.
 */
// A troca no-js → js acontece num script inline no <head> (antes da 1ª pintura, sem CLS); aqui só garante.
document.documentElement.classList.replace('no-js', 'js')

const loaded = new Map()
// Mesma versão do main.js (?v=) nos módulos: um deploy novo nunca reaproveita JS antigo do cache.
const v = new URL(import.meta.url).search

async function boot(root = document) {
  const els = root.querySelectorAll('[data-module]')
  for (const el of els) {
    if (el.dataset.moduleReady) continue
    el.dataset.moduleReady = '1'
    for (const name of el.dataset.module.split(/\s+/)) {
      try {
        if (!loaded.has(name)) loaded.set(name, import(`./modules/${name}.js${v}`))
        const mod = await loaded.get(name)
        mod.default?.(el)
      } catch (err) {
        console.error(`[fck] módulo ${name}`, err)
      }
    }
  }
}

boot()
import(`./modules/reveal.js${v}`).then((m) => m.default())
import(`./modules/route-line.js${v}`).then((m) => m.default())

export { boot }
