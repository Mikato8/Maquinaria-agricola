<?php
/** @var int $status */
/** @var string $message */
?>
<section class="page-hero error-hero">
    <div class="container">
        <span class="error-code"><?= (int) $status ?></span>
        <h1><?= $status === 404 ? 'No encontramos esta página' : ($status === 403 ? 'No tienes acceso' : ($status === 419 ? 'La sesión expiró' : 'Algo salió mal')) ?></h1>
        <p><?= e($message) ?></p>
        <div class="error-actions">
            <a class="btn btn-primary" href="<?= e(url('/')) ?>">Volver al inicio</a>
            <a class="btn btn-outline" href="<?= e(url('/maquinas')) ?>">Explorar máquinas</a>
            <a class="btn btn-ghost" href="<?= e(url('/fallas')) ?>">Ver fallas</a>
        </div>
        <?php if ($status === 419): ?>
            <p class="error-hint">Recarga la página y vuelve a enviar el formulario. Si el problema continúa, inicia sesión de nuevo.</p>
        <?php endif; ?>
    </div>
</section>
