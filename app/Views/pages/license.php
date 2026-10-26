<?php
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Licencia</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Reutilización</span>
        <h1>Licencia de contenido y código</h1>
        <p class="page-hero-lead">Queremos que el material se use, se comparta y se corrija. Estas son las condiciones.</p>
    </div>
</section>

<div class="container prose-page">
    <article class="prose">
        <h2>Código fuente</h2>
        <p>El código de este portal se publica bajo licencia <strong>MIT</strong>. Puedes usarlo, modificarlo y distribuirlo conservando el aviso de copyright. No hay garantía de ningún tipo.</p>

        <h2>Contenido editorial</h2>
        <p>Los textos originales —fichas, guías, fallas, glosario y páginas institucionales— se publican bajo <strong>Creative Commons Atribución-CompartirIgual 4.0 Internacional (CC BY-SA 4.0)</strong>.</p>
        <ul>
            <li><strong>Atribución:</strong> menciona «AgroMáquina MX» y enlaza a la ficha original.</li>
            <li><strong>CompartirIgual:</strong> si adaptas el material, distribúyelo con la misma licencia.</li>
            <li><strong>Sin aval:</strong> no sugieras que el portal respalda tu producto o servicio.</li>
        </ul>

        <h2>Casos de taller de la comunidad</h2>
        <p>Cada autor conserva la autoría de su caso. Al publicarlo otorga al portal una licencia no exclusiva de publicación y permite que el caso se comparta con atribución al autor y al portal. Para reutilizarlo fuera del portal, solicita permiso al autor.</p>

        <h2>Imágenes</h2>
        <p>Las imágenes subidas por los autores pertenecen a quien las subió y se muestran con su autorización. Las ilustraciones generadas por el portal (iconos, patrones, portadas) son parte del diseño y se comparten con la misma licencia del contenido editorial.</p>

        <h2>Recursos de terceros</h2>
        <p>Las fichas de software, datasets y modelos enlazan a proyectos de terceros. Cada uno conserva su licencia (MIT, Apache 2.0, GPL, Creative Commons, comercial, etc.). La ficha indica la licencia declarada al momento de la verificación, pero debes confirmarla en la fuente antes de usarla.</p>

        <h2>Marcas</h2>
        <p>Los nombres y logotipos de fabricantes son marcas de sus titulares y se usan únicamente con fines descriptivos e informativos. AgroMáquina MX no está afiliado a ningún fabricante.</p>

        <h2>Cómo citar</h2>
        <div class="citation-box">
            <p>AgroMáquina MX. «<em>Título de la ficha</em>». Consultado el <?= e(date('d/m/Y')) ?>. <?= e(url('/')) ?></p>
        </div>
    </article>
</div>
