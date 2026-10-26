<?php
/** @var array<int, array<string, mixed>> $implements */
/** @var int $total */
/** @var \App\Core\Paginator $paginator */
/** @var array<string, mixed> $filters */
/** @var array<string, array{name: string, emoji: string, description: string}> $categories */
/** @var array<int, array{category: string, count: int}> $counts */
$countMap = [];
foreach ($counts as $row) {
    $countMap[(string) $row['category']] = (int) $row['count'];
}
?><section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Implementos</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Aditamentos y aperos</span>
        <h1>El implemento correcto rinde más que un tractor más grande.</h1>
        <p class="page-hero-lead">
            Ancho de trabajo, profundidad, potencia mínima, peso y precio de referencia. Cada ficha explica cómo se opera,
            cómo se calibra y qué revisar antes de engancharlo.
        </p>
        <div class="kpi-row">
            <div class="kpi"><strong><?= (int) $total ?></strong><span>Implementos fichados</span></div>
            <div class="kpi"><strong><?= count($categories) ?></strong><span>Funciones</span></div>
            <div class="kpi"><strong>10</strong><span>Categorías</span></div>
            <div class="kpi"><strong>PDF</strong><span>Fichas descargables</span></div>
        </div>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="tag-row category-tabs">
            <a class="tag tag-sm <?= $filters['category'] === '' ? 'is-active' : '' ?>" href="<?= e(filter_url(['category' => null])) ?>">Todos</a>
            <?php foreach ($categories as $code => $category): ?>
                <a class="tag tag-sm <?= $filters['category'] === $code ? 'is-active' : '' ?>" href="<?= e(filter_url(['category' => $code])) ?>"><?= e($category['emoji']) ?> <?= e($category['name']) ?><?= isset($countMap[$code]) ? ' · ' . $countMap[$code] : '' ?></a>
            <?php endforeach; ?>
        </div>

        <form class="filter-panel" method="get" action="<?= e(url('/implementos')) ?>">
            <div class="field-grid field-grid-4">
                <div class="field">
                    <label for="q">Buscar implemento</label>
                    <input type="search" id="q" name="q" value="<?= e($filters['q']) ?>" placeholder="Ej. rastra, aspersora, subsolador">
                </div>
                <div class="field">
                    <label for="tractor_hp">Potencia de mi tractor (HP)</label>
                    <input type="number" id="tractor_hp" name="tractor_hp" min="8" max="600" step="5" value="<?= (int) $filters['tractor_hp'] ?: '' ?>" placeholder="75">
                </div>
                <div class="field">
                    <label for="budget">Presupuesto (MXN)</label>
                    <input type="number" id="budget" name="budget" min="0" step="10000" value="<?= (int) $filters['budget'] ?: '' ?>" placeholder="250000">
                </div>
                <div class="field">
                    <label for="sort">Ordenar por</label>
                    <select id="sort" name="sort">
                        <option value="relevancia" <?= $filters['sort'] === 'relevancia' ? 'selected' : '' ?>>Función</option>
                        <option value="precio_asc" <?= $filters['sort'] === 'precio_asc' ? 'selected' : '' ?>>Precio: menor a mayor</option>
                        <option value="precio_desc" <?= $filters['sort'] === 'precio_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
                        <option value="ancho" <?= $filters['sort'] === 'ancho' ? 'selected' : '' ?>>Mayor ancho de trabajo</option>
                    </select>
                </div>
            </div>
            <?php if ($filters['category'] !== ''): ?>
                <input type="hidden" name="category" value="<?= e($filters['category']) ?>">
            <?php endif; ?>
            <div class="filter-actions">
                <div class="filter-actions-left">
                    <button class="btn btn-primary" type="submit"><?= icon('filter', 'icon icon-sm') ?> Filtrar</button>
                    <a class="btn btn-ghost" href="<?= e(url('/implementos')) ?>">Limpiar</a>
                </div>
                <span class="results-meta"><?= (int) $total ?> implemento<?= (int) $total === 1 ? '' : 's' ?></span>
            </div>
        </form>

        <?php if ($implements === []): ?>
            <div class="empty-state">
                <span class="empty-icon"><?= icon('tool') ?></span>
                <h2>No hay implementos con esos criterios</h2>
                <p>Prueba con otra potencia de tractor, quita el presupuesto o revisa todas las categorías.</p>
                <a class="btn btn-primary" href="<?= e(url('/implementos')) ?>">Ver todos</a>
            </div>
        <?php else: ?>
            <div class="machine-grid">
                <?php foreach ($implements as $implement): ?>
                    <?php \App\Core\View::partial('partials/implement-card', ['implement' => $implement]); ?>
                <?php endforeach; ?>
            </div>
            <div class="pagination-wrap">
                <?php \App\Core\View::partial('partials/pagination', ['paginator' => $paginator]); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-muted">
    <div class="container">
        <div class="callout-band">
            <div class="callout-band-copy">
                <h2>¿No sabes si tu tractor lo aguanta?</h2>
                <p>La calculadora de compatibilidad estima fuerza de tiro, potencia, levante, caudal y lastre con tus condiciones de suelo, humedad y pendiente.</p>
                <a class="btn btn-sand" href="<?= e(url('/compatibilidad')) ?>">Abrir calculadora <?= icon('arrow-right', 'icon icon-sm') ?></a>
            </div>
            <div class="callout-band-note">
                <?= icon('info', 'icon icon-lg') ?>
                <p>Los precios de implementos varían mucho por importador y temporada. Úsalos como orden de magnitud, no como cotización.</p>
            </div>
        </div>
    </div>
</section>
