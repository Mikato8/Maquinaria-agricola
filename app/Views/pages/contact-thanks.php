<?php
/** @var string $name */
?><section class="section">
    <div class="container">
        <div class="empty-state">
            <span class="empty-icon"><?= icon('check-circle') ?></span>
            <h1>Gracias, <?= e(first_name($name)) ?>.</h1>
            <p>Recibimos tu mensaje. Si requiere respuesta, te escribiremos al correo que dejaste. Para correcciones técnicas, revisa que hayas incluido modelo, año y fuente: eso acelera la verificación.</p>
            <div class="empty-actions">
                <a class="btn btn-primary" href="<?= e(url('/')) ?>">Volver al inicio</a>
                <a class="btn btn-outline" href="<?= e(url('/fallas')) ?>">Explorar fallas</a>
            </div>
        </div>
    </div>
</section>
