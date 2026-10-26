<?php
/** @var string $q */
/** @var string $type */
/** @var array<string, array{label: string, items: array<int, array<string, mixed>>}> $results */
/** @var int $total */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Buscador</span>
        <h1><?= $q !== '' ? 'Resultados para «' . e($q) . '»' : 'Busca en todo el portal' ?></h1>
        <p class="page-hero-lead">Máquinas, implementos, fallas, guías, tecnología, casos de taller y proveedores.</p>

        <form class="hero-search" action="<?= e(url('/buscar')) ?>" method="get" role="search">
            <div class="hero-search-field">
                <?= icon('search', 'icon') ?>
                <input type="search" name="q" value="<?= e($q) ?>" placeholder="Ej. John Deere 6110J, humo negro, RTK" aria-label="Buscar">
            </div>
            <select name="tipo" aria-label="Tipo">
                <option value="">Todo</option>
                <?php foreach (['maquinas' => 'Máquinas', 'implementos' => 'Implementos', 'fallas' => 'Fallas', 'tecnologia' => 'Tecnología', 'guias' => 'Guías', 'taller' => 'Taller', 'proveedores' => 'Proveedores'] as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $type === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Buscar</button>
        </form>
        <?php if ($q !== ''): ?>
            <p class="results-meta" style="margin-top:14px"><?= (int) $total ?> resultado<?= (int) $total === 1 ? '' : 's' ?> en <?= count($results) ?> categoría<?= count($results) === 1 ? '' : 's' ?>.</p>
        <?php endif; ?>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <?php if (mb_strlen($q) < 2): ?>
            <div class="empty-state">
                <span class="empty-icon"><?= icon('search') ?></span>
                <h2>Escribe al menos dos caracteres</h2>
                <p>Puedes buscar por marca, modelo, síntoma, sistema, código o nombre de proveedor.</p>
                <div class="empty-actions">
                    <a class="btn btn-primary" href="<?= e(url('/maquinas')) ?>">Ver máquinas</a>
                    <a class="btn btn-outline" href="<?= e(url('/fallas')) ?>">Ver fallas</a>
                </div>
            </div>
        <?php elseif ($results === []): ?>
            <div class="empty-state">
                <span class="empty-icon"><?= icon('search') ?></span>
                <h2>Sin resultados para «<?= e($q) ?>»</h2>
                <p>Prueba con una palabra más corta, revisa la ortografía o cambia el tipo de contenido.</p>
                <a class="btn btn-primary" href="<?= e(url('/buscar?q=' . urlencode($q))) ?>">Buscar en todo el portal</a>
            </div>
        <?php else: ?>
            <div class="search-results">
                <?php foreach ($results as $key => $group): ?>
                    <section class="search-group">
                        <h2><?= e($group['label']) ?> <span class="count-badge"><?= count($group['items']) ?></span></h2>
                        <?php foreach ($group['items'] as $item): ?>
                            <a class="search-result" href="<?= e($item['url']) ?>">
                                <span class="search-kind"><?= e($group['label']) ?></span>
                                <strong><?= e($item['name']) ?></strong>
                                <p><?= e(excerpt($item['description'] ?? '', 170)) ?><?= !empty($item['extra']) ? ' · ' . e($item['extra']) : '' ?></p>
                            </a>
                        <?php endforeach; ?>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
