#!/usr/bin/env bash
# Verifica a sintaxe de todos os arquivos PHP do tema e do plugin.
set -euo pipefail
cd "$(dirname "$0")/.."
fail=0
while IFS= read -r f; do
  out=$(php -l "$f" 2>&1) || { echo "$out"; fail=1; }
done < <(find wp-content -name '*.php')
[ $fail -eq 0 ] && echo "✓ lint:php — sintaxe PHP ok."
exit $fail
