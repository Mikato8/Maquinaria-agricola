<?php
/** @var array<string, mixed> $implement */
/** @var array<int, array<string, mixed>> $tractors */
/** @var array<int, array<string, mixed>> $related */
/** @var array<string, mixed>|null $example */
$soilTypes = pg_array($implement['soil_types'] ?? []);
$crops = pg_array($implement['suitable_crops'] ?? []);
?><section class="detail-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <a href="<?= e(url('/implementos')) ?>">Implementos</a>
            <a href="<?= e(url('/implementos?category=' . $implement['category'])) ?>"><?= e(implement_category_label((string) $implement['category'])) ?></a>
            <span aria-current="page"><?= e($implement['name']) ?></span>
        </nav>

        <div class="detail-hero-grid">
            <div>
                <span class="detail-kicker"><?= icon('tool', 'icon icon-sm') ?> <?= e(implement_category_label((string) $implement['category'])) ?> · <?= e($implement['hitch']) ?></span>
                <h1><?= e($implement['name']) ?></h1>
                <p class="detail-lead"><?= e($implement['summary'] ?: excerpt($implement['description'], 240)) ?></p>

                <dl class="detail-facts">
                    <div class="detail-fact"><dt>Potencia requerida</dt><dd><?= e(power_range($implement['power_min_hp'], $implement['power_max_hp'])) ?></dd></div>
                    <div class="detail-fact"><dt>Ancho de trabajo</dt><dd><?= $implement['working_width_m'] !== null ? e(num($implement['working_width_m'], 2)) . ' m' : 'No aplica' ?></dd></div>
                    <div class="detail-fact"><dt>Profundidad</dt><dd><?= $implement['working_depth_cm'] !== null && (float) $implement['working_depth_cm'] > 0 ? e(num($implement['working_depth_cm'], 0)) . ' cm' : 'Superficial' ?></dd></div>
                    <div class="detail-fact"><dt>Peso</dt><dd><?= $implement['weight_kg'] !== null ? e(num($implement['weight_kg'])) . ' kg' : 'No declarado' ?></dd></div>
                    <div class="detail-fact"><dt>Capacidad</dt><dd><?= e($implement['capacity'] ?: '—') ?></dd></div>
                    <div class="detail-fact"><dt>Precio de referencia</dt><dd><?= e(price_range($implement['price_min_mxn'], $implement['price_max_mxn'])) ?></dd></div>
                </dl>

                <div class="detail-actions">
                    <a class="btn btn-primary" href="<?= e(url('/compatibilidad?implemento_id=' . $implement['id'])) ?>"><?= icon('calculator', 'icon icon-sm') ?> Calcular con mi tractor</a>
                    <a class="btn btn-outline" href="<?= e(url('/implementos')) ?>"><?= icon('layers', 'icon icon-sm') ?> Otros implementos</a>
                </div>
            </div>

            <div class="detail-photo" aria-hidden="true">
                <span class="machine-emoji"><?= e(implement_category_emoji((string) $implement['category'])) ?></span>
                <span class="detail-photo-note"><?= e(implement_category_label((string) $implement['category'])) ?> · imagen ilustrativa</span>
            </div>
        </div>
    </div>
</section>

