<?php
/** @var array<int, array<string, mixed>> $terms */
/** @var string $q */
/** @var string $category */
/** @var array<int, array{category: string, total: int}> $categories */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Glosario</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Lenguaje común</span>
        <h1>Glosario de maquinaria y precisión agrícola.</h1>
        <p class="page-hero-lead">
            Los términos que aparecen en fichas, manuales y pláticas de taller: TDF, levante a 610 mm, ISOBUS, RTK,
            carga específica, lastre, patinamiento y más.
        </p>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <form class="filter-panel" method="get" action="<?= e(url('/glosario')) ?>">
            <div class="field-grid field-grid-3">
                <div class="field">
                    <label for="q">Buscar término</label>
                    <input type="search" id="q" name="q" value="<?= e($q) ?>" placeholder="Ej. lastre, TDF, ISOBUS">
                </div>
                <div class="field">
                    <label for="category">Categoría</label>
                    <select id="category" name="category">
                        <option value="">Todas</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= e($cat['category']) ?>" <?= $category === $cat['category'] ? 'selected' : '' ?>><?= e(ucfirst($cat['category'])) ?> (<?= (int) $cat['total'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary" type="submit"><?= icon('search', 'icon icon-sm') ?> Buscar</button>
                </div>
            </div>
        </form>

        <?php if ($terms === []): ?>
            <div class="empty-state">
                <span class="empty-icon"><?= icon('book-open') ?></span>
                <h2>No encontramos ese término</h2>
                <p>Propón una palabra para agregarla al glosario.</p>
                <a class="btn btn-primary" href="<?= e(url('/contacto')) ?>">Sugerir término</a>
            </div>
        <?php else: ?>
            <div class="glossary-grid">
                <?php foreach ($terms as $term): ?>
                    <article class="glossary-item">
                        <h2><?= e($term['term']) ?></h2>
                        <p><?= e($term['definition']) ?></p>
                        <span class="glossary-cat"><?= e(ucfirst((string) $term['category'])) ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
