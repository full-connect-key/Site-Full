#!/usr/bin/env bash
# Monta um WordPress local em .wp-local/ com o tema e o plugin do repositório
# (links simbólicos), cria o banco e importa o conteúdo inicial.
# Requisitos: PHP 8.1+, MySQL/MariaDB, WP-CLI (wp).
set -euo pipefail
cd "$(dirname "$0")/.."
ROOT="$(pwd)"
DB_NAME="${DB_NAME:-fck_wp}"; DB_USER="${DB_USER:-fck}"; DB_PASS="${DB_PASS:-fck_dev}"; DB_HOST="${DB_HOST:-localhost}"
URL="${WP_URL:-http://localhost:8080}"
ADMIN_USER="${ADMIN_USER:-admin}"; ADMIN_PASS="${ADMIN_PASS:-dev-only-change-me-123}"
WP="wp --allow-root --path=$ROOT/.wp-local"

mkdir -p .wp-local
if [ ! -f .wp-local/wp-load.php ]; then $WP core download --locale=pt_BR --quiet; fi
if [ ! -f .wp-local/wp-config.php ]; then
  $WP config create --dbname="$DB_NAME" --dbuser="$DB_USER" --dbpass="$DB_PASS" --dbhost="$DB_HOST" --locale=pt_BR --skip-check --extra-php <<'PHP'
define( 'WP_ENVIRONMENT_TYPE', 'local' );
if ( getenv( 'FCK_DB_NAME' ) ) { define( 'UPLOADS', 'wp-content/uploads-test' ); }
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'DISALLOW_FILE_EDIT', true );
define( 'FCK_SMTP_HOST', '127.0.0.1' );
define( 'FCK_SMTP_PORT', 1025 );
PHP
  sed -i "s/define( 'DB_NAME', '$DB_NAME' );/define( 'DB_NAME', getenv( 'FCK_DB_NAME' ) ?: '$DB_NAME' );/" .wp-local/wp-config.php
fi
mkdir -p .wp-local/wp-content/themes .wp-local/wp-content/plugins
ln -sfn "$ROOT/wp-content/themes/fullconnectkey" .wp-local/wp-content/themes/fullconnectkey
ln -sfn "$ROOT/wp-content/plugins/fck-core" .wp-local/wp-content/plugins/fck-core
$WP db create 2>/dev/null || true
if ! $WP core is-installed 2>/dev/null; then
  $WP core install --url="$URL" --title="Full Connect Key" --admin_user="$ADMIN_USER" --admin_password="$ADMIN_PASS" --admin_email=admin@localhost.test --skip-email
  $WP post delete 1 2 3 --force >/dev/null 2>&1 || true
fi
$WP option update blog_public 0 >/dev/null
$WP theme activate fullconnectkey
$WP plugin activate fck-core
$WP fck seed
echo "Pronto. Rode: npm run serve  →  $URL  (painel: $URL/wp-admin, usuário $ADMIN_USER)"

# Banco de TESTE (fixtures do e2e; nunca o banco de revisão)
if [ "${WITH_TEST_DB:-1}" = "1" ]; then
  bash scripts/test-db.sh
fi
