<?php
/** @var array<string, mixed> $input */
/** @var array<string, float> $result */
/** @var float $haHour */
/** @var float $costHa */
$components = [
    'Depreciación' => (float) $result['depreciation_hour'],
    'Interés del capital' => (float) $result['interest_hour'],
    'Seguro y resguardo' => (float) $result['insurance_hour'],
    'Mantenimiento y refacciones' => (float) $result['maintenance_hour'],
    'Diésel' => (float) $result['fuel_hour'],
    'Operador' => (float) $result['labor_hour'],
];
$total = (float) $result['total_hour'] ?: 1;
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Costos</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Números del taller</span>
        <h1>Cuánto cuesta de verdad una hora de tractor.</h1>
        <p class="page-hero-lead">
            La depreciación y el mantenimiento pesan más de lo que parece. Calcula el costo por hora y por hectárea
            con tu precio de compra, horas de uso, diésel y mano de obra.
        </p>
    </div>
</section>

<div class="container calc-layout">
    <form class="calc-form" method="get" action="<?= e(url('/costos')) ?>">
        <h2>Datos de la máquina</h2>
        <div class="field-grid field-grid-2">
            <div class="field">
                <label for="price_mxn">Valor de compra (MXN)</label>
                <input type="number" id="price_mxn" name="price_mxn" min="0" step="10000" value="<?= (int) $input['price_mxn'] ?>">
            </div>
            <div class="field">
                <label for="hours_year">Horas de uso al año</label>
                <input type="number" id="hours_year" name="hours_year" min="1" max="6000" step="50" value="<?= (int) $input['hours_year'] ?>">
            </div>
            <div class="field">
                <label for="years_life">Vida útil (años)</label>
                <input type="number" id="years_life" name="years_life" min="1" max="30" step="1" value="<?= (int) $input['years_life'] ?>">
            </div>
            <div class="field">
                <label for="salvage_pct">Valor de rescate (%)</label>
                <input type="number" id="salvage_pct" name="salvage_pct" min="0" max="60" step="1" value="<?= (int) $input['salvage_pct'] ?>">
            </div>
            <div class="field">
                <label for="fuel_l_h">Consumo de diésel (L/h)</label>
                <input type="number" id="fuel_l_h" name="fuel_l_h" min="0" step="0.5" value="<?= e((string) $input['fuel_l_h']) ?>">
            </div>
            <div class="field">
                <label for="diesel_price">Precio del diésel ($/L)</label>
                <input type="number" id="diesel_price" name="diesel_price" min="0" step="0.5" value="<?= e((string) $input['diesel_price']) ?>">
            </div>
            <div class="field">
                <label for="maintenance_pct">Mantenimiento (% del valor/año)</label>
                <input type="number" id="maintenance_pct" name="maintenance_pct" min="0" max="30" step="0.5" value="<?= e((string) $input['maintenance_pct']) ?>">
            </div>
            <div class="field">
                <label for="labor_hour">Operador por hora (MXN)</label>
                <input type="number" id="labor_hour" name="labor_hour" min="0" step="10" value="<?= (int) $input['labor_hour'] ?>">
            </div>
            <div class="field">
                <label for="interest_pct">Tasa de interés anual (%)</label>
                <input type="number" id="interest_pct" name="interest_pct" min="0" max="40" step="0.5" value="<?= e((string) $input['interest_pct']) ?>">
            </div>
            <div class="field">
                <label for="insurance_pct">Seguro (% del valor/año)</label>
                <input type="number" id="insurance_pct" name="insurance_pct" min="0" max="10" step="0.1" value="<?= e((string) $input['insurance_pct']) ?>">
            </div>
            <div class="field">
                <label for="utilization_pct">Aprovechamiento del operador (%)</label>
                <input type="number" id="utilization_pct" name="utilization_pct" min="1" max="100" step="1" value="<?= e((string) $input['utilization_pct']) ?>">
            </div>
        </div>

        <h2>Rendimiento en campo</h2>
        <div class="field-grid field-grid-2">
            <div class="field">
                <label for="width_m">Ancho de trabajo (m)</label>
                <input type="number" id="width_m" name="width_m" min="0" max="12" step="0.1" value="<?= e((string) $input['width_m']) ?>">
            </div>
            <div class="field">
                <label for="speed_kmh">Velocidad (km/h)</label>
                <input type="number" id="speed_kmh" name="speed_kmh" min="0" max="14" step="0.5" value="<?= e((string) $input['speed_kmh']) ?>">
            </div>
        </div>

        <button class="btn btn-primary btn-block" type="submit"><?= icon('calculator', 'icon icon-sm') ?> Calcular costo</button>
    </form>

    <div class="calc-results">
        <div class="metrics-grid">
            <div class="metric"><span>Costo por hora</span><strong><?= e(compact_money($result['total_hour'])) ?></strong><small>con operador</small></div>
            <div class="metric"><span>Costo por hectárea</span><strong><?= e(compact_money($costHa)) ?></strong><small><?= e(num($haHour, 2)) ?> ha/h</small></div>
            <div class="metric"><span>Costo anual</span><strong><?= e(compact_money($result['total_hour'] * $input['hours_year'])) ?></strong><small><?= (int) $input['hours_year'] ?> h/año</small></div>
            <div class="metric"><span>Costo fijo por hora</span><strong><?= e(compact_money($result['depreciation_hour'] + $result['interest_hour'] + $result['insurance_hour'])) ?></strong><small>aunque no trabajes</small></div>
        </div>

        <div class="aside-card">
            <h3>Desglose por hora</h3>
            <ul class="cost-bars">
                <?php foreach ($components as $label => $value): ?>
                    <li>
                        <span class="cost-bar-label"><?= e($label) ?></span>
                        <span class="cost-bar-track"><span class="cost-bar-fill" style="width: <?= (int) round($value / $total * 100) ?>%"></span></span>
                        <span class="cost-bar-value"><?= e(compact_money($value)) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="field-help">Los costos fijos (depreciación, interés y seguro) se reparten entre las horas anuales de uso: trabajar más horas baja el costo fijo por hora.</p>
        </div>

        <div class="verdict is-ok">
            <span class="verdict-icon"><?= icon('gauge', 'icon icon-lg') ?></span>
            <div>
                <h2><?= e(compact_money($costHa)) ?> por hectárea</h2>
                <p>Este es el costo de máquina, diésel y operador. Súmale insumos, semilla, fletes y financiamiento para el costo total del cultivo.</p>
            </div>
        </div>

        <div class="aside-card">
            <h3>Qué incluye y qué no</h3>
            <ul class="check-list">
                <li><?= icon('check', 'icon icon-sm') ?> <span>Depreciación lineal con valor de rescate.</span></li>
                <li><?= icon('check', 'icon icon-sm') ?> <span>Interés del capital invertido y seguro anual.</span></li>
                <li><?= icon('check', 'icon icon-sm') ?> <span>Mantenimiento preventivo y correctivo como porcentaje del valor.</span></li>
                <li><?= icon('check', 'icon icon-sm') ?> <span>Diésel y operador (ajustado por aprovechamiento).</span></li>
                <li><?= icon('alert-triangle', 'icon icon-sm') ?> <span>No incluye insumos, semilla, fletes, administración ni IVA.</span></li>
            </ul>
        </div>
    </div>
</div>
