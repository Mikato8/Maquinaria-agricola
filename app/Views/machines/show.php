<?php
/** @var array<string, mixed> $machine */
/** @var array<int, array<string, mixed>> $related */
/** @var array<int, array<string, mixed>> $implements */
/** @var array<int, array<string, mixed>> $faults */
$features = pg_array($machine['features'] ?? []);
$uses = pg_array($machine['common_uses'] ?? []);
$specs = [
    'Motor' => [
        'Potencia nominal' => hp_label($machine['power_hp']),
        'Potencia a la TDF' => hp_label($machine['pto_hp']),
        'Motor' => $machine['engine'] ?: '—',
        'Cilindrada' => $machine['displacement_l'] !== null ? num($machine['displacement_l'], 2) . ' L' : '—',
        'Cilindros' => $machine['cylinders'] !== null ? (string) $machine['cylinders'] : '—',
        'Aspiración' => $machine['aspiration'] !== '' ? ucfirst(str_replace('_', ' ', (string) $machine['aspiration'])) : '—',
    ],
    'Transmisión y tracción' => [
        'Transmisión' => $machine['transmission'] ?: '—',
        'Velocidades' => $machine['gears'] !== null ? (string) $machine['gears'] : '—',
        'Tracción' => $machine['traction'],
        'TDF' => ((int) ($machine['pto_rpm'] ?? 540)) . ' rpm',
        'Radio de giro' => $machine['turning_radius_m'] !== null ? num($machine['turning_radius_m'], 2) . ' m' : '—',
    ],
    'Hidráulico y pesos' => [
        'Caudal hidráulico' => $machine['hydraulic_flow_lpm'] !== null ? num($machine['hydraulic_flow_lpm'], 1) . ' L/min' : '—',
        'Capacidad de levante' => $machine['lift_capacity_kg'] !== null ? num($machine['lift_capacity_kg']) . ' kg' : '—',
        'Peso de operación' => $machine['weight_kg'] !== null ? num($machine['weight_kg']) . ' kg' : '—',
        'Tanque de combustible' => $machine['fuel_tank_l'] !== null ? num($machine['fuel_tank_l'], 1) . ' L' : '—',
    ],
];
$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $machine['brand_name'] . ' ' . $machine['model'],
    'brand' => ['@type' => 'Brand', 'name' => $machine['brand_name']],
    'category' => machine_category_label((string) $machine['category']),
    'description' => excerpt($machine['summary'] ?: $machine['description'], 300),
    'url' => url('/maquinas/' . $machine['slug']),
];
if ($machine['price_min_mxn'] !== null) {
    $jsonLd['offers'] = [
        '@type' => 'AggregateOffer',
        'priceCurrency' => 'MXN',
        'lowPrice' => (float) $machine['price_min_mxn'],
        'highPrice' => (float) ($machine['price_max_mxn'] ?? $machine['price_min_mxn']),
        'availability' => 'https://schema.org/InStock',
    ];
}
?><script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>

