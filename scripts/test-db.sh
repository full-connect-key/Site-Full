#!/usr/bin/env bash
# Cria/atualiza o banco de TESTE (fck_wp_test) com o seed + fixtures fictícias
# usadas pelos testes e2e. Nunca toca o banco de revisão.
set -euo pipefail
cd "$(dirname "$0")/.."
export FCK_DB_NAME="${FCK_TEST_DB:-fck_wp_test}"
WP="wp --allow-root --path=$(pwd)/.wp-local"
$WP db create 2>/dev/null || true
if ! $WP core is-installed 2>/dev/null; then
  $WP core install --url="${WP_TEST_URL:-http://localhost:8081}" --title="Full Connect Key (teste)" --admin_user=admin --admin_password=dev-only-change-me-123 --admin_email=admin@localhost.test --skip-email
  $WP post delete 1 2 3 --force >/dev/null 2>&1 || true
fi
$WP theme activate fullconnectkey >/dev/null
$WP plugin activate fck-core >/dev/null
$WP fck seed >/dev/null
$WP eval-file tests/fixtures/fixtures.php
echo "✓ banco de teste pronto ($FCK_DB_NAME). Servidor de teste: npm run serve:test (http://localhost:8081)"
