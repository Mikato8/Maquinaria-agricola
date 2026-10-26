<?php
/** @var array<string, mixed> $resource */
/** @var array<int, array<string, mixed>> $related */
$features = pg_array($resource['features'] ?? []);
?><section class="detail-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <a href="<?= e(url('/tecnologia')) ?>">Tecnología</a>
            <a href="<?= e(url('/tecnologia?kind=' . $resource['kind'])) ?>"><?= e(tech_kind_label((string) $resource['kind'])) ?></a>
            <span aria-current="page"><?= e($resource['name']) ?></span>
        </nav>

        <div class="detail-hero-grid">
            <div>
                <span class="detail-kicker"><?= icon(kind_icon((string) $resource['kind']), 'icon icon-sm') ?> <?= e(tech_kind_label((string) $resource['kind'])) ?> · <?= e(tech_category_label((string) $resource['category'])) ?></span>
                <h1><?= e($resource['name']) ?></h1>
                <p class="detail-lead"><?= e($resource['description']) ?></p>
                <div class="detail-actions">
                    <?php if ($resource['url'] !== ''): ?>
                        <a class="btn btn-primary" href="<?= e($resource['url']) ?>" target="_blank" rel="noopener nofollow"><?= icon('external-link', 'icon icon-sm') ?> Sitio oficial</a>
                    <?php endif; ?>
                    <?php if ($resource['docs_url'] !== ''): ?>
                        <a class="btn btn-outline" href="<?= e($resource['docs_url']) ?>" target="_blank" rel="noopener nofollow">Documentación</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="aside-card">
                <h3>Ficha</h3>
                <div class="aside-list">
                    <span>Proveedor o autor <strong><?= e($resource['vendor'] ?: 'Comunidad') ?></strong></span>
                    <span>Licencia <strong><?= e($resource['license'] ?: 'No declarada') ?></strong></span>
                    <span>Plataforma <strong><?= e($resource['platform'] ?: 'Web / móvil') ?></strong></span>
                    <?php if ((string) $resource['size_label'] !== ''): ?><span>Tamaño <strong><?= e($resource['size_label']) ?></strong></span><?php endif; ?>
                    <span>Precio <strong><?= e($resource['price_note'] ?: 'Consultar') ?></strong></span>
                    <?php if ($resource['verified_on'] !== null): ?><span>Verificado <strong><?= e(format_date((string) $resource['verified_on'], 'd/m/Y')) ?></strong></span><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container detail-layout">
    <div class="detail-main">
        <section class="detail-section">
            <h2>Viabilidad en México</h2>
            <div class="callout">
                <?= icon('map', 'icon icon-sm') ?>
                <p><?= e($resource['mexico_fit'] ?: 'Sin evaluación específica. Revisa requisitos de conectividad, idioma, soporte y costo antes de adoptarlo.') ?></p>
            </div>
        </section>

        <?php if ($features !== []): ?>
            <section class="detail-section">
                <h2>Qué incluye</h2>
                <ul class="check-list">
                    <?php foreach ($features as $feature): ?>
                        <li><?= icon('check', 'icon icon-sm') ?> <span><?= e($feature) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <section class="detail-section">
            <h2>Requisitos</h2>
            <p><?= nl2br(e($resource['requirements'] ?: 'Consulta la documentación oficial. En general se requiere equipo compatible, conectividad y capacitación del operador.')) ?></p>
        </section>

        <section class="detail-section">
            <h2>Cómo empezar</h2>
            <div class="step-list">
                <div class="step-item"><p>Define el problema que quieres resolver (guiado, dosis variable, registro de labores, detección de malezas) y la métrica con la que medirás el resultado.</p></div>
                <div class="step-item"><p>Verifica compatibilidad con tu tractor, pulverizadora o implemento: protocolo ISOBUS, voltaje, montaje y licencias.</p></div>
                <div class="step-item"><p>Haz una prueba en un lote pequeño y compara contra tu método actual: tiempo, insumos, calidad y costo.</p></div>
                <div class="step-item"><p>Capacita al operador y documenta el proceso. La tecnología sin capacitación se abandona en la primera temporada.</p></div>
            </div>
        </section>

        <p class="calc-note">AgroMáquina MX no vende ni distribuye estos productos. La inclusión es editorial y no implica recomendación comercial. Verifica precios, licencias y soporte directamente con el proveedor.</p>
    </div>

    <aside class="detail-aside">
        <div class="aside-card">
            <h3>Del mismo tipo</h3>
            <div class="aside-list">
                <?php foreach ($related as $item): ?>
                    <a href="<?= e(url('/tecnologia/' . $item['slug'])) ?>">
                        <span><?= e($item['name']) ?><br><small class="muted"><?= e($item['vendor'] ?: tech_category_label((string) $item['category'])) ?></small></span>
                        <?= icon('arrow-right', 'icon icon-xs') ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="aside-card">
            <h3>Guías relacionadas</h3>
            <div class="aside-list">
                <a href="<?= e(url('/guias/agricultura-de-precision-de-bajo-costo')) ?>">Precisión de bajo costo <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/guias/isobus-en-mexico-lo-que-debes-saber')) ?>">ISOBUS en México <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/guias/datos-y-modelos-de-ia-para-el-campo')) ?>">Datos y modelos de IA <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
            </div>
        </div>
    </aside>
</div>
