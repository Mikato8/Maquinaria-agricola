<?php
/** @var array<string, mixed> $input */
/** @var array<int, array{step: array<string, mixed>, ha_hour: float, hours_total: float, cost_ha: float}> $sequence */
/** @var array<string, float> $tractorCost */
/** @var float $totalCostHa */
/** @var float $totalHours */
/** @var string $cropLabel */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Recomendador</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Selección de implementos</span>
        <h1>Qué implementos necesita tu parcela, en qué orden.</h1>
        <p class="page-hero-lead">
            Una secuencia realista para tu cultivo, superficie y potencia: labranza, siembra y protección,
            con ancho estimado, horas y costo por hectárea.
        </p>
    </div>
</section>

<div class="container calc-layout">
    <form class="calc-form" method="get" action="<?= e(url('/recomendador')) ?>">
        <h2>Datos de la parcela</h2>
        <div class="field-grid field-grid-2">
            <div class="field">
                <label for="area_ha">Superficie (ha)</label>
                <input type="number" id="area_ha" name="area_ha" min="0.5" max="5000" step="0.5" value="<?= e((string) $input['area_ha']) ?>">
            </div>
            <div class="field">
                <label for="hp">Potencia del tractor (HP)</label>
                <input type="number" id="hp" name="hp" min="15" max="600" step="5" value="<?= (int) $input['hp'] ?>">
            </div>
            <div class="field">
                <label for="crop">Cultivo</label>
                <select id="crop" name="crop">
                    <?php foreach (crop_options() as $code => $label): ?>
                        <option value="<?= e($code) ?>" <?= $input['crop'] === $code ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="soil">Suelo</label>
                <select id="soil" name="soil">
                    <?php foreach (\App\Services\MachineryMath::soilFactors() as $code => $factor): ?>
                        <option value="<?= e($code) ?>" <?= $input['soil'] === $code ? 'selected' : '' ?>><?= e(ucfirst($code)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="slope_pct">Pendiente (%)</label>
                <input type="number" id="slope_pct" name="slope_pct" min="0" max="35" step="1" value="<?= (int) $input['slope_pct'] ?>">
            </div>
            <div class="field">
                <label for="system">Sistema de labranza</label>
                <select id="system" name="system">
                    <option value="cero" <?= $input['system'] === 'cero' ? 'selected' : '' ?>>Siembra directa (cero labranza)</option>
                    <option value="minima" <?= $input['system'] === 'minima' ? 'selected' : '' ?>>Labranza mínima</option>
                    <option value="convencional" <?= $input['system'] === 'convencional' ? 'selected' : '' ?>>Labranza convencional</option>
                </select>
            </div>
            <div class="field">
                <label for="budget">Presupuesto para implementos (MXN)</label>
                <input type="number" id="budget" name="budget" min="0" step="10000" value="<?= (int) $input['budget'] ?: '' ?>" placeholder="Opcional">
            </div>
        </div>
        <button class="btn btn-primary btn-block" type="submit"><?= icon('sparkles', 'icon icon-sm') ?> Armar secuencia</button>
        <p class="field-help">El presupuesto se usa para señalar alternativas; no filtra la recomendación técnica.</p>
    </form>

    <div class="calc-results">
        <div class="verdict is-ok">
            <span class="verdict-icon"><?= icon('layers', 'icon icon-lg') ?></span>
            <div>
                <h2>Secuencia para <?= e(num($input['area_ha'], 1)) ?> ha de <?= e(mb_strtolower($cropLabel)) ?></h2>
                <p><?= count($sequence) ?> pasos · tractor de <?= (int) $input['hp'] ?> HP · suelo <?= e($input['soil']) ?> · <?= e($input['system'] === 'cero' ? 'siembra directa' : 'labranza ' . $input['system']) ?></p>
            </div>
        </div>

        <?php if ($sequence === []): ?>
            <div class="empty-state">
                <span class="empty-icon"><?= icon('tool') ?></span>
                <h2>No hay implementos para esa potencia</h2>
                <p>Amplía el catálogo o ajusta la potencia. También puedes revisar la compatibilidad manual.</p>
                <a class="btn btn-primary" href="<?= e(url('/compatibilidad')) ?>">Calcular compatibilidad</a>
            </div>
        <?php else: ?>
            <ol class="sequence-list">
                <?php foreach ($sequence as $index => $pass): ?>
                    <li class="sequence-item">
                        <div class="sequence-step">
                            <span class="step-number"><?= $index + 1 ?></span>
                            <div>
                                <h3><a href="<?= e(url('/implementos/' . $pass['step']['implement']['slug'])) ?>"><?= e($pass['step']['implement']['name']) ?></a></h3>
                                <p class="muted"><?= e($pass['step']['note']) ?></p>
                                <div class="sequence-meta">
                                    <span>Ancho <?= e(num($pass['step']['width_m'], 2)) ?> m</span>
                                    <span><?= e(num($pass['ha_hour'], 2)) ?> ha/h</span>
                                    <span><?= e(num($pass['hours_total'], 1)) ?> h en total</span>
                                    <span><?= e(compact_money($pass['cost_ha'])) ?>/ha</span>
                                </div>
                            </div>
                        </div>
                        <div class="sequence-price">
                            <?= e(price_range($pass['step']['implement']['price_min_mxn'], $pass['step']['implement']['price_max_mxn'])) ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>

            <div class="metrics-grid">
                <div class="metric"><span>Costo estimado</span><strong><?= e(compact_money($totalCostHa)) ?></strong><small>por hectárea (sin insumos)</small></div>
                <div class="metric"><span>Tiempo de máquina</span><strong><?= e(num($totalHours, 1)) ?> h</strong><small>en <?= e(num($input['area_ha'], 1)) ?> ha</small></div>
                <div class="metric"><span>Costo total</span><strong><?= e(compact_money($totalCostHa * $input['area_ha'])) ?></strong><small>del ciclo mecanizado</small></div>
                <div class="metric"><span>Costo por hora</span><strong><?= e(compact_money($tractorCost['total_hour'])) ?></strong><small>tractor + operador</small></div>
            </div>

            <div class="aside-card">
                <h3>Cómo se compone el costo por hora</h3>
                <ul class="cost-bars">
                    <?php
                    $components = [
                        'Depreciación' => (float) $tractorCost['depreciation_hour'],
                        'Interés' => (float) $tractorCost['interest_hour'],
                        'Seguro' => (float) $tractorCost['insurance_hour'],
                        'Mantenimiento' => (float) $tractorCost['maintenance_hour'],
                        'Diésel' => (float) $tractorCost['fuel_hour'],
                        'Operador' => (float) $tractorCost['labor_hour'],
                    ];
                    $maxComponent = max($components) ?: 1;
                    ?>
                    <?php foreach ($components as $label => $value): ?>
                        <li>
                            <span class="cost-bar-label"><?= e($label) ?></span>
                            <span class="cost-bar-track"><span class="cost-bar-fill" style="width: <?= (int) round($value / $maxComponent * 100) ?>%"></span></span>
                            <span class="cost-bar-value"><?= e(compact_money($value)) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <p class="calc-note">
                Secuencia orientativa según potencia disponible y sistema de labranza. Un técnico debe ajustar el ancho, la profundidad y el número de pasadas
                a la textura, la humedad, los residuos y la topografía de cada lote. Los costos usan diésel a 27 $/L y operador a 350 $/h.
            </p>
            <div class="cta-actions">
                <a class="btn btn-primary" href="<?= e(url('/costos')) ?>">Ajustar costos con mis datos <?= icon('arrow-right', 'icon icon-sm') ?></a>
                <a class="btn btn-outline" href="<?= e(url('/implementos?tractor_hp=' . (int) $input['hp'])) ?>">Ver implementos para <?= (int) $input['hp'] ?> HP</a>
            </div>
        <?php endif; ?>
    </div>
</div>
