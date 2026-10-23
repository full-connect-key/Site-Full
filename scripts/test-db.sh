#!/usr/bin/env bash
# Recria o banco de TESTE (fck_site_test) com o seed + fixtures fictícias.
# Nunca toca o banco de revisão.
set -euo pipefail
cd "$(dirname "$0")/.."
export FCK_DB_NAME="${FCK_TEST_DB:-fck_site_test}" FCK_URL="${FCK_TEST_URL:-http://localhost:8081}" FCK_UPLOADS=uploads-test
rm -rf uploads-test && mkdir -p uploads-test && cp uploads/.htaccess uploads-test/
mysql -u"${DB_ROOT_USER:-root}" -e "DROP DATABASE IF EXISTS \`$FCK_DB_NAME\`; CREATE DATABASE \`$FCK_DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL ON \`$FCK_DB_NAME\`.* TO 'fck'@'localhost';"
php scripts/fck.php migrate
php scripts/fck.php seed >/dev/null
php scripts/fck.php create-admin e2e-admin@localhost.test e2e-admin-pass-123
php scripts/fck.php fixtures
echo "✓ banco de teste pronto. Servidor: npm run serve:test (http://localhost:8081)"
