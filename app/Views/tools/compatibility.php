<?php
/** @var array<int, array<string, mixed>> $tractors */
/** @var array<int, array<string, mixed>> $implementsList */
/** @var array<string, mixed>|null $tractor */
/** @var array<string, mixed>|null $implement */
/** @var array<string, mixed> $input */
/** @var array<string, mixed> $implementInput */
/** @var array<string, mixed> $options */
/** @var array<string, mixed>|null $result */
$verdictTone = match ($result['verdict'] ?? '') {
    'insuficiente' => 'is-danger',
    'al_limite' => 'is-warning',
    'sobredimensionado' => 'is-warning',
    'adecuado' => 'is-ok',
    default => '',
};
$verdictIcon = match ($result['verdict'] ?? '') {
    'insuficiente' => 'alert-triangle',
    'al_limite' => 'gauge',
    'sobredimensionado' => 'layers',
    'adecuado' => 'check-circle',
    default => 'calculator',
};
$verdictTitle = match ($result['verdict'] ?? '') {
    'insuficiente' => 'El tractor no alcanza para este implemento',
    'al_limite' => 'Acople viable, pero al límite',
    'sobredimensionado' => 'El tractor sobra para este implemento',
    'adecuado' => 'Acople adecuado',
    default => 'Calculadora de compatibilidad',
};
$load = (int) ($result['load_pct'] ?? 0);
$loadPosition = max(0, min(140, $load));
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Compatibilidad</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Herramienta de ingeniería</span>
        <h1>¿Tu tractor puede con ese implemento?</h1>
        <p class="page-hero-lead">
            Estima fuerza de tiro, potencia necesaria, carga del motor, levante hidráulico, caudal, lastre,
            rendimiento y consumo de diésel con las condiciones reales de tu lote.
        </p>
    </div>
</section>

