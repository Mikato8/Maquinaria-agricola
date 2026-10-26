# Cómo contribuir

## Reportar un error de contenido

Envía el formulario de [/contacto](https://lightslategrey-aardvark-281629.hostingersite.com/contacto) con:

- Modelo y año exactos de la máquina.
- Dato correcto y fuente (página del manual, boletín técnico, ficha de distribuidor).
- Si es una falla: síntoma, código, prueba y solución comprobada.

Corregimos y dejamos constancia de la fecha. No publicamos correcciones anónimas sin fuente verificable.

## Proponer un proveedor

Incluye nombre comercial, dirección, teléfono, marcas que atiende, servicios y sitio web. Publicamos solo datos verificables y sin costo. No aceptamos pagos por aparecer en el directorio.

## Publicar un caso de taller

1. Crea una cuenta como productor, técnico o taller.
2. Sube tu cédula o constancia de taller para verificación.
3. Documenta síntoma, diagnóstico, reparación, refacciones, costo y resultado.
4. Envía a revisión desde tu panel.

Un editor revisa coherencia técnica, datos verificables y privacidad. Los casos se publican con folio y crédito al autor.

## Contribuir al código

- PHP 8.2+ con `declare(strict_types=1)`, sin Composer ni frameworks.
- Respeta la estructura `app/Core`, `app/Controllers`, `app/Repositories`, `app/Services`, `app/Views`.
- SQL siempre parametrizado. No concatenes valores en consultas.
- Nuevas migraciones en `database/migrations` con numeración consecutiva; nunca edites una migración ya aplicada en producción.
- Contenido nuevo en `database/seeds` y ejecuta `php bin/seed.php` (idempotente).
- Antes de enviar: `php -l` en los archivos tocados y `php bin/check.php`.

## Estilo editorial

- Español de México, sin anglicismos innecesarios.
- Datos con fuente o sin dato; no se estima una especificación y se presenta como hecho.
- Sin contenido patrocinado disfrazado de ficha técnica.
- Las recomendaciones explican sus supuestos y límites.
