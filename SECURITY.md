# Seguridad

## Reportar una vulnerabilidad

Escribe a través de [/contacto](https://lightslategrey-aardvark-281629.hostingersite.com/contacto) con el asunto «Seguridad» e incluye:

- Descripción del problema y su impacto.
- Pasos para reproducirlo.
- Versión o URL afectada.
- Si lo deseas, una propuesta de corrección.

Respondemos en un plazo de 5 días hábiles. No publicamos detalles hasta que el problema esté corregido.

## Alcance

Este portal es un proyecto editorial independiente sin programa de recompensas. Agradecemos los reportes responsables; no realizamos acciones legales contra quien investigue de buena fe y sin afectar a terceros.

## Medidas implementadas

- Contraseñas con `password_hash` (bcrypt) y rehash automático.
- CSRF en todos los formularios que modifican datos.
- Límite de intentos de acceso y registro en bitácora.
- Consultas SQL parametrizadas.
- Validación de archivos por tipo real, tamaño y dimensiones; re-codificación de imágenes.
- Sesiones con `HttpOnly`, `SameSite=Lax` y `Secure` en producción.
- Cabeceras de seguridad y política de contenido.
- Acceso al panel editorial por rol y auditoría de acciones.
- `.env` fuera del repositorio y con permisos `600` en el servidor.
