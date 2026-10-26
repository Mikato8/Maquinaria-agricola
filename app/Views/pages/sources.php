<?php
/** @var array<int, array<string, mixed>> $techResources */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Fuentes y método</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Transparencia</span>
        <h1>Cómo se construye y se verifica cada ficha.</h1>
        <p class="page-hero-lead">
            Ningún dato entra sin una fuente identificable. Aquí están los criterios, las fechas de revisión y los límites
            de lo que podemos afirmar.
        </p>
    </div>
</section>

<div class="container prose-page">
    <article class="prose">
        <h2>1. Especificaciones de máquinas</h2>
        <p>Se toman de fichas técnicas de distribuidor, manuales de operación y bases públicas de fabricantes. Se registran potencia nominal y a la TDF, cilindrada, transmisión, caudal hidráulico, capacidad de levante y peso. Cuando un dato no está disponible se deja como «no declarado» en lugar de estimarlo.</p>
        <p><strong>Límite:</strong> la configuración cambia por año, mercado y opciones. Un modelo 2020 puede diferir de uno 2024 con el mismo nombre comercial. Confirma siempre con el concesionario y con el manual de la unidad específica.</p>

        <h2>2. Precios</h2>
        <p>Los precios son rangos de referencia en pesos mexicanos para equipo nuevo, sin IVA y sin implementos, recopilados de cotizaciones públicas y listas de distribuidor. Se revisan por temporada. No son oferta ni cotización.</p>

        <h2>3. Fallas y diagnóstico</h2>
        <p>Cada falla parte de un síntoma observado y se organiza en causas por probabilidad, prueba de confirmación y corrección. Las referencias son manuales de servicio, boletines técnicos y experiencia de taller documentada. Los costos son rangos de refacción, no de mano de obra, y varían por región y disponibilidad.</p>
        <p><strong>Límite:</strong> un síntoma puede tener causas distintas según el modelo y el historial. Las fichas son orientativas y no sustituyen el diagnóstico en máquina.</p>

        <h2>4. Cálculos de ingeniería</h2>
        <p>La compatibilidad tractor–implemento usa la fórmula de tiro con resistencia específica del suelo y factores de humedad y pendiente. Los valores de <em>k</em> están en rangos publicados para labranza. La potencia se corrige por eficiencia de tracción (2WD, 4WD, oruga). El consumo de diésel se estima con 0.23 L por HP·hora a la carga calculada.</p>
        <p><strong>Límite:</strong> son estimaciones de dimensionamiento, no una prueba de campo. La humedad real, el estado del suelo, el lastre, las llantas y la habilidad del operador cambian el resultado.</p>

        <h2>5. Tecnología, datos y modelos</h2>
        <p>Cada recurso se verificó en la fecha indicada: URL activa, licencia declarada y tipo de acceso. Los conjuntos de datos y modelos se describen por tarea, tamaño y requisito, no por popularidad. Los que están en un idioma o ecosistema distinto se advierten.</p>

        <h2>6. Correcciones</h2>
        <p>Si detectas un error, escribe a <a href="<?= e(url('/contacto')) ?>">contacto</a> con la ficha, la página y la edición del manual. Se corrige y se anota la fecha. No borramos el historial de una corrección relevante.</p>

        <h2>Recursos de tecnología verificados</h2>
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Recurso</th><th>Tipo</th><th>Autor</th><th>Licencia</th><th>Verificado</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($techResources as $resource): ?>
                    <tr>
                        <td><?= e($resource['name']) ?></td>
                        <td><?= e(tech_kind_label((string) $resource['kind'])) ?></td>
                        <td><?= e($resource['vendor'] ?: '—') ?></td>
                        <td><?= e($resource['license'] ?: 'No declarada') ?></td>
                        <td><?= $resource['verified_on'] ? e(format_date((string) $resource['verified_on'], 'd/m/Y')) : '—' ?></td>
                        <td><?php if ($resource['url'] !== ''): ?><a class="text-link" href="<?= e($resource['url']) ?>" target="_blank" rel="noopener nofollow">Abrir</a><?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</div>
