<?php
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Nosotros</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Quiénes somos</span>
        <h1>Una base técnica abierta para quien trabaja con máquinas en el campo.</h1>
        <p class="page-hero-lead">
            AgroMáquina MX reúne fichas de tractores e implementos, diagnóstico de fallas, mantenimiento y tecnología
            de precisión con un criterio: que la información sirva para decidir y reparar, no para llenar un catálogo.
        </p>
    </div>
</section>

<div class="container prose-page">
    <div class="prose-page-grid">
        <article class="prose">
            <h2>Por qué existe</h2>
            <p>En México la información de maquinaria está dispersa entre folletos de distribuidor, manuales en inglés, grupos de WhatsApp y la memoria de los técnicos. Esa dispersión encarece la compra, retrasa la reparación y hace que se repitan errores de acople.</p>
            <p>Este portal ordena lo que se puede verificar: potencia y levante de una máquina, requisitos de un implemento, pasos de diagnóstico de una falla, intervalos de mantenimiento y qué tecnología funciona con conectividad limitada. Cuando no hay certeza, se dice.</p>

            <h2>Qué puedes hacer aquí</h2>
            <ul>
                <li><strong>Consultar sin registro.</strong> Todo el catálogo, las fallas, las guías y el directorio son abiertos.</li>
                <li><strong>Calcular.</strong> Compatibilidad tractor–implemento, secuencia de labranza y costo por hora con supuestos visibles.</li>
                <li><strong>Documentar.</strong> Publicar un caso de taller con síntoma, diagnóstico, refacciones y costo, bajo revisión editorial.</li>
                <li><strong>Corregir.</strong> Reportar un dato equivocado con la fuente que lo desmiente.</li>
            </ul>

            <h2>Lo que no somos</h2>
            <ul>
                <li>No vendemos maquinaria, refacciones ni servicios de taller.</li>
                <li>No sustituimos el manual del fabricante, la garantía ni la valoración de un técnico certificado.</li>
                <li>No publicamos «el mejor tractor» sin condiciones: no existe una respuesta única sin cultivo, suelo, superficie, presupuesto y servicio local.</li>
                <li>No aceptamos contenido pagado disfrazado de ficha técnica.</li>
            </ul>

            <h2>Cómo se sostiene</h2>
            <p>Es un proyecto independiente. Los costos de infraestructura son modestos y el contenido se produce con trabajo editorial. Si en el futuro hay patrocinios, se marcarán de forma explícita y no alterarán fichas ni recomendaciones.</p>

            <h2>Criterio editorial</h2>
            <p>Publicamos con tres reglas: fuente identificable, fecha de revisión y límites declarados. Las especificaciones se contrastan con fichas de distribuidor o manuales públicos; los precios se expresan como rango y se revisan por temporada. Las fallas se describen por síntoma y prueba, no por «cambia esta pieza».</p>

            <div class="callout">
                <strong>¿Ves un error?</strong> Escríbenos con la ficha, la página del manual y la edición. Corregimos y dejamos constancia.
            </div>

            <div class="cta-actions">
                <a class="btn btn-primary" href="<?= e(url('/fuentes')) ?>">Ver fuentes y método <?= icon('arrow-right', 'icon icon-sm') ?></a>
                <a class="btn btn-outline" href="<?= e(url('/contacto')) ?>">Escribir al equipo</a>
            </div>
        </article>

        <aside class="prose-aside">
            <div class="aside-card">
                <h3>En números</h3>
                <div class="aside-list">
                    <span>Máquinas fichadas <strong><?= (int) \App\Core\Database::value("select count(*) from machines where status = 'publicado'") ?></strong></span>
                    <span>Implementos <strong><?= (int) \App\Core\Database::value("select count(*) from implements where status = 'publicado'") ?></strong></span>
                    <span>Fallas documentadas <strong><?= (int) \App\Core\Database::value("select count(*) from faults where status = 'publicado'") ?></strong></span>
                    <span>Recursos de tecnología <strong><?= (int) \App\Core\Database::value('select count(*) from tech_resources') ?></strong></span>
                    <span>Casos de taller <strong><?= (int) \App\Core\Database::value("select count(*) from workshop_cases where status = 'publicado'") ?></strong></span>
                </div>
            </div>
            <div class="aside-card">
                <h3>Principios</h3>
                <ul class="check-list">
                    <li><?= icon('check', 'icon icon-sm') ?> <span>Dato con fuente o sin dato.</span></li>
                    <li><?= icon('check', 'icon icon-sm') ?> <span>Cálculo con supuestos visibles.</span></li>
                    <li><?= icon('check', 'icon icon-sm') ?> <span>Sin marca patrocinada en recomendaciones.</span></li>
                    <li><?= icon('check', 'icon icon-sm') ?> <span>Corrección pública y fechada.</span></li>
                </ul>
            </div>
        </aside>
    </div>
</div>