<section class="detail-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <a href="<?= e(url('/maquinas')) ?>">Máquinas</a>
            <a href="<?= e(url('/maquinas?category=' . $machine['category'])) ?>"><?= e(machine_category_label((string) $machine['category'])) ?></a>
            <span aria-current="page"><?= e($machine['brand_name']) ?> <?= e($machine['model']) ?></span>
        </nav>

        <div class="detail-hero-grid">
            <div>
                <span class="detail-kicker"><?= icon('tractor', 'icon icon-sm') ?> <?= e(machine_category_label((string) $machine['category'])) ?> · <?= e(years_label($machine['year_from'] !== null ? (int) $machine['year_from'] : null, $machine['year_to'] !== null ? (int) $machine['year_to'] : null)) ?></span>
                <h1><?= e($machine['brand_name']) ?> <?= e($machine['model']) ?></h1>
                <p class="detail-lead"><?= e($machine['summary'] ?: excerpt($machine['description'], 240)) ?></p>

                <dl class="detail-facts">
                    <div class="detail-fact"><dt>Potencia</dt><dd><?= e(hp_label($machine['power_hp'])) ?></dd></div>
                    <div class="detail-fact"><dt>Tracción</dt><dd><?= e($machine['traction']) ?></dd></div>
                    <div class="detail-fact"><dt>Levante</dt><dd><?= $machine['lift_capacity_kg'] !== null ? e(num($machine['lift_capacity_kg'])) . ' kg' : 'No declarado' ?></dd></div>
                    <div class="detail-fact"><dt>Caudal hidráulico</dt><dd><?= $machine['hydraulic_flow_lpm'] !== null ? e(num($machine['hydraulic_flow_lpm'], 1)) . ' L/min' : 'No declarado' ?></dd></div>
                    <div class="detail-fact"><dt>Transmisión</dt><dd><?= e($machine['transmission'] ?: 'No declarada') ?></dd></div>
                    <div class="detail-fact"><dt>Precio de referencia</dt><dd><?= e(price_range($machine['price_min_mxn'], $machine['price_max_mxn'])) ?></dd></div>
                </dl>

                <div class="detail-actions">
                    <a class="btn btn-primary" href="<?= e(url('/maquinas/' . $machine['slug'] . '/pdf')) ?>" target="_blank" rel="noopener"><?= icon('download', 'icon icon-sm') ?> Ficha en PDF</a>
                    <a class="btn btn-outline" href="<?= e(url('/compatibilidad?tractor_id=' . $machine['id'])) ?>"><?= icon('calculator', 'icon icon-sm') ?> Calcular acople</a>
                    <a class="btn btn-ghost" href="<?= e(url('/maquinas/comparar?ids[]=' . $machine['id'])) ?>"><?= icon('layers', 'icon icon-sm') ?> Comparar</a>
                </div>
            </div>

            <div class="detail-photo" aria-hidden="true">
                <span class="machine-emoji"><?= e(machine_category_emoji((string) $machine['category'])) ?></span>
                <span class="detail-photo-note"><?= e($machine['brand_name']) ?> · imagen ilustrativa del tipo de máquina</span>
            </div>
        </div>
    </div>
</section>