<div class="container detail-layout">
    <div class="detail-main">
        <section class="detail-section">
            <h2>Qué hace y cómo se usa</h2>
            <div class="prose">
                <p><?= nl2br(e((string) $implement['description'])) ?></p>
                <?php if ((string) $implement['operation'] !== ''): ?>
                    <h3>Operación</h3>
                    <p><?= nl2br(e((string) $implement['operation'])) ?></p>
                <?php endif; ?>
            </div>
        </section>

        <section class="detail-section">
            <h2>Calibración y ajuste</h2>
            <div class="callout">
                <?= icon('gauge', 'icon icon-sm') ?>
                <p><?= e($implement['calibration_tip'] ?: 'Sigue el manual del fabricante: presión de llantas, nivelación, profundidad y velocidad cambian el resultado y el consumo.') ?></p>
            </div>
        </section>

        <section class="detail-section">
            <h2>Seguridad antes de enganchar</h2>
            <div class="callout callout-danger">
                <?= icon('hard-hat', 'icon icon-sm') ?>
                <p><?= e($implement['safety'] ?: 'Detén el motor, baja el implemento al suelo, bloquea el hidráulico y usa calzos antes de meterte debajo. Nunca permitas pasajeros en el implemento.') ?></p>
            </div>
        </section>

        <?php if ((string) $implement['maintenance'] !== ''): ?>
            <section class="detail-section">
                <h2>Mantenimiento</h2>
                <p><?= nl2br(e((string) $implement['maintenance'])) ?></p>
            </section>
        <?php endif; ?>

        <?php if ($example !== null): ?>
            <section class="detail-section">
                <h2>Ejemplo de acople</h2>
                <p>Con un <a href="<?= e(url('/maquinas/' . $example['tractor']['slug'])) ?>"><?= e($example['tractor']['brand_name']) ?> <?= e($example['tractor']['model']) ?></a> (<?= e(hp_label($example['tractor']['power_hp'])) ?>) en suelo franco y humedad óptima:</p>
                <div class="metrics-grid">
                    <div class="metric"><span>Potencia requerida</span><strong><?= e(num($example['result']['power_hp'], 1)) ?> HP</strong><small><?= e(num($example['result']['load_pct'])) ?> % de carga</small></div>
                    <div class="metric"><span>Fuerza de tiro</span><strong><?= e(num($example['result']['draft_kgf'])) ?> kgf</strong><small>a la profundidad de ficha</small></div>
                    <div class="metric"><span>Rendimiento</span><strong><?= e(num($example['result']['field_capacity_ha_h'], 2)) ?> ha/h</strong><small><?= e(num($example['result']['hours_per_ha'], 1)) ?> h por ha</small></div>
                    <div class="metric"><span>Diésel</span><strong><?= e(num($example['result']['fuel_l_ha'], 1)) ?> L/ha</strong><small>a 27 $/L</small></div>
                </div>
                <p class="calc-note">Estimación orientativa. El resultado real depende del estado del suelo, humedad, lastre, llantas y habilidad del operador.</p>
            </section>
        <?php endif; ?>

        <?php if ($soilTypes !== [] || $crops !== []): ?>
            <section class="detail-section">
                <h2>Dónde funciona mejor</h2>
                <?php if ($soilTypes !== []): ?>
                    <h3>Suelos</h3>
                    <div class="tag-row">
                        <?php foreach ($soilTypes as $soil): ?><span class="tag tag-sm"><?= e($soil) ?></span><?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ($crops !== []): ?>
                    <h3>Cultivos</h3>
                    <div class="tag-row">
                        <?php foreach ($crops as $crop): ?><span class="tag tag-sm"><?= e($crop) ?></span><?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if ($tractors !== []): ?>
            <section class="detail-section">
                <h2>Tractores compatibles</h2>
                <p>Modelos del catálogo cuya potencia declarada entra en el rango recomendado.</p>
                <div class="mini-tractor-grid">
                    <?php foreach ($tractors as $tractor): ?>
                        <a class="mini-tractor" href="<?= e(url('/maquinas/' . $tractor['slug'])) ?>">
                            <span class="mini-tractor-emoji"><?= e(machine_category_emoji((string) $tractor['category'])) ?></span>
                            <span>
                                <strong><?= e($tractor['brand_name']) ?> <?= e($tractor['model']) ?></strong>
                                <small><?= e(hp_label($tractor['power_hp'])) ?> · levante <?= $tractor['lift_capacity_kg'] !== null ? e(num($tractor['lift_capacity_kg'])) . ' kg' : 's/d' ?></small>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <aside class="detail-aside">
        <div class="aside-card">
            <h3>Precio de referencia</h3>
            <p class="aside-price"><?= e(price_range($implement['price_min_mxn'], $implement['price_max_mxn'])) ?></p>
            <p>Precio nuevo estimado en México. Varía por importador, ancho, opciones y tipo de cambio.</p>
        </div>

        <div class="aside-card">
            <h3>Requisitos del tractor</h3>
            <div class="aside-list">
                <a href="<?= e(url('/maquinas?min_hp=' . (int) $implement['power_min_hp'])) ?>">Potencia mínima <span><?= e(hp_label($implement['power_min_hp'])) ?></span></a>
                <?php if ($implement['min_lift_kg'] !== null): ?>
                    <a href="<?= e(url('/compatibilidad?implemento_id=' . $implement['id'])) ?>">Levante mínimo <span><?= e(num($implement['min_lift_kg'])) ?> kg</span></a>
                <?php endif; ?>
                <?php if ($implement['required_flow_lpm'] !== null): ?>
                    <a href="<?= e(url('/compatibilidad?implemento_id=' . $implement['id'])) ?>">Caudal requerido <span><?= e(num($implement['required_flow_lpm'], 1)) ?> L/min</span></a>
                <?php endif; ?>
                <a href="<?= e(url('/compatibilidad?implemento_id=' . $implement['id'])) ?>">Tracción <span><?= e($implement['drive_required'] === 'cualquiera' ? 'Cualquiera' : $implement['drive_required']) ?></span></a>
            </div>
        </div>

        <?php if ($related !== []): ?>
            <div class="aside-card">
                <h3>Otros <?= e(mb_strtolower(implement_category_label((string) $implement['category']))) ?></h3>
                <div class="aside-list">
                    <?php foreach ($related as $item): ?>
                        <a href="<?= e(url('/implementos/' . $item['slug'])) ?>">
                            <span><?= e($item['name']) ?><br><small class="muted"><?= e(power_range($item['power_min_hp'], $item['power_max_hp'])) ?></small></span>
                            <?= icon('arrow-right', 'icon icon-xs') ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="aside-card">
            <h3>Antes de comprar</h3>
            <div class="aside-list">
                <a href="<?= e(url('/guias/implementos-por-tipo-de-labranza')) ?>">Labranza: cuándo usar cada uno <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/costos')) ?>">Costo por hectárea <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/proveedores')) ?>">Proveedores <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
            </div>
        </div>
    </aside>
</div>
