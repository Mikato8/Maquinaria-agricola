<?php
/** @var array<string, mixed> $implement */
?><article class="machine-card implement-card">
    <a class="machine-media implement-media" href="<?= e(url('/implementos/' . $implement['slug'])) ?>" tabindex="-1" aria-hidden="true">
        <span class="machine-emoji"><?= e(implement_category_emoji((string) $implement['category'])) ?></span>
        <span class="machine-brand"><?= e(implement_category_label((string) $implement['category'])) ?></span>
    </a>
    <div class="machine-body">
        <div>
            <h3><a href="<?= e(url('/implementos/' . $implement['slug'])) ?>"><?= e($implement['name']) ?></a></h3>
            <p class="machine-model"><?= e($implement['hitch']) ?><?= $implement['working_width_m'] !== null ? ' · ' . e(num($implement['working_width_m'], 2)) . ' m' : '' ?><?= $implement['working_depth_cm'] !== null && (float) $implement['working_depth_cm'] > 0 ? ' · ' . e(num($implement['working_depth_cm'], 0)) . ' cm' : '' ?></p>
        </div>
        <p class="machine-summary"><?= e(excerpt($implement['function'] ?: $implement['description'], 120)) ?></p>
        <div class="machine-specs">
            <span class="spec-chip"><em>Requiere</em> <?= e(power_range($implement['power_min_hp'], $implement['power_max_hp'])) ?></span>
            <?php if ($implement['weight_kg'] !== null): ?>
                <span class="spec-chip"><em>Peso</em> <?= e(num($implement['weight_kg'])) ?> kg</span>
            <?php endif; ?>
            <?php if ($implement['capacity'] !== ''): ?>
                <span class="spec-chip"><?= e(mb_strimwidth((string) $implement['capacity'], 0, 30, '…')) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="machine-foot">
        <span class="machine-price">
            <?= e(price_range($implement['price_min_mxn'], $implement['price_max_mxn'])) ?>
            <small>Precio de referencia</small>
        </span>
        <a class="btn btn-ghost btn-sm" href="<?= e(url('/implementos/' . $implement['slug'])) ?>">Ver ficha</a>
    </div>
</article>