<div class="container calc-layout">
    <form class="calc-form" method="get" action="<?= e(url('/compatibilidad')) ?>">
        <h2>1. Tu tractor</h2>
        <div class="field">
            <label for="tractor_id">Elegir del catálogo (opcional)</label>
            <select id="tractor_id" name="tractor_id" data-tractor-select>
                <option value="">Capturar datos manualmente</option>
                <?php foreach ($tractors as $item): ?>
                    <option value="<?= (int) $item['id'] ?>"
                            data-hp="<?= e((string) $item['power_hp']) ?>"
                            data-lift="<?= e((string) ($item['lift_capacity_kg'] ?? '')) ?>"
                            data-flow="<?= e((string) ($item['hydraulic_flow_lpm'] ?? '')) ?>"
                            data-weight="<?= e((string) ($item['weight_kg'] ?? '')) ?>"
                            data-traction="<?= e((string) $item['traction']) ?>"
                        <?= $tractor !== null && (int) $tractor['id'] === (int) $item['id'] ? 'selected' : '' ?>>
                        <?= e($item['brand_name'] . ' ' . $item['model']) ?> · <?= e(hp_label($item['power_hp'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="field-help">Al elegir una ficha se llenan potencia, peso, levante y caudal; puedes ajustarlos.</span>
        </div>
        <div class="field-grid field-grid-2">
            <div class="field">
                <label for="tractor_power_hp">Potencia nominal (HP)</label>
                <input type="number" id="tractor_power_hp" name="tractor_power_hp" min="5" max="700" step="1" value="<?= (int) $input['power_hp'] ?: '' ?>" required>
            </div>
            <div class="field">
                <label for="tractor_traction">Tracción</label>
                <select id="tractor_traction" name="tractor_traction">
                    <?php foreach (['4WD' => '4WD', '2WD' => '2WD', 'oruga' => 'Orugas'] as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $input['traction'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="tractor_weight_kg">Peso del tractor (kg)</label>
                <input type="number" id="tractor_weight_kg" name="tractor_weight_kg" min="0" step="50" value="<?= (int) $input['weight_kg'] ?: '' ?>" placeholder="3200">
            </div>
            <div class="field">
                <label for="tractor_lift_kg">Capacidad de levante (kg)</label>
                <input type="number" id="tractor_lift_kg" name="tractor_lift_kg" min="0" step="50" value="<?= (int) $input['lift_capacity_kg'] ?: '' ?>" placeholder="1800">
            </div>
            <div class="field">
                <label for="tractor_flow_lpm">Caudal hidráulico (L/min)</label>
                <input type="number" id="tractor_flow_lpm" name="tractor_flow_lpm" min="0" step="1" value="<?= (int) $input['hydraulic_flow_lpm'] ?: '' ?>" placeholder="45">
            </div>
        </div>

        <h2>2. El implemento</h2>
        <div class="field">
            <label for="implemento_id">Elegir del catálogo (opcional)</label>
            <select id="implemento_id" name="implemento_id">
                <option value="">Capturar datos manualmente</option>
                <?php foreach ($implementsList as $item): ?>
                    <option value="<?= (int) $item['id'] ?>" <?= $implement !== null && (int) $implement['id'] === (int) $item['id'] ? 'selected' : '' ?>>
                        <?= e($item['name']) ?> · <?= e(power_range($item['power_min_hp'], $item['power_max_hp'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field-grid field-grid-2">
            <div class="field">
                <label for="implement_category">Tipo de labor</label>
                <select id="implement_category" name="implement_category">
                    <?php foreach (category_config('implement_categories') as $code => $category): ?>
                        <option value="<?= e($code) ?>" <?= $implementInput['category'] === $code ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="implement_width_m">Ancho de trabajo (m)</label>
                <input type="number" id="implement_width_m" name="implement_width_m" min="0.2" max="12" step="0.05" value="<?= $implementInput['working_width_m'] ?: '' ?>" placeholder="2.5" required>
            </div>
            <div class="field">
                <label for="implement_depth_cm">Profundidad (cm)</label>
                <input type="number" id="implement_depth_cm" name="implement_depth_cm" min="0" max="90" step="1" value="<?= $implementInput['working_depth_cm'] ?: '' ?>" placeholder="25">
            </div>
            <div class="field">
                <label for="implement_weight_kg">Peso del implemento (kg)</label>
                <input type="number" id="implement_weight_kg" name="implement_weight_kg" min="0" step="10" value="<?= $implementInput['weight_kg'] ?: '' ?>" placeholder="450">
            </div>
            <div class="field">
                <label for="implement_flow_lpm">Caudal requerido (L/min)</label>
                <input type="number" id="implement_flow_lpm" name="implement_flow_lpm" min="0" step="1" value="<?= $implementInput['required_flow_lpm'] ?: '' ?>" placeholder="0">
            </div>
        </div>

        <h2>3. Condiciones del lote</h2>
        <div class="field-grid field-grid-2">
            <div class="field">
                <label for="soil">Suelo</label>
                <select id="soil" name="soil">
                    <?php foreach (\App\Services\MachineryMath::soilFactors() as $code => $factor): ?>
                        <option value="<?= e($code) ?>" <?= $options['soil'] === $code ? 'selected' : '' ?>><?= e(ucfirst($code)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="moisture">Humedad</label>
                <select id="moisture" name="moisture">
                    <option value="seco" <?= $options['moisture'] === 'seco' ? 'selected' : '' ?>>Seco</option>
                    <option value="optimo" <?= $options['moisture'] === 'optimo' ? 'selected' : '' ?>>Óptimo para labor</option>
                    <option value="humedo" <?= $options['moisture'] === 'humedo' ? 'selected' : '' ?>>Húmedo</option>
                </select>
            </div>
            <div class="field">
                <label for="slope_pct">Pendiente (%)</label>
                <input type="number" id="slope_pct" name="slope_pct" min="0" max="35" step="1" value="<?= (int) $options['slope_pct'] ?>">
            </div>
            <div class="field">
                <label for="speed_kmh">Velocidad de trabajo (km/h)</label>
                <input type="number" id="speed_kmh" name="speed_kmh" min="2" max="14" step="0.5" value="<?= e((string) $options['speed_kmh']) ?>">
            </div>
            <div class="field">
                <label for="diesel_price">Precio del diésel ($/L)</label>
                <input type="number" id="diesel_price" name="diesel_price" min="0" step="0.5" value="<?= e((string) $options['diesel_price']) ?>">
            </div>
        </div>

        <button class="btn btn-primary btn-block" type="submit"><?= icon('calculator', 'icon icon-sm') ?> Calcular compatibilidad</button>
        <p class="field-help">Los resultados se actualizan al enviar el formulario. Puedes compartir la URL con los parámetros.</p>
    </form>

    <div class="calc-results">
        <?php if ($result === null): ?>
            <div class="empty-state">
                <span class="empty-icon"><?= icon('tractor') ?></span>
                <h2>Elige tractor e implemento</h2>
                <p>Selecciona una ficha del catálogo o captura potencia, ancho y profundidad para ver el cálculo completo.</p>
                <p class="muted">Ejemplo: tractor de 75 HP, rastra de 2.4 m a 18 cm en suelo franco.</p>
            </div>
        <?php else: ?>
            <div class="verdict <?= e($verdictTone) ?>">
                <span class="verdict-icon"><?= icon($verdictIcon, 'icon icon-lg') ?></span>
                <div>
                    <h2><?= e($verdictTitle) ?></h2>
                    <p><?= e(num($result['power_hp'], 1)) ?> HP requeridos · <?= (int) $result['load_pct'] ?> % de la potencia nominal del tractor</p>
                </div>
            </div>

            <div class="load-gauge">
                <div class="load-gauge-head">
                    <span>Carga del motor</span>
                    <strong><?= (int) $result['load_pct'] ?> %</strong>
                </div>
                <div class="load-bar">
                    <span class="load-bar-fill" style="left: <?= (int) min(100, $loadPosition) ?>%"></span>
                </div>
                <div class="load-scale"><span>0 %</span><span>55 %</span><span>90 %</span><span>100 %</span></div>
                <p class="field-help">Zona verde: 55–90 %. Por debajo se desperdicia potencia; por arriba no hay reserva para pendientes ni suelo húmedo.</p>
            </div>

            <div class="metrics-grid">
                <div class="metric"><span>Fuerza de tiro</span><strong><?= e(num($result['draft_kgf'])) ?></strong><small>kgf en el implemento</small></div>
                <div class="metric"><span>Potencia en el motor</span><strong><?= e(num($result['power_hp'], 1)) ?></strong><small>HP (<?= e(num($result['power_kw'], 1)) ?> kW)</small></div>
                <div class="metric"><span>Rendimiento</span><strong><?= e(num($result['field_capacity_ha_h'], 2)) ?></strong><small>ha por hora</small></div>
                <div class="metric"><span>Tiempo por hectárea</span><strong><?= e(num($result['hours_per_ha'], 1)) ?></strong><small>horas</small></div>
                <div class="metric"><span>Diésel por hectárea</span><strong><?= e(num($result['fuel_l_ha'], 1)) ?></strong><small>L/ha</small></div>
                <div class="metric"><span>Costo de diésel</span><strong><?= e(compact_money($result['fuel_cost_ha'])) ?></strong><small>por hectárea</small></div>
            </div>

            <?php if ($result['warnings'] !== []): ?>
                <div class="calc-warnings">
                    <?php foreach ($result['warnings'] as $warning): ?>
                        <div class="calc-warning is-<?= e($warning['tone']) ?>">
                            <?= icon($warning['tone'] === 'danger' ? 'alert-triangle' : 'info', 'icon icon-sm') ?>
                            <div>
                                <strong><?= e($warning['title']) ?></strong>
                                <p><?= e($warning['text']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="aside-card">
                <h3>Recomendaciones de operación</h3>
                <ul class="check-list">
                    <?php foreach ($result['recommendations'] as $recommendation): ?>
                        <li><?= icon('check', 'icon icon-sm') ?> <span><?= e($recommendation) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <details class="calc-details">
                <summary>Ver supuestos y fórmula del cálculo</summary>
                <div class="calc-details-body">
                    <p>La fuerza de tiro se estima con la resistencia específica del suelo (<em>k</em>) por la sección transversal de trabajo:</p>
                    <p class="formula">F (kgf) = k × ancho (dm) × profundidad (dm) × factor de suelo × factor de humedad × factor de pendiente</p>
                    <p>La potencia se obtiene de <span class="formula-inline">P (kW) = F (N) × v (m/s) / (1000 × eficiencia de tracción)</span>, con eficiencia de <?= e(num((float) $result['traction_efficiency'] * 100)) ?> % para la tracción elegida.</p>
                    <ul>
                        <li><strong>k</strong> usada: <?= e(num((float) $result['factors']['k'], 1)) ?> kgf/dm² para <?= e(mb_strtolower(implement_category_label((string) $implementInput['category']))) ?>.</li>
                        <li>Factor de suelo: <?= e(num((float) $result['factors']['soil'], 2)) ?> · humedad: <?= e(num((float) $result['factors']['moisture'], 2)) ?> · pendiente: <?= e(num((float) $result['factors']['slope'], 2)) ?>.</li>
                        <li>Rendimiento con eficiencia de campo del 78 % (giros, abastecimiento y ajustes).</li>
                        <li>Consumo de diésel: 0.23 L por HP·hora a la carga estimada.</li>
                        <li>Lastre sugerido: <?= e(num($result['ballast_kg'])) ?> kg al frente para conservar al menos 20 % del peso total sobre el eje delantero.</li>
                    </ul>
                    <p class="calc-note"><?= e(\App\Services\MachineryMath::DISCLAIMER) ?></p>
                </div>
            </details>

            <div class="cta-actions">
                <a class="btn btn-primary" href="<?= e(url('/costos?price_mxn=' . (int) (($input['power_hp'] ?: 75) * 14000) . '&fuel_l_h=' . (int) num((float) $result['fuel_l_h']))) ?>">Calcular costo por hora <?= icon('arrow-right', 'icon icon-sm') ?></a>
                <a class="btn btn-outline" href="<?= e(url('/implementos')) ?>">Ver otros implementos</a>
            </div>
        <?php endif; ?>
    </div>
</div>
