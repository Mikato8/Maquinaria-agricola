<?php
/** @var array<int, array<string, mixed>> $resources */
/** @var int $total */
/** @var \App\Core\Paginator $paginator */
/** @var array<string, mixed> $filters */
/** @var array<string, int> $kinds */
/** @var array<string, string> $categories */
?><section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Tecnología</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Agricultura de precisión</span>
        <h1>Tecnología que sí se puede usar en el campo mexicano.</h1>
        <p class="page-hero-lead">
            Software de guiado y gestión, hardware RTK e ISOBUS, datasets y modelos de IA con licencia clara.
            Cada ficha dice qué resuelve, qué requiere y qué tan viable es aquí, con enlace a la fuente.
        </p>
        <div class="kpi-row">
            <div class="kpi"><strong><?= (int) $total ?></strong><span>Recursos catalogados</span></div>
            <?php foreach (['software', 'hardware', 'dataset', 'modelo'] as $kind): ?>
                <?php if (isset($kinds[$kind])): ?>
                    <div class="kpi"><strong><?= (int) $kinds[$kind] ?></strong><span><?= e(tech_kind_plural($kind)) ?></span></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="tag-row category-tabs">
            <a class="tag tag-sm <?= $filters['kind'] === '' ? 'is-active' : '' ?>" href="<?= e(filter_url(['kind' => null])) ?>">Todo</a>
            <?php foreach (category_config('tech_kinds') as $code => $label): ?>
                <a class="tag tag-sm <?= $filters['kind'] === $code ? 'is-active' : '' ?>" href="<?= e(filter_url(['kind' => $code])) ?>"><?= e($label) ?><?= isset($kinds[$code]) ? ' · ' . (int) $kinds[$code] : '' ?></a>
            <?php endforeach; ?>
        </div>

        <form class="filter-panel" method="get" action="<?= e(url('/tecnologia')) ?>">
            <div class="field-grid field-grid-4">
                <div class="field">
                    <label for="q">Buscar</label>
                    <input type="search" id="q" name="q" value="<?= e($filters['q']) ?>" placeholder="Ej. RTK, NDVI, ISOBUS, malezas">
                </div>
                <div class="field">
                    <label for="category">Categoría</label>
                    <select id="category" name="category">
                        <option value="">Todas</option>
                        <?php foreach ($categories as $code => $label): ?>
                            <option value="<?= e($code) ?>" <?= $filters['category'] === $code ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="license">Licencia</label>
                    <select id="license" name="license">
                        <option value="">Cualquiera</option>
                        <option value="abierta" <?= $filters['license'] === 'abierta' ? 'selected' : '' ?>>Código o datos abiertos</option>
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <button class="btn btn-primary" type="submit"><?= icon('filter', 'icon icon-sm') ?> Filtrar</button>
                <a class="btn btn-ghost" href="<?= e(url('/tecnologia')) ?>">Limpiar</a>
            </div>
            <?php if ($filters['kind'] !== ''): ?><input type="hidden" name="kind" value="<?= e($filters['kind']) ?>"><?php endif; ?>
        </form>

        <?php if ($resources === []): ?>
            <div class="empty-state">
                <span class="empty-icon"><?= icon('cpu') ?></span>
                <h2>Sin resultados</h2>
                <p>Prueba otra palabra o quita el filtro de licencia.</p>
                <a class="btn btn-primary" href="<?= e(url('/tecnologia')) ?>">Ver todo</a>
            </div>
        <?php else: ?>
            <div class="tech-grid">
                <?php foreach ($resources as $resource): ?>
                    <?php \App\Core\View::partial('partials/tech-card', ['resource' => $resource]); ?>
                <?php endforeach; ?>
            </div>
            <div class="pagination-wrap">
                <?php \App\Core\View::partial('partials/pagination', ['paginator' => $paginator]); ?>
            </div>
        <?php endif; ?>

        <div class="notice notice-info">
            <?= icon('info', 'icon icon-sm') ?>
            <p>Los enlaces, licencias y disponibilidad se verificaron en la fecha indicada en cada ficha. Si un proyecto cambió de licencia o de URL, repórtalo en <a href="<?= e(url('/contacto')) ?>">contacto</a>.</p>
        </div>
    </div>
</section>
