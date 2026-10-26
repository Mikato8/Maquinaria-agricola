<?php
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Privacidad</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Datos personales</span>
        <h1>Aviso de privacidad</h1>
        <p class="page-hero-lead">Última actualización: <?= e(format_date(date('Y-m-d'), 'd \d\e M \d\e Y')) ?>. Aplica a este portal y a las cuentas registradas.</p>
    </div>
</section>

<div class="container prose-page">
    <article class="prose">
        <h2>Responsable</h2>
        <p>AgroMáquina MX es un proyecto editorial independiente operado desde México. Para cualquier asunto de datos personales puedes escribir a <a href="<?= e(url('/contacto')) ?>">nuestro formulario de contacto</a> con el asunto «Datos personales».</p>

        <h2>Qué datos recabamos</h2>
        <ul>
            <li><strong>Cuenta:</strong> nombre, correo electrónico, contraseña cifrada, rol, estado, estado de la República, ciudad, teléfono opcional, especialidad, taller y semblanza.</li>
            <li><strong>Verificación:</strong> comprobante de cédula o constancia de taller, guardado en un directorio privado y visible solo para el comité editorial.</li>
            <li><strong>Contenido:</strong> casos de taller, comentarios, imágenes que decidas subir y marcas de «útil» o favoritos.</li>
            <li><strong>Contacto:</strong> nombre, correo, teléfono opcional y mensaje del formulario.</li>
            <li><strong>Técnicos:</strong> dirección IP, fecha y resultado de los intentos de acceso, para prevenir abuso.</li>
        </ul>

        <h2>Finalidades</h2>
        <ul>
            <li>Crear y autenticar tu cuenta, y verificar tu identidad profesional o de taller.</li>
            <li>Publicar y moderar el contenido que envías, con crédito de autor.</li>
            <li>Responder mensajes y correcciones.</li>
            <li>Prevenir fraude, abuso y accesos no autorizados.</li>
            <li>Generar estadísticas agregadas de uso, sin identificar personas.</li>
        </ul>
        <p>No usamos tus datos para publicidad de terceros ni vendemos bases de datos.</p>

        <h2>Cookies y sesión</h2>
        <p>Usamos una cookie de sesión necesaria para mantener tu acceso y una cookie opcional para recordarte en el dispositivo si marcas «mantener sesión». No usamos cookies publicitarias ni de seguimiento entre sitios.</p>

        <h2>Transferencias</h2>
        <p>La base de datos está alojada en Neon (PostgreSQL en la nube) y el sitio en Hostinger. Ambos proveedores procesan datos por cuenta de este proyecto con medidas de seguridad contractuales. No transferimos datos a otras personas sin tu consentimiento, salvo obligación legal.</p>

        <h2>Conservación</h2>
        <p>Los datos de cuenta se conservan mientras la cuenta esté activa. Si eliminas tu cuenta, se anonimizan tu nombre, correo, teléfono, semblanza y foto; los casos publicados permanecen sin datos de autor para no romper el hilo editorial. Los intentos de acceso se conservan 12 meses.</p>

        <h2>Derechos ARCO</h2>
        <p>Puedes ejercer tus derechos de acceso, rectificación, cancelación y oposición escribiendo desde el correo registrado. Respondemos en un plazo máximo de 20 días hábiles. También puedes solicitar la portabilidad de tu contenido en formato de texto.</p>

        <h2>Seguridad</h2>
        <p>Las contraseñas se guardan con <code>password_hash</code> (bcrypt). Las conexiones usan TLS. El acceso al panel editorial está restringido por rol y registrado en bitácora. Ningún sistema es infalible: si detectas una vulnerabilidad, repórtala de inmediato.</p>

        <h2>Menores de edad</h2>
        <p>El portal está dirigido a personas mayores de 18 años con actividad profesional o formativa en el sector. No recabamos conscientemente datos de menores.</p>

        <h2>Cambios</h2>
        <p>Si modificamos este aviso, actualizaremos la fecha y avisaremos en la portada. El uso continuado después del cambio implica aceptación.</p>
    </article>
</div>
