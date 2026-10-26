<?php
/** @var array<string, mixed> $provider */
$brands = pg_array($provider['brands'] ?? []);
$states = pg_array($provider['states'] ?? []);
$services = pg_array($provider['services'] ?? []);
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <a href="<?= e(url('/proveedores')) ?>">Proveedores</a>
            <a href="<?= e(url('/proveedores?type=' . $provider['type'])) ?>"><?= e(provider_type_label((string) $provider['type'])) ?></a>
            <span aria-current="page"><?= e($provider['name']) ?></span>
        </nav>
        <span class="detail-kicker"><?= icon('map-pin', 'icon icon-sm') ?> <?= e(provider_type_label((string) $provider['type'])) ?></span>
        <h1><?= e($provider['name']) ?></h1>
        <p class="page-hero-lead"><?= e($provider['notes']) ?></p>
    </div>
</section>

<div class="container detail-layout">
    <div class="detail-main">
        <section class="detail-section">
            <h2>Contacto y ubicación</h2>
            <table class="spec-table">
                <tbody>
                <?php if ($provider['address'] !== ''): ?>
                    <tr><th scope="row">Dirección</th><td><?= e($provider['address']) ?><?= $provider['city'] !== '' ? ', ' . e($provider['city']) : '' ?></td></tr>
                <?php endif; ?>
                <?php if ($states !== []): ?>
                    <tr><th scope="row">Estados con cobertura</th><td><?= e(implode(' · ', array_map('state_name', $states))) ?></td></tr>
                <?php endif; ?>
                <?php if ($provider['phone'] !== ''): ?>
                    <tr><th scope="row">Teléfono</th><td><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', (string) $provider['phone'])) ?>"><?= e($provider['phone']) ?></a></td></tr>
                <?php endif; ?>
                <?php if ($provider['whatsapp'] !== ''): ?>
                    <tr><th scope="row">WhatsApp</th><td><?= e($provider['whatsapp']) ?></td></tr>
                <?php endif; ?>
                <?php if ($provider['email'] !== ''): ?>
                    <tr><th scope="row">Correo</th><td><a href="mailto:<?= e($provider['email']) ?>"><?= e($provider['email']) ?></a></td></tr>
                <?php endif; ?>
                <?php if ($provider['website'] !== ''): ?>
                    <tr><th scope="row">Sitio web</th><td><a href="<?= e($provider['website']) ?>" target="_blank" rel="nofollow noopener"><?= e($provider['website']) ?></a></td></tr>
                <?php endif; ?>
                <?php if ($provider['hours'] !== ''): ?>
                    <tr><th scope="row">Horario</th><td><?= e($provider['hours']) ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </section>

        <?php if ($services !== []): ?>
            <section class="detail-section">
                <h2>Servicios</h2>
                <ul class="check-list">
                    <?php foreach ($services as $service): ?>
                        <li><?= icon('check', 'icon icon-sm') ?> <span><?= e($service) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if ($brands !== []): ?>
            <section class="detail-section">
                <h2>Marcas que maneja</h2>
                <div class="tag-row">
                    <?php foreach ($brands as $brand): ?><span class="tag tag-sm"><?= e($brand) ?></span><?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <p class="calc-note">
            Ficha de directorio con fines informativos. AgroMáquina MX no garantiza existencias, precios ni calidad del servicio.
            Confirma por teléfono y solicita factura y garantía por escrito.
            <?php if ($provider['verified_on'] !== null): ?>Datos revisados el <?= e(format_date((string) $provider['verified_on'], 'd/m/Y')) ?>.<?php endif; ?>
        </p>
    </div>

    <aside class="detail-aside">
        <div class="aside-card">
            <h3>Contactar</h3>
            <?php if ($provider['phone'] !== ''): ?>
                <a class="btn btn-primary btn-block btn-sm" href="tel:<?= e(preg_replace('/[^0-9+]/', '', (string) $provider['phone'])) ?>"><?= icon('message-circle', 'icon icon-sm') ?> Llamar ahora</a>
            <?php endif; ?>
            <?php if ($provider['website'] !== ''): ?>
                <a class="btn btn-outline btn-block btn-sm" href="<?= e($provider['website']) ?>" target="_blank" rel="nofollow noopener">Sitio web <?= icon('external-link', 'icon icon-xs') ?></a>
            <?php endif; ?>
            <a class="btn btn-ghost btn-block btn-sm" href="<?= e(url('/proveedores')) ?>">Volver al directorio</a>
        </div>

        <div class="aside-card">
            <h3>Antes de ir</h3>
            <div class="aside-list">
                <a href="<?= e(url('/fallas')) ?>">Identifica la falla <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/codigos')) ?>">Lleva el código de falla <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/proveedores?type=refacciones')) ?>">Otras refaccionarias <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
            </div>
        </div>
    </aside>
</div>
