<?php
/** @var array<string, mixed> $resource */
$kindIcons = [
    'software' => 'code',
    'hardware' => 'chip',
    'dataset' => 'database',
    'modelo' => 'cpu',
    'espacio' => 'sparkles',
    'norma' => 'shield-check',
];
?><article class="tech-card">
    <div class="tech-card-top">
        <span class="tech-kind"><?= icon($kindIcons[(string) $resource['kind']] ?? 'cpu', 'icon icon-xs') ?> <?= e(tech_kind_label((string) $resource['kind'])) ?></span>
        <?php if (!empty($resource['license'])): ?>
            <span class="badge badge-soft"><?= e(mb_strimwidth((string) $resource['license'], 0, 26, '…')) ?></span>
        <?php endif; ?>
    </div>
    <h3><a href="<?= e(url('/tecnologia/' . $resource['slug'])) ?>"><?= e($resource['name']) ?></a></h3>
    <p><?= e(excerpt($resource['description'], 130)) ?></p>
    <div class="tech-foot">
        <span><?= e($resource['vendor'] ?: tech_category_label((string) $resource['category'])) ?></span>
        <a class="text-link" href="<?= e(url('/tecnologia/' . $resource['slug'])) ?>">Ver ficha <?= icon('arrow-right', 'icon icon-xs') ?></a>
    </div>
</article>
