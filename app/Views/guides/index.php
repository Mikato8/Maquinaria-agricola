<?php
/** @var array<int, array<string, mixed>> $guides */
/** @var int $total */
/** @var \App\Core\Paginator $paginator */
/** @var array<string, mixed> $filters */
/** @var array<string, string> $categories */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Guías</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Lectura técnica</span>
        <h1>Guías para decidir, operar y reparar con criterio.</h1>
        <p class="page-hero-lead">
            Explicaciones largas, con supuestos, tablas y fuentes. Sin promesas de marca y sin recetas que solo funcionan
            en el lote del vecino.
        </p>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="tag-row category-tabs">
            <a class="tag tag-sm <?= $filters['category'] === '' ? 'is-active' : '' ?>" href="<?= e(filter_url(['category' => null])) ?>">Todas</a>
            <?php foreach ($categories as $code => $label): ?>
                <a class="tag tag-sm <?= $filters['category'] === $code ? 'is-active' : '' ?>" href="<?= e(filter_url(['category' => $code])) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </div>

        <form class="filter-panel" method="get" action="<?= e(url('/guias')) ?>">
            <div class="field-grid field-grid-3">
                <div class="field">
                    <label for="q">Buscar en las guías</label>
                    <input type="search" id="q" name="q" value="<?= e($filters['q']) ?>" placeholder="Ej. lastre, RTK, labranza">
                </div>
                <div class="field">
                    <label for="level">Nivel</label>
                    <select id="level" name="level">
                        <option value="">Todos</option>
                        <option value="introductorio" <?= $filters['level'] === 'introductorio' ? 'selected' : '' ?>>Introductorio</option>
                        <option value="intermedio" <?= $filters['level'] === 'intermedio' ? 'selected' : '' ?>>Intermedio</option>
                        <option value="avanzado" <?= $filters['level'] === 'avanzado' ? 'selected' : '' ?>>Avanzado</option>
                    </select>
                </div>
                <div class="field">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary" type="submit"><?= icon('search', 'icon icon-sm') ?> Buscar</button>
                </div>
            </div>
            <?php if ($filters['category'] !== ''): ?><input type="hidden" name="category" value="<?= e($filters['category']) ?>"><?php endif; ?>
        </form>

        <?php if ($guides === []): ?>
            <div class="empty-state">
                <span class="empty-icon"><?= icon('book-open') ?></span>
                <h2>Sin guías para esa búsqueda</h2>
                <p>Prueba con otra palabra o revisa todas las categorías.</p>
                <a class="btn btn-primary" href="<?= e(url('/guias')) ?>">Ver todas</a>
            </div>
        <?php else: ?>
            <div class="guide-grid">
                <?php foreach ($guides as $guide): ?>
                    <a class="guide-card" href="<?= e(url('/guias/' . $guide['slug'])) ?>">
                        <span class="guide-number"><?= e(guide_category_label((string) $guide['category'])) ?> · <?= e(level_label((string) $guide['level'])) ?></span>
                        <h3><?= e($guide['title']) ?></h3>
                        <p><?= e(excerpt($guide['summary'], 160)) ?></p>
                        <span class="guide-meta"><?= (int) $guide['reading_min'] ?> min · <?= e(format_date((string) $guide['published_at'], 'd M Y')) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="pagination-wrap">
                <?php \App\Core\View::partial('partials/pagination', ['paginator' => $paginator]); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
