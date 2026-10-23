import type { Field, GlobalConfig } from 'payload'

import { staffOnly } from '../access'
import { enabledField, text, textarea } from '../fields/common'
import { ctaField } from '../fields/link'
import { seoGroup } from '../fields/seo'
import { revalidateGlobal } from '../hooks/revalidate'
import { globalLivePreview } from '../preview'

/** Seção da Home: grupo com chave "exibir". A ordem é fixa para preservar a narrativa. */
const section = (name: string, label: string, description: string, fields: Field[]): Field => ({
  name,
  label,
  type: 'group',
  admin: { description },
  fields: [enabledField(), ...fields],
})

const titleAndText = (titleMax = 120, textMax = 500): Field[] => [
  text('title', 'Título', titleMax),
  textarea('text', 'Texto de apoio', textMax),
]

export const HomepageGlobal: GlobalConfig = {
  slug: 'homepage',
  label: 'Home',
  admin: {
    group: 'Home',
    description:
      'As 11 seções da Home em ordem fixa. Cada seção pode ser ocultada. Seções que dependem de conteúdo real (sócios, depoimentos, time...) só aparecem quando esse conteúdo existe.',
    livePreview: globalLivePreview('homepage'),
    preview: () => `${process.env.NEXT_PUBLIC_SERVER_URL || 'http://localhost:3000'}/next/preview?global=homepage`,
  },
  access: { read: () => true, update: staffOnly },
  versions: { drafts: { autosave: { interval: 2000 } }, max: 50 },
  hooks: { afterChange: [revalidateGlobal] },
  fields: [
    {
      type: 'tabs',
      tabs: [
        {
          label: '1–3 Abertura',
          fields: [
            section('hero', '1. Hero', 'Os dois títulos se alternam.', [
              text('headline', 'Título principal', 120, { required: true }),
              text('headlineAlt', 'Título alternativo', 120),
              textarea('lead', 'Texto de apoio', 400),
              ctaField(),
            ]),
            section(
              'logos',
              '2. Faixa de logos',
              'Sem título. Mostra apenas clientes com autorização de exibição; sem logos autorizados a faixa some.',
              [],
            ),
            section('essence', '3. Nossa Essência', 'Título, introdução e três colunas.', [
              text('title', 'Título', 120),
              textarea('intro', 'Introdução', 700),
              {
                name: 'pillars',
                label: 'Colunas',
                type: 'array',
                maxRows: 3,
                labels: { singular: 'Coluna', plural: 'Colunas' },
                fields: [text('name', 'Nome (ex.: Conecta)', 30, { required: true }), textarea('text', 'Texto', 300)],
              },
            ]),
          ],
        },
        {
          label: '4–6 Ecossistema, setores, cases',
          fields: [
            section(
              'ecosystem',
              '4. Ecossistema',
              'Os resumos de cada plataforma são editados em Ecossistema › Plataformas.',
              [...titleAndText(), ctaField()],
            ),
            section('sectors', '5. Setores', 'Os setores são editados em Home › Setores.', [
              text('title', 'Título', 120),
              textarea('intro', 'Introdução (aparece sem setor selecionado)', 400),
            ]),
            section(
              'cases',
              '6. Cases',
              'Mostra os cases marcados como destaque (posições 1 a 3). Indicadores só com métricas reais.',
              [...titleAndText(), ctaField()],
            ),
          ],
        },
        {
          label: '7–9 Pessoas',
          fields: [
            section('partners', '7. Os Sócios', 'Aparece somente com os três sócios cadastrados.', titleAndText()),
            section(
              'testimonials',
              '8. Depoimentos',
              'Aparece somente com depoimentos autorizados.',
              titleAndText(),
            ),
            section('team', '9. O Time', 'Aparece somente com pessoas cadastradas.', titleAndText()),
          ],
        },
        {
          label: '10–11 Insights e CTA',
          fields: [
            section('insights', '10. Insights', 'Aparece somente com artigos publicados.', [
              ...titleAndText(),
              ctaField(),
            ]),
            section('footerCta', '11. CTA final', 'Chamada cinematográfica antes do rodapé.', [
              text('title', 'Título', 120),
              textarea('text', 'Texto', 300),
              ctaField(),
            ]),
          ],
        },
        { label: 'SEO', fields: [seoGroup] },
      ],
    },
  ],
}
