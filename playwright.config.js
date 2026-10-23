import { defineConfig, devices } from '@playwright/test'

// Testes rodam contra o banco de TESTE (fixtures), servido na porta 8081.
const baseURL = process.env.E2E_BASE_URL || 'http://localhost:8081'

/**
 * E2E contra o banco de TESTE (npm run test:db), servido na porta 8081.
 * Viewports do briefing: 375 / 768 / 1280 / 1440.
 */
export default defineConfig({
  testDir: './tests/e2e',
  timeout: 60_000,
  expect: { timeout: 10_000 },
  fullyParallel: false,
  workers: 1,
  retries: process.env.CI ? 1 : 0,
  reporter: [['list'], ['html', { open: 'never' }]],
  use: { baseURL, locale: 'pt-BR', trace: 'retain-on-failure' },
  projects: [
    { name: 'desktop-1280', use: { ...devices['Desktop Chrome'], viewport: { width: 1280, height: 800 } }, grepInvert: /@mobile-only/ },
    { name: 'mobile-375', use: { ...devices['Pixel 5'], viewport: { width: 375, height: 812 } }, grep: /@mobile/ },
    { name: 'tablet-768', use: { ...devices['Desktop Chrome'], viewport: { width: 768, height: 1024 }, hasTouch: true }, grep: /@responsive/ },
    { name: 'desktop-1440', use: { ...devices['Desktop Chrome'], viewport: { width: 1440, height: 900 } }, grep: /@responsive/ },
  ],
  webServer: process.env.E2E_BASE_URL
    ? undefined
    : { command: 'FCK_LOGIN_LIMIT=1000 FCK_DB_NAME=fck_site_test FCK_URL=http://localhost:8081 FCK_UPLOADS=uploads-test php -S 127.0.0.1:8081 -t . scripts/router.php', url: baseURL, reuseExistingServer: true, timeout: 30_000 },
})
