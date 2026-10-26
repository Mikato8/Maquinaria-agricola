#!/usr/bin/env bash
# Empaqueta el portal sin credenciales ni archivos de runtime.
# Uso: bin/empaquetar.sh [salida.tar.gz]
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="${1:-/tmp/agromaquina.tar.gz}"

cd "$ROOT"

COPYFILE_DISABLE=1 tar czf "$OUT" \
  --exclude='./.env' \
  --exclude='./.git' \
  --exclude='./.DS_Store' \
  --exclude='./capturas' \
  --exclude='./docs/credenciales-admin.txt' \
  --exclude='./docs/paquete' \
  --exclude='./storage/logs/*' \
  --exclude='./storage/sessions/*' \
  --exclude='./storage/cache/*' \
  --exclude='./storage/constancias/*' \
  --exclude='./public/uploads/*' \
  .

echo "Paquete listo: $OUT ($(du -h "$OUT" | cut -f1))"
