import { defineConfig, devices } from '@playwright/test'
import 'dotenv/config'

// E2E roda contra o banco de TESTE: fixtures nunca tocam o banco de revisão.
if (process.env.DATABASE_URL_TEST) process.env.DATABASE_URL = process.env.DATABASE_URL_TEST

const PORT = Number(process.env.E2E_PORT || 3100)
const baseURL = process.env.E2E_BASE_URL || `http://localhost:${PORT}`

/**
 * E2E: por padrão reaproveita um servidor já rodando (pnpm dev ou pnpm start).
 * Viewports do briefing: 375 / 768 / 1280 / 1440.
 */
export default defineConfig({
  testDir: './tests/e2e',
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : 2,
  timeout: 60_000,
  expect: { timeout: 10_000 },
  reporter: [['list'], ['html', { open: 'never' }]],
  use: {
    baseURL,
    trace: 'retain-on-failure',
    locale: 'pt-BR',
  },
  projects: [
    { name: 'desktop-1280', use: { ...devices['Desktop Chrome'], viewport: { width: 1280, height: 800 } } },
    {
      name: 'mobile-375',
      use: { ...devices['iPhone 13'], defaultBrowserType: 'chromium', viewport: { width: 375, height: 812 } },
      grep: /@mobile/,
    },
    {
      name: 'tablet-768',
      use: { ...devices['Desktop Chrome'], viewport: { width: 768, height: 1024 }, hasTouch: true },
      grep: /@responsive/,
    },
    {
      name: 'desktop-1440',
      use: { ...devices['Desktop Chrome'], viewport: { width: 1440, height: 900 } },
      grep: /@responsive/,
    },
  ],
  webServer: process.env.E2E_BASE_URL
    ? undefined
    : {
        command: `pnpm exec next start -p ${PORT}`,
        port: PORT,
        env: {
          DATABASE_URL: process.env.DATABASE_URL || '',
          NEXT_PUBLIC_SERVER_URL: baseURL,
          E2E: 'true',
        },
        reuseExistingServer: true,
        timeout: 180_000,
      },
})
