<?php
/** @var array<int, array<string, mixed>> $machines */
/** @var array<int, array<string, mixed>> $available */
$columns = $machines;
$ids = array_map(static fn (array $m): int => (int) $m['id'], $columns);

$max = static function (array $items, string $key): ?float {
    $values = [];
    foreach ($items as $item) {
        if (isset($item[$key]) && $item[$key] !== null && $item[$key] !== '') {
            $values[] = (float) $item[$key];
        }
    }
    return $values === [] ? null : max($values);
};
$min = static function (array $items, string $key): ?float {
    $values = [];
    foreach ($items as $item) {
        if (isset($item[$key]) && $item[$key] !== null && $item[$key] !== '') {
            $values[] = (float) $item[$key];
        }
    }
    return $values === [] ? null : min($values);
};
$bestPower = $max($columns, 'power_hp');
$bestLift = $max($columns, 'lift_capacity_kg');
$bestFlow = $max($columns, 'hydraulic_flow_lpm');
$bestPrice = $min($columns, 'price_min_mxn');

$rows = [
    'Categoría' => static fn (array $m): string => machine_category_label((string) $m['category']),
    'Marca' => static fn (array $m): string => (string) $m['brand_name'],
    'Potencia' => static fn (array $m): string => hp_label($m['power_hp']),
    'Potencia a la TDF' => static fn (array $m): string => hp_label($m['pto_hp']),
    'Tracción' => static fn (array $m): string => (string) $m['traction'],
    'Motor' => static fn (array $m): string => (string) ($m['engine'] ?: '—'),
    'Cilindros' => static fn (array $m): string => $m['cylinders'] !== null ? (string) $m['cylinders'] : '—',
    'Transmisión' => static fn (array $m): string => (string) ($m['transmission'] ?: '—'),
    'TDF' => static fn (array $m): string => ((int) ($m['pto_rpm'] ?? 540)) . ' rpm',
    'Caudal hidráulico' => static fn (array $m): string => $m['hydraulic_flow_lpm'] !== null ? num($m['hydraulic_flow_lpm'], 1) . ' L/min' : '—',
    'Capacidad de levante' => static fn (array $m): string => $m['lift_capacity_kg'] !== null ? num($m['lift_capacity_kg']) . ' kg' : '—',
    'Peso' => static fn (array $m): string => $m['weight_kg'] !== null ? num($m['weight_kg']) . ' kg' : '—',
    'Tanque' => static fn (array $m): string => $m['fuel_tank_l'] !== null ? num($m['fuel_tank_l'], 1) . ' L' : '—',
    'Años' => static fn (array $m): string => years_label($m['year_from'] !== null ? (int) $m['year_from'] : null, $m['year_to'] !== null ? (int) $m['year_to'] : null),
    'Precio de referencia' => static fn (array $m): string => price_range($m['price_min_mxn'], $m['price_max_mxn']),
];
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <a href="<?= e(url('/maquinas')) ?>">Máquinas</a>
            <span aria-current="page">Comparador</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Decisión de compra</span>
        <h1>Compara hasta tres máquinas lado a lado.</h1>
        <p class="page-hero-lead">Potencia, levante, caudal, transmisión y precio de referencia. Los mejores valores de cada fila se resaltan para que la diferencia se vea de un golpe.</p>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <?php if (count($columns) < 2): ?>
            <div class="empty-state">
                <span class="empty-icon"><?= icon('layers') ?></span>
                <h2>Elige al menos dos máquinas</h2>
                <p>Marca las casillas de las máquinas que quieres comparar y vuelve a enviar el formulario.</p>
            </div>
        <?php endif; ?>

        <form class="filter-panel" method="get" action="<?= e(url('/maquinas/comparar')) ?>" data-compare-form>
            <div class="field">
                <label>Máquinas disponibles (máximo 3)</label>
                <div class="compare-picker">
                    <?php foreach ($available as $item): ?>
                        <label class="compare-option">
                            <input type="checkbox" name="ids[]" value="<?= (int) $item['id'] ?>" <?= in_array((int) $item['id'], $ids, true) ? 'checked' : '' ?>>
                            <span><?= e($item['brand_name']) ?> <?= e($item['model']) ?> <small><?= e(hp_label($item['power_hp'])) ?></small></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="filter-actions">
                <button class="btn btn-primary" type="submit">Comparar</button>
                <span class="compare-count" data-compare-count><?= count($columns) ?> de 3</span>
            </div>
        </form>

        <?php if (count($columns) >= 2): ?>
            <div class="table-wrap">
                <table class="compare-table">
                    <thead>
                    <tr>
                        <th scope="col">Característica</th>
                        <?php foreach ($columns as $machine): ?>
                            <th scope="col">
                                <a href="<?= e(url('/maquinas/' . $machine['slug'])) ?>"><?= e($machine['brand_name']) ?> <?= e($machine['model']) ?></a>
                                <span class="compare-price"><?= e(price_range($machine['price_min_mxn'], $machine['price_max_mxn'])) ?></span>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $label => $resolver): ?>
                        <tr>
                            <th scope="row"><?= e($label) ?></th>
                            <?php foreach ($columns as $machine): ?>
                                <?php
                                $isBest = false;
                                if ($label === 'Potencia' && $bestPower !== null) {
                                    $isBest = (float) $machine['power_hp'] === $bestPower;
                                } elseif ($label === 'Capacidad de levante' && $bestLift !== null) {
                                    $isBest = (float) $machine['lift_capacity_kg'] === $bestLift;
                                } elseif ($label === 'Caudal hidráulico' && $bestFlow !== null) {
                                    $isBest = (float) $machine['hydraulic_flow_lpm'] === $bestFlow;
                                } elseif ($label === 'Precio de referencia' && $bestPrice !== null) {
                                    $isBest = (float) $machine['price_min_mxn'] === $bestPrice;
                                }
                                ?>
                                <td class="<?= $isBest ? 'is-best' : '' ?>">
                                    <?= e($resolver($machine)) ?>
                                    <?php if ($isBest): ?><span class="best-mark" title="Mejor valor del grupo">Mejor</span><?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="calc-note">
                El “mejor” valor es el mayor en potencia, levante y caudal, y el menor en precio. No es una recomendación de compra:
                la máquina adecuada depende del cultivo, el suelo, la superficie, el servicio cercano y el costo de las refacciones.
            </p>
        <?php endif; ?>
    </div>
</section>
