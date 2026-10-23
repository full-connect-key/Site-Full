import path from 'path'
import { fileURLToPath } from 'url'

import { postgresAdapter } from '@payloadcms/db-postgres'
import { nodemailerAdapter } from '@payloadcms/email-nodemailer'
import { lexicalEditor } from '@payloadcms/richtext-lexical'
import { pt } from '@payloadcms/translations/languages/pt'
import { buildConfig } from 'payload'
import sharp from 'sharp'

import { Articles, ArticleCategories, Authors } from './cms/collections/Articles'
import { Cases } from './cms/collections/Cases'
import { Clients } from './cms/collections/Clients'
import { ContactSubmissions } from './cms/collections/ContactSubmissions'
import { Media } from './cms/collections/Media'
import { Pages } from './cms/collections/Pages'
import { InstitutionalMetrics, Partners, TeamMembers, Testimonials } from './cms/collections/People'
import { Platforms } from './cms/collections/Platforms'
import { PressMentions } from './cms/collections/PressMentions'
import { Redirects } from './cms/collections/Redirects'
import { Sectors } from './cms/collections/Sectors'
import { Users } from './cms/collections/Users'
import { HomepageGlobal } from './cms/globals/homepage'
import { SiteSettingsGlobal } from './cms/globals/siteSettings'
import { ThemeSettingsGlobal } from './cms/globals/themeSettings'
import { UiCopyGlobal } from './cms/globals/uiCopy'
import { ensureInitialAdmin } from './cms/hooks/initialAdmin'

const filename = fileURLToPath(import.meta.url)
const dirname = path.dirname(filename)

const serverURL = process.env.NEXT_PUBLIC_SERVER_URL || 'http://localhost:3000'

const email = process.env.SMTP_HOST
  ? nodemailerAdapter({
      defaultFromAddress: process.env.EMAIL_FROM_ADDRESS || 'site@localhost.test',
      defaultFromName: process.env.EMAIL_FROM_NAME || 'Full Connect Key',
      skipVerify: true,
      transportOptions: {
        host: process.env.SMTP_HOST,
        port: Number(process.env.SMTP_PORT || 1025),
        secure: process.env.SMTP_SECURE === 'true',
        auth: process.env.SMTP_USER ? { user: process.env.SMTP_USER, pass: process.env.SMTP_PASS } : undefined,
      },
    })
  : undefined

export default buildConfig({
  serverURL,
  admin: {
    user: Users.slug,
    meta: {
      titleSuffix: ' — Painel Full Connect Key',
      robots: 'noindex, nofollow',
    },
    importMap: { baseDir: path.resolve(dirname) },
    dateFormat: 'dd/MM/yyyy HH:mm',
  },
  i18n: { supportedLanguages: { pt }, fallbackLanguage: 'pt' },
  collections: [
    Pages,
    Platforms,
    Sectors,
    Cases,
    Clients,
    InstitutionalMetrics,
    Articles,
    ArticleCategories,
    Authors,
    PressMentions,
    Partners,
    TeamMembers,
    Testimonials,
    Media,
    Redirects,
    ContactSubmissions,
    Users,
  ],
  globals: [HomepageGlobal, SiteSettingsGlobal, UiCopyGlobal, ThemeSettingsGlobal],
  editor: lexicalEditor(),
  secret: process.env.PAYLOAD_SECRET || '',
  typescript: { outputFile: path.resolve(dirname, 'payload-types.ts') },
  db: postgresAdapter({
    pool: { connectionString: process.env.DATABASE_URL || '' },
    migrationDir: path.resolve(dirname, 'migrations'),
    // Em desenvolvimento o schema é sincronizado automaticamente; em staging/produção use migrations.
    push: process.env.NODE_ENV !== 'production' && process.env.PAYLOAD_DB_PUSH !== 'false',
  }),
  email,
  sharp,
  cors: [serverURL],
  csrf: [serverURL],
  graphQL: { disable: true },
  telemetry: false,
  onInit: async (payload) => {
    await ensureInitialAdmin(payload)
  },
})
