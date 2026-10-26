# AgroMáquina MX

**Base técnica abierta de maquinaria agrícola para México: catálogo, taller, implementos y agricultura de precisión.**

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb4?logo=php&logoColor=white)](https://www.php.net/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15%2B-336791?logo=postgresql&logoColor=white)](https://www.postgresql.org/)
[![Licencia](https://img.shields.io/badge/c%C3%B3digo-MIT-0d5c4d)](LICENSE)

## Qué es

AgroMáquina MX es un portal en español de México para consultar, comparar y reparar maquinaria agrícola. Reúne:

- **Catálogo técnico** de tractores, cosechadoras, pulverizadoras, forrajeras, motocultores y equipos especializados, con potencia, levante, caudal hidráulico, transmisión, peso y precio de referencia.
- **Implementos** con requisitos de potencia, ancho, profundidad, peso, calibración y seguridad.
- **Calculadoras de ingeniería**: compatibilidad tractor–implemento, recomendador de secuencia de labranza y costo por hora/hectárea.
- **Taller**: 30 fichas de falla con causas ordenadas por probabilidad, pruebas de diagnóstico, herramientas, refacciones y prevención; 16 códigos SPN/FMI; planes de mantenimiento por horas.
- **Tecnología**: software, hardware RTK, ISOBUS, datasets y modelos de IA con licencia, requisitos y viabilidad en México.
- **Comunidad**: casos de taller documentados por técnicos y productores, con revisión editorial, comentarios, favoritos y PDF.
- **Directorio** de distribuidores, refaccionarios, talleres, renta y financiamiento.
- **Guías** largas y **glosario** de 50 términos.

> **Aviso.** El contenido es educativo y de referencia. No sustituye el manual del fabricante, la garantía, la normativa aplicable ni la valoración de un técnico certificado. Verifica siempre especificaciones, dosis, torques y procedimientos en la máquina concreta.

## Demo

Despliegue en Hostinger con base PostgreSQL en Neon:

**<https://lightslategrey-aardvark-281629.hostingersite.com>**

El catálogo, las guías y las calculadoras son de consulta libre. Para publicar casos de taller se requiere cuenta verificada con cédula, registro de taller o revisión manual.

### Capturas

| Portada | Catálogo de máquinas |
| --- | --- |
| ![Portada](docs/capturas/home.png) | ![Catálogo](docs/capturas/maquinas.png) |

| Ficha técnica | Compatibilidad tractor–implemento |
| --- | --- |
| ![Ficha](docs/capturas/detalle.png) | ![Calculadora](docs/capturas/compatibilidad.png) |

| Fallas y diagnóstico | Mantenimiento por horas |
| --- | --- |
| ![Fallas](docs/capturas/fallas.png) | ![Mantenimiento](docs/capturas/mantenimiento.png) |

| Comunidad de taller | Tecnología y datos |
| --- | --- |
| ![Taller](docs/capturas/taller.png) | ![Tecnología](docs/capturas/tecnologia.png) |

Las capturas corresponden al contenido demostrativo. No contienen datos personales reales.

## Funcionalidades

### Catálogo y decisión de compra

- Fichas de máquinas con filtros por categoría, marca, tracción, potencia y precio; orden por potencia, precio o modelo.
- Comparador de hasta tres máquinas con resaltado del mejor valor por fila (potencia, levante, caudal, precio).
- PDF de ficha técnica con especificaciones y compatibilidad orientativa con implementos.
- Implementos por función con potencia requerida, ancho, profundidad y calibración.
- Precios de referencia en MXN, con nota de vigencia y sin IVA.

### Calculadoras

1. **Compatibilidad tractor–implemento** (`/compatibilidad`): fuerza de tiro por resistencia específica del suelo, potencia requerida, carga del motor, levante a 610 mm, caudal, lastre, rendimiento, consumo de diésel y costo por hectárea.
2. **Recomendador** (`/recomendador`): secuencia de labranza, siembra y protección según superficie, cultivo, suelo, pendiente, potencia y sistema de labranza, con ancho, horas y costo estimado.
3. **Costos** (`/costos`): depreciación, interés, seguro, mantenimiento, diésel y operador; costo por hora y por hectárea.

Todas muestran supuestos, fórmulas y límites. Los resultados son orientativos y requieren validación en campo.

### Taller

- 30 fallas con síntoma, causas (probabilidad, prueba, corrección), procedimiento paso a paso, herramientas, refacciones, costo, tiempo, dificultad y seguridad.
- 16 códigos SPN/FMI del estándar J1939 con explicación, causas y acción.
- Planes de mantenimiento a 25, 50, 250, 500, 1000 y 2000 horas para tractor, compacto, cosechadora, pulverizadora, forrajera y motocultor; checklist imprimible.
- Búsqueda full-text en español, insensible a acentos, y filtros por sistema, máquina y gravedad.

### Tecnología y datos

- 27 fichas de software (AgOpenGPS, farmOS, QGIS, plataformas comerciales), hardware RTK (Emlid, u-blox, ArduSimple, terminales ISOBUS), datasets (AgML, iNatAg, Agriculture-Vision, Hokkaido), modelos (agriculture-bert, ViT de enfermedades, TractorGPT) y normas (ISO 11783, J1939).
- Cada ficha indica licencia, plataforma, requisitos, viabilidad en México, enlace y fecha de verificación.

### Comunidad y cuentas

- Registro como productor, técnico o taller; verificación con cédula, registro de taller o revisión manual.
- Casos de taller con folio, imágenes validadas y re-codificadas, costo, tiempo, dificultad y resultado.
- Comentarios de cuentas verificadas, reacciones «me resultó útil», favoritos y panel personal.
- Moderación editorial: cola de revisión, decisión de publicar/cambios/rechazar, destacados, verificación de cuentas, moderación de comentarios y bitácora de auditoría.
- PDF de cada caso de taller y perfil público del autor.
- Eliminación de cuenta con anonimización de datos personales.

### Infraestructura

- PHP 8.2+ sin Composer ni frameworks; HTML, CSS y JavaScript propios.
- PostgreSQL 15+ (probado en Neon 18) con `pg_trgm`, `citext`, `unaccent` y `tsvector` en español.
- Conexión por `pdo_pgsql` o por el endpoint SQL HTTPS de Neon (`DB_DRIVER=auto|pdo|http`).
- Apache/LiteSpeed en Hostinger; `php -S` en local.
- Protección CSRF, contraseñas con `password_hash`, límite de intentos de acceso, validación de archivos y permisos por rol.
- Sin cookies publicitarias ni dependencias de terceros en el navegador.

## Stack

| Capa | Tecnología |
| --- | --- |
| Lenguaje | PHP 8.2+ con `declare(strict_types=1)` |
| Base de datos | PostgreSQL 15+ en Neon; `pg_trgm`, `citext`, `unaccent`, `tsvector` |
| Acceso a datos | `pdo_pgsql` (TLS) o endpoint SQL HTTPS de Neon |
| Servidor | Apache/LiteSpeed en Hostinger; `php -S` en local |
| Frontend | HTML semántico, CSS propio y JavaScript sin dependencias |
| Correo | `mail()` de PHP, opcional con `MAIL_ENABLED=true` |
| PDF | Generador propio (`app/Support/SimplePdf.php`), sin librerías externas |

## Arquitectura

```text
Navegador
   │ HTTPS
   ▼
Hostinger · public_html
   ├─ .htaccess              redirige a public/ y bloquea rutas sensibles
   ├─ public/index.php       front controller
   ├─ app/Core               router, request, sesión, CSRF, BD, vistas
   ├─ app/Controllers        catálogo, herramientas, taller, cuenta, admin
   ├─ app/Repositories       SQL parametrizado por dominio
   ├─ app/Services           cálculos de ingeniería agrícola
   ├─ app/Views              plantillas PHP con layouts
   └─ database/              migraciones y seeds
          │
          └─ pdo_pgsql (TLS) ──► Neon PostgreSQL
```

### Modelo de datos

| Tabla | Contenido |
| --- | --- |
| `machines`, `brands` | Máquinas autopropulsadas y marcas |
| `implements` | Implementos con factor de resistencia específica (`draft_factor`) |
| `faults` | Fallas con causas y pasos en JSONB |
| `maintenance_tasks`, `dtc_codes` | Planes por horas y códigos SPN/FMI |
| `tech_resources` | Software, hardware, datasets, modelos, normas |
| `providers` | Directorio por tipo, estado y marca |
| `guides`, `glossary` | Contenido editorial largo |
| `workshop_cases`, `workshop_case_images` | Casos de taller y fotos |
| `comments`, `reactions`, `bookmarks` | Interacción comunitaria |
| `reviews`, `audit_log` | Moderación y trazabilidad |
| `users`, `password_resets`, `login_attempts` | Cuentas y seguridad |
| `inquiries` | Mensajes del formulario de contacto |

## Cálculos: supuestos y fórmulas

La calculadora de compatibilidad usa la fórmula clásica de tiro:

```text
F (kgf) = k · ancho(dm) · profundidad(dm) · f_suelo · f_humedad · f_pendiente
P (kW)  = F (N) · v (m/s) / (1000 · η_tracción)
```

- `k` es la resistencia específica en kgf/dm²; cada implemento puede declarar la suya y, si no, se usa el valor de su categoría.
- Factores de suelo: arenoso 0.78, franco 1.00, limoso 0.92, arcilloso 1.35, pedregoso 1.25.
- Humedad: seco 1.18, óptimo 1.00, húmedo 0.85.
- Pendiente: `1 + 0.012 · %`.
- Eficiencia de tracción: 2WD 0.70, 4WD 0.78, oruga 0.86.
- Rendimiento: ancho × velocidad × 0.78 / 10 (ha/h).
- Diésel: 0.23 L por HP·hora a la carga estimada.
- Costo por hora: depreciación lineal + interés del capital + seguro + mantenimiento + diésel + operador ajustado por aprovechamiento.

Los límites y advertencias se muestran junto al resultado; el portal no recomienda comprar una máquina solo por el cálculo.

## Puesta en marcha local

Requisitos: PHP 8.2+ con `pdo_pgsql`, `curl`, `mbstring`, `gd`, `intl`; PostgreSQL 15+ (o Neon).

```bash
cp .env.example .env
# Edita DATABASE_URL, APP_URL y APP_KEY

php bin/migrate.php         # crea el esquema
php bin/seed.php            # catálogo + guías + contenido demostrativo
php bin/seed.php --no-demo  # solo catálogo y contenido editorial
php bin/create-admin.php --name="Comité" --email="admin@ejemplo.mx" --password="UnaClaveLarga"
php bin/check.php           # diagnóstico

php -S 127.0.0.1:8090 -t public bin/router.php
```

Abre <http://127.0.0.1:8090>.

### Cuentas demostrativas

`php bin/seed.php` crea cuentas con la contraseña `Demo2026$`:

| Correo | Rol |
| --- | --- |
| `juan@ejemplo.mx` | Técnico verificado |
| `maria@ejemplo.mx` | Taller verificado |
| `carlos@ejemplo.mx` | Productor verificado |
| `luis@ejemplo.mx` | Técnico verificado |
| `editor@ejemplo.mx` | Editor |
| `pedro@ejemplo.mx` | Productor pendiente |

Los casos de taller y las cuentas están marcados con `is_demo = true`. `php bin/seed.php --force` recrea solo el contenido demostrativo.

## Despliegue

Instrucciones completas en [docs/DESPLIEGUE-HOSTINGER.md](docs/DESPLIEGUE-HOSTINGER.md). Resumen:

1. Crear el sitio y la base en Neon.
2. Empaquetar sin `.env`, `.git`, logs, sesiones ni capturas.
3. Subir por SSH/SFTP a `public_html` y extraer.
4. Crear `.env` de producción (permisos 600), con `APP_DEBUG=false` y `DB_DRIVER=pdo`.
5. Ejecutar migraciones y seed **desde local o por SSH**, nunca desde el navegador.
6. Crear la cuenta administradora y comprobar `/salud`.

## Estructura del repositorio

```text
app/               núcleo, controladores, repositorios, servicios, vistas
bin/               migraciones, seed, verificación, administrador, router local
config/            configuración de la aplicación
database/          migraciones SQL y seeds en PHP
docs/              despliegue, fuentes y método
public/            front controller, assets y uploads
routes/web.php     definición de rutas
storage/           logs, sesiones y comprobantes privados
```

## Contenido y licencias

- **Código:** MIT (ver [LICENSE](LICENSE)).
- **Contenido editorial:** CC BY-SA 4.0, con atribución a AgroMáquina MX.
- **Casos de taller:** autoría de quien los publica; el portal recibe licencia no exclusiva de publicación.
- **Recursos de terceros:** conservan su licencia (MIT, Apache-2.0, GPL, Creative Commons o comercial). La ficha indica la licencia declarada al momento de la verificación.

## Fuentes y verificación

Las especificaciones se contrastan con fichas de distribuidor y manuales públicos; los precios son rangos de referencia revisados por temporada; las fallas se documentan por síntoma y prueba, no por «cambia esta pieza». Cada recurso tecnológico indica la fecha de verificación.

Metodología y límites: [/fuentes](https://lightslategrey-aardvark-281629.hostingersite.com/fuentes). Correcciones: formulario de contacto con modelo, año, página del manual y edición.

## Créditos

Proyecto editorial independiente hecho en México. Inspirado en la necesidad real de técnicos, productores y talleres de contar con información verificable en español, y en el ecosistema abierto de agricultura de precisión (AgML, AgOpenGPS, farmOS, QGIS).
