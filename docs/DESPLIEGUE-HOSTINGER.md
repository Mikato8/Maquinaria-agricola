# Despliegue en Hostinger

Documento del despliegue real de **AgroMáquina MX** en el plan Business de Hostinger, con PostgreSQL en Neon.

## 1. Arquitectura

```text
lightslategrey-aardvark-281629.hostingersite.com
   │ HTTPS
   ▼
~/domains/lightslategrey-aardvark-281629.hostingersite.com/public_html/
   ├── .htaccess              redirige a public/ y bloquea rutas sensibles
   ├── public/                front controller, assets y uploads
   ├── app/                   código, fuera del alcance directo
   ├── config/
   ├── database/
   ├── storage/               logs, sesiones y comprobantes privados
   └── .env                   credenciales, permisos 600
          │
          └── pdo_pgsql (TLS 5432) ──► Neon PostgreSQL
```

El documento raíz del sitio es `public_html`. El `.htaccess` raíz reescribe a `public/` y bloquea `app/`, `config/`, `database/`, `storage/`, `bin/`, `docs/`, `routes/`, `.env` y archivos de repositorio.

## 2. Requisitos verificados

- PHP 8.3.36 (CLI y FPM) en Hostinger.
- Extensiones presentes: `pdo_pgsql`, `curl`, `mbstring`, `fileinfo`, `gd`, `intl`, `zip`.
- Acceso SSH por el puerto **65002** con llave ED25519.
- SSL activo para el subdominio.
- Base Neon `neondb` (PostgreSQL 18.6) en el proyecto `ep-rapid-paper-b42m9jen`.

## 3. La cadena de conexión (punto crítico)

Hostinger usa una `libpq` antigua que **no envía SNI**. Neon rechaza la conexión con:

```text
Endpoint ID is not specified. Either please upgrade the postgres client library (libpq)
for SNI support or pass the endpoint ID ... as a parameter: '?options=endpoint%3D<endpoint-id>'
```

Solución: usar el host **directo** (sin `-pooler`) y añadir el endpoint en `options`, codificado:

```dotenv
DATABASE_URL="postgresql://neondb_owner:CLAVE@ep-rapid-paper-b42m9jen.c-6.us-east-2.aws.neon.tech/neondb?sslmode=require&channel_binding=require&options=endpoint%3Dep-rapid-paper-b42m9jen"
DB_DRIVER=pdo
```

Notas:

- No mezcles el host `-pooler` con un endpoint sin `-pooler`: Neon responde `Inconsistent project name inferred from SNI`.
- Con el endpoint **pooler** (`...-pooler.c-6...`) el `options` debe ser `endpoint=ep-rapid-paper-b42m9jen-pooler`.
- El pooler de Neon puede devolver `cached plan must not change result type` con PDO y `ATTR_EMULATE_PREPARES=false` después de un `ALTER TABLE`. Para este proyecto se usa el host directo.
- `PdoDatabase` reintenta una vez la conexión si Neon la rechaza de forma transitoria.

## 4. Empaquetado

```bash
bin/empaquetar.sh /tmp/agromaquina.tar.gz
```

Excluye `.env`, `.git`, capturas, logs, sesiones, caché, comprobantes y uploads. Usa `COPYFILE_DISABLE=1` para no subir archivos `._*` de macOS (en el servidor provocan 403 y basura).

## 5. Subida

```bash
SSH_KEY=../data-agro/.ssh/hostinger_ed25519 \
REMOTE_DIR=domains/lightslategrey-aardvark-281629.hostingersite.com/public_html \
bin/desplegar.sh
```

El script:

1. Empaqueta y sube por `scp`.
2. Extrae en `public_html`.
3. Elimina `._*` y ajusta permisos (`644` archivos, `755` directorios).
4. Crea y da permisos `775` a `storage/` y `public/uploads/`.
5. Ejecuta migraciones y `bin/check.php`.

### Permisos (causa del 403 en CSS)

Los archivos extraídos desde macOS conservan modo `600`, y Apache —que sirve los estáticos con otro usuario— responde **403**. Tras cada despliegue:

```bash
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod 600 .env
chmod -R 775 storage public/uploads
```

## 6. `.env` de producción

```dotenv
APP_NAME="AgroMáquina MX"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://lightslategrey-aardvark-281629.hostingersite.com
APP_TIMEZONE=America/Mexico_City
APP_LOCALE=es_MX
APP_KEY=<32 bytes hex>

DATABASE_URL="postgresql://…&options=endpoint%3Dep-rapid-paper-b42m9jen"
DB_DRIVER=pdo

SESSION_NAME=agromaquina_session
SESSION_LIFETIME=7200
SESSION_SECURE=true

MAIL_ENABLED=false
AUTO_VERIFY_USERS=false
```

`APP_KEY` se genera con `php -r 'echo bin2hex(random_bytes(24));'`. El archivo debe quedar en `600`.

## 7. Migraciones, seed y administrador

Se ejecutan desde local o por SSH, **nunca desde el navegador**:

```bash
php bin/migrate.php
php bin/seed.php --no-demo    # catálogo y guías, sin cuentas ni casos demo
php bin/create-admin.php --name="Comité Editorial" --email="admin@…" --password="…"
php bin/check.php
```

La base ya quedó sembrada con catálogo, guías, 7 casos demostrativos y cuentas de ejemplo (`Demo2026$`). En producción conviene:

1. Cambiar la contraseña de las cuentas demo o eliminarlas.
2. Crear la cuenta administradora real.
3. Revisar `AUTO_VERIFY_USERS=false` para que el alta de técnicos requiera verificación.

## 8. Verificación posterior

```bash
curl -I https://lightslategrey-aardvark-281629.hostingersite.com/
curl -s https://…/salud
curl -s -o /dev/null -w '%{http_code}\n' https://…/assets/css/app.css
curl -s -o /dev/null -w '%{http_code}\n' https://…/maquinas/john-deere-5075e/pdf
```

Comprobado en este despliegue:

- Portada, catálogo, detalle, fallas, calculadoras, tecnología, proveedores, taller, guías, glosario, sitemap y robots: **200**.
- CSS y JS: **200** (tras corregir permisos).
- PDF de ficha y de caso: **200**, `application/pdf`.
- Login del administrador y panel editorial: **200**.

## 9. Rollback

```bash
# En el servidor
cd ~/domains/lightslategrey-aardvark-281629.hostingersite.com
mv public_html public_html.bak-$(date +%Y%m%d-%H%M)
# Restaurar el respaldo anterior
```

## 10. Bitácora

### 2026-10-26 · Despliegue inicial

- Sitio creado en Hostinger; se eliminó `default.php`.
- Primer intento: **500** por `No fue posible conectar con PostgreSQL`. Causa: `libpq` sin SNI. Se añadió `options=endpoint%3Dep-rapid-paper-b42m9jen` y la conexión quedó en `pdo_pgsql` contra el host directo.
- Segundo hallazgo: CSS y JS respondían **403** por permisos `600` heredados del empaquetado en macOS. Se normalizaron a `644/755`.
- Migraciones aplicadas (4) y `bin/check.php` en verde.
- Cuenta administradora creada; credenciales guardadas fuera del repositorio en `docs/credenciales-admin.txt` (excluido por `.gitignore`).
