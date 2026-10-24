#!/usr/bin/env bash
# Testa a migração da Sprint ECO num banco descartável (fck_site_ecotest).
set -euo pipefail
cd "$(dirname "$0")/.."
DB=fck_site_ecotest
mysql -u"${DB_ROOT_USER:-root}" -e "DROP DATABASE IF EXISTS \`$DB\`; CREATE DATABASE \`$DB\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL ON \`$DB\`.* TO 'fck'@'localhost';"
trap 'mysql -u"${DB_ROOT_USER:-root}" -e "DROP DATABASE IF EXISTS \`'$DB'\`"; rm -rf uploads-ecotest' EXIT
mkdir -p uploads-ecotest
FCK_DB_NAME=$DB FCK_UPLOADS=uploads-ecotest php tests/php/eco_migration_test.php
