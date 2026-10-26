#!/usr/bin/env bash
# Despliegue por SSH a Hostinger.
#
# Variables:
#   SSH_KEY   ruta a la llave privada (obligatoria)
#   SSH_HOST  usuario@IP (por defecto u771496034@45.93.101.150)
#   SSH_PORT  puerto (por defecto 65002)
#   REMOTE_DIR carpeta del sitio dentro del home (obligatoria)
#
# Uso:
#   SSH_KEY=~/.ssh/hostinger REMOTE_DIR=domains/mi-sitio/public_html bin/desplegar.sh
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SSH_KEY="${SSH_KEY:?Falta SSH_KEY}"
SSH_HOST="${SSH_HOST:-u771496034@45.93.101.150}"
SSH_PORT="${SSH_PORT:-65002}"
REMOTE_DIR="${REMOTE_DIR:?Falta REMOTE_DIR}"
PKG="/tmp/agromaquina-$(date +%Y%m%d-%H%M%S).tar.gz"

"$ROOT/bin/empaquetar.sh" "$PKG"

echo "→ Subiendo paquete…"
scp -i "$SSH_KEY" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null \
  -P "$SSH_PORT" "$PKG" "$SSH_HOST:/tmp/portal.tar.gz"

echo "→ Extrayendo y ajustando permisos…"
ssh -i "$SSH_KEY" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -p "$SSH_PORT" "$SSH_HOST" "
  set -e
  cd ~/$REMOTE_DIR
  tar xzf /tmp/portal.tar.gz
  find . -name '._*' -delete
  find . -type d -exec chmod 755 {} \;
  find . -type f -exec chmod 644 {} \;
  [ -f .env ] && chmod 600 .env
  mkdir -p storage/logs storage/sessions storage/cache storage/constancias public/uploads/taller public/uploads/avatares
  chmod -R 775 storage public/uploads
  php bin/migrate.php
  php bin/check.php | tail -5
"

echo "Listo. Verifica la URL del sitio y /salud."
