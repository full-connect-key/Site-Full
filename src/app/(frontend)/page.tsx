import type { Metadata } from 'next'

import { HomeSections } from '@/features/home/HomeSections'
import { getHomepage } from '@/lib/cms'
import { buildPageMetadata } from '@/lib/seo'

export async function generateMetadata(): Promise<Metadata> {
  const home = await getHomepage()
  return buildPageMetadata({ seo: home.seo, fallbackTitle: null, path: '/' })
}

export default async function HomePage() {
  return <HomeSections />
}
