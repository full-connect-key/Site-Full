#!/usr/bin/env bash
# Gera os .zip do tema e do plugin para enviar pelo painel do WordPress
# (Aparência › Temas › Adicionar › Enviar / Plugins › Adicionar › Enviar).
set -euo pipefail
cd "$(dirname "$0")/.."
mkdir -p dist
rm -f dist/fullconnectkey.zip dist/fck-core.zip
(cd wp-content/themes && zip -qr ../../dist/fullconnectkey.zip fullconnectkey -x '*.DS_Store')
(cd wp-content/plugins && zip -qr ../../dist/fck-core.zip fck-core -x '*.DS_Store')
echo "✓ dist/fullconnectkey.zip e dist/fck-core.zip"