<div class="container detail-layout">
    <div class="detail-main">
        <?php if ((string) $machine['description'] !== ''): ?>
            <section class="detail-section">
                <h2>Para qué sirve y en qué se distingue</h2>
                <div class="prose">
                    <?php foreach (preg_split("/\n{2,}/", (string) $machine['description']) ?: [] as $paragraph): ?>
                        <p><?= nl2br(e(trim($paragraph))) ?></p>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="detail-section">
            <h2>Especificaciones técnicas</h2>
            <?php foreach ($specs as $group => $rows): ?>
                <table class="spec-table">
                    <caption><?= e($group) ?></caption>
                    <tbody>
                    <?php foreach ($rows as $label => $value): ?>
                        <?php if ($value === '—' || $value === '' || $value === null) { continue; } ?>
                        <tr>
                            <th scope="row"><?= e($label) ?></th>
                            <td><?= e($value) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
            <p class="calc-note">
                Especificaciones de referencia recopiladas de fichas de distribuidor y manuales públicos. La configuración cambia por año, mercado y opciones.
                Confirma con el concesionario antes de comprar y usa el manual de operación para límites de carga y lastre.
            </p>
        </section>

        <?php if ($features !== []): ?>
            <section class="detail-section">
                <h2>Equipamiento y características</h2>
                <ul class="check-list">
                    <?php foreach ($features as $feature): ?>
                        <li><?= icon('check', 'icon icon-sm') ?> <span><?= e($feature) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if ($uses !== []): ?>
            <section class="detail-section">
                <h2>Usos típicos en México</h2>
                <div class="tag-row">
                    <?php foreach ($uses as $use): ?>
                        <span class="tag tag-sm"><?= e($use) ?></span>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($implements !== []): ?>
            <section class="detail-section">
                <h2>Implementos que puede mover</h2>
                <p>Selección aproximada para la potencia declarada. El acople real depende del suelo, la profundidad y el peso del implemento: <a href="<?= e(url('/compatibilidad?tractor_id=' . $machine['id'])) ?>">calcula la compatibilidad exacta</a>.</p>
                <div class="mini-implement-grid">
                    <?php foreach ($implements as $implement): ?>
                        <a class="mini-implement" href="<?= e(url('/implementos/' . $implement['slug'])) ?>">
                            <span class="mini-implement-emoji"><?= e(implement_category_emoji((string) $implement['category'])) ?></span>
                            <span>
                                <strong><?= e($implement['name']) ?></strong>
                                <small><?= e(implement_category_label((string) $implement['category'])) ?> · <?= e(power_range($implement['power_min_hp'], $implement['power_max_hp'])) ?></small>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($faults !== []): ?>
            <section class="detail-section">
                <h2>Fallas frecuentes en este tipo de máquina</h2>
                <div class="fault-list">
                    <?php foreach ($faults as $fault): ?>
                        <?php \App\Core\View::partial('partials/fault-card', ['fault' => $fault]); ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ((string) ($machine['maintenance_tip'] ?? '') !== ''): ?>
            <section class="detail-section">
                <h2>Nota de mantenimiento</h2>
                <div class="callout">
                    <?= icon('clipboard', 'icon icon-sm') ?>
                    <p><?= e($machine['maintenance_tip']) ?></p>
                </div>
                <a class="text-link" href="<?= e(url('/mantenimiento?categoria=' . $machine['category'])) ?>">Generar plan de mantenimiento por horas <?= icon('arrow-right', 'icon icon-sm') ?></a>
            </section>
        <?php endif; ?>
    </div>

    <aside class="detail-aside">
        <div class="aside-card">
            <h3>Precio de referencia</h3>
            <p class="aside-price"><?= e(price_range($machine['price_min_mxn'], $machine['price_max_mxn'])) ?></p>
            <p><?= e($machine['price_note'] ?: 'Precio nuevo estimado en México. Varía por región, tipo de cambio, equipamiento y disponibilidad.') ?></p>
            <p class="muted">Disponibilidad: <?= e($machine['availability']) ?></p>
        </div>

        <div class="aside-card">
            <h3>Marca</h3>
            <p><strong><?= e($machine['brand_name']) ?></strong><?= $machine['brand_country'] ? ' · ' . e($machine['brand_country']) : '' ?></p>
            <?php if (!empty($machine['brand_notes'])): ?>
                <p><?= e($machine['brand_notes']) ?></p>
            <?php endif; ?>
            <p><?= !empty($machine['brand_in_mexico']) ? 'Con presencia o red de distribuidores en México.' : 'Presencia limitada: verifica refacciones y servicio local.' ?></p>
            <?php if (!empty($machine['brand_website'])): ?>
                <a class="text-link" href="<?= e($machine['brand_website']) ?>" rel="nofollow noopener" target="_blank">Sitio del fabricante <?= icon('external-link', 'icon icon-xs') ?></a>
            <?php endif; ?>
        </div>

        <div class="aside-card">
            <h3>Antes de comprar</h3>
            <div class="aside-list">
                <a href="<?= e(url('/guias/como-elegir-tractor-segun-superficie-y-cultivo')) ?>">Cómo elegir tractor <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/costos')) ?>">Costo por hora <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/proveedores?type=distribuidor')) ?>">Distribuidores <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/proveedores?type=refacciones')) ?>">Refacciones <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
            </div>
        </div>

        <?php if ($related !== []): ?>
            <div class="aside-card">
                <h3>Máquinas parecidas</h3>
                <div class="aside-list">
                    <?php foreach ($related as $item): ?>
                        <a href="<?= e(url('/maquinas/' . $item['slug'])) ?>">
                            <span><?= e($item['brand_name']) ?> <?= e($item['model']) ?><br><small class="muted"><?= e(hp_label($item['power_hp'])) ?> · <?= e($item['traction']) ?></small></span>
                            <?= icon('arrow-right', 'icon icon-xs') ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </aside>
</div>
