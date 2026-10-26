<?php
/** @var array<string, mixed> $machine */
$compareEnabled = $compareEnabled ?? false;
?><article class="machine-card">
    <a class="machine-media" href="<?= e(url('/maquinas/' . $machine['slug'])) ?>" aria-hidden="true" tabindex="-1">
        <span class="machine-emoji"><?= e(machine_category_emoji((string) $machine['category'])) ?></span>
        <span class="machine-brand"><?= e($machine['brand_name']) ?></span>
    </a>
    <div class="machine-body">
        <div>
            <h3><a href="<?= e(url('/maquinas/' . $machine['slug'])) ?>"><?= e($machine['name']) ?></a></h3>
            <p class="machine-model"><?= e($machine['model']) ?> · <?= e(years_label($machine['year_from'] !== null ? (int) $machine['year_from'] : null, $machine['year_to'] !== null ? (int) $machine['year_to'] : null)) ?></p>
        </div>
        <p class="machine-summary"><?= e(excerpt($machine['summary'] ?: $machine['description'], 120)) ?></p>
        <div class="machine-specs">
            <span class="spec-chip"><em>Potencia</em> <?= e(hp_label($machine['power_hp'])) ?></span>
            <span class="spec-chip"><em>Tracción</em> <?= e($machine['traction']) ?></span>
            <?php if (!empty($machine['lift_capacity_kg'])): ?>
                <span class="spec-chip"><em>Levante</em> <?= e(num($machine['lift_capacity_kg'])) ?> kg</span>
            <?php endif; ?>
            <?php if (!empty($machine['transmission'])): ?>
                <span class="spec-chip"><?= e(mb_strimwidth((string) $machine['transmission'], 0, 34, '…')) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="machine-foot">
        <span class="machine-price">
            <?= e(price_range($machine['price_min_mxn'], $machine['price_max_mxn'])) ?>
            <small><?= e($machine['price_note'] ?: 'Precio de referencia') ?></small>
        </span>
        <a class="btn btn-ghost btn-sm" href="<?= e(url('/maquinas/' . $machine['slug'])) ?>">Ficha técnica</a>
    </div>
    <?php if ($compareEnabled): ?>
        <label class="machine-compare">
            <input type="checkbox" name="ids[]" value="<?= (int) $machine['id'] ?>">
            Comparar
        </label>
    <?php endif; ?>
</article>
