import { getHomepage } from '@/lib/cms'

/** Home — seções implementadas nas Sprints 03 e 04. */
export async function HomeSections() {
  const home = await getHomepage()
  return (
    <section data-theme="hero" className="flex min-h-dvh items-center">
      <div className="container-site">
        {home.hero?.headline ? <h1 className="text-display-xl">{home.hero.headline}</h1> : null}
      </div>
    </section>
  )
}
