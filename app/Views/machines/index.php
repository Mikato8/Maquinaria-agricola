<?php
/** @var array<int, array<string, mixed>> $machines */
/** @var int $total */
/** @var \App\Core\Paginator $paginator */
/** @var array<string, mixed> $filters */
/** @var array<int, array{slug: string, name: string, count: int}> $brands */
/** @var array<string, array{name: string, emoji: string}> $categories */
$hasFilters = $filters['q'] !== '' || $filters['category'] !== '' || $filters['brand'] !== '' || $filters['traction'] !== ''
    || (int) $filters['min_hp'] > 0 || (int) $filters['max_hp'] > 0 || $filters['price'] !== '';
?><section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Máquinas</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Catálogo técnico</span>
        <h1>Máquinas agrícolas a la venta en México, con los datos que importan.</h1>
        <p class="page-hero-lead">
            Potencia real, capacidad de levante, caudal hidráulico, transmisión, peso y precio de referencia.
            Cada ficha enlaza las fallas típicas y los implementos que puede mover sin forzarse.
        </p>
        <div class="kpi-row">
            <div class="kpi"><strong><?= (int) $total ?></strong><span>Máquinas en el catálogo</span></div>
            <div class="kpi"><strong><?= count($brands) ?></strong><span>Marcas con presencia</span></div>
            <div class="kpi"><strong>8</strong><span>Categorías</span></div>
            <div class="kpi"><strong>0 $</strong><span>Consulta sin registro</span></div>
        </div>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <form class="filter-panel" method="get" action="<?= e(url('/maquinas')) ?>">
            <div class="field-grid field-grid-4">
                <div class="field">
                    <label for="q">Buscar por marca, modelo o motor</label>
                    <input type="search" id="q" name="q" value="<?= e($filters['q']) ?>" placeholder="Ej. 5075E, tractor 90 HP, Perkins">
                </div>
                <div class="field">
                    <label for="category">Categoría</label>
                    <select id="category" name="category">
                        <option value="">Todas</option>
                        <?php foreach ($categories as $code => $category): ?>
                            <option value="<?= e($code) ?>" <?= $filters['category'] === $code ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="brand">Marca</label>
                    <select id="brand" name="brand">
                        <option value="">Todas</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= e($brand['slug']) ?>" <?= $filters['brand'] === $brand['slug'] ? 'selected' : '' ?>><?= e($brand['name']) ?> (<?= (int) $brand['count'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="traction">Tracción</label>
                    <select id="traction" name="traction">
                        <option value="">Cualquiera</option>
                        <?php foreach (['2WD' => '2WD', '4WD' => '4WD', 'oruga' => 'Oruga'] as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $filters['traction'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="field-grid field-grid-4">
                <div class="field">
                    <label for="min_hp">Potencia mínima (HP)</label>
                    <input type="number" id="min_hp" name="min_hp" min="0" max="700" step="5" value="<?= (int) $filters['min_hp'] ?: '' ?>" placeholder="0">
                </div>
                <div class="field">
                    <label for="max_hp">Potencia máxima (HP)</label>
                    <input type="number" id="max_hp" name="max_hp" min="0" max="700" step="5" value="<?= (int) $filters['max_hp'] ?: '' ?>" placeholder="700">
                </div>
                <div class="field">
                    <label for="price">Precio de referencia</label>
                    <select id="price" name="price">
                        <option value="">Cualquiera</option>
                        <option value="hasta500" <?= $filters['price'] === 'hasta500' ? 'selected' : '' ?>>Hasta $500 mil</option>
                        <option value="500a1000" <?= $filters['price'] === '500a1000' ? 'selected' : '' ?>>$500 mil a $1 millón</option>
                        <option value="mas1000" <?= $filters['price'] === 'mas1000' ? 'selected' : '' ?>>Más de $1 millón</option>
                    </select>
                </div>
                <div class="field">
                    <label for="sort">Ordenar por</label>
                    <select id="sort" name="sort">
                        <option value="relevancia" <?= $filters['sort'] === 'relevancia' ? 'selected' : '' ?>>Relevancia</option>
                        <option value="potencia_asc" <?= $filters['sort'] === 'potencia_asc' ? 'selected' : '' ?>>Potencia: menor a mayor</option>
                        <option value="potencia_desc" <?= $filters['sort'] === 'potencia_desc' ? 'selected' : '' ?>>Potencia: mayor a menor</option>
                        <option value="precio_asc" <?= $filters['sort'] === 'precio_asc' ? 'selected' : '' ?>>Precio: menor a mayor</option>
                        <option value="precio_desc" <?= $filters['sort'] === 'precio_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
                        <option value="nuevos" <?= $filters['sort'] === 'nuevos' ? 'selected' : '' ?>>Modelo más reciente</option>
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <div class="filter-actions-left">
                    <button class="btn btn-primary" type="submit"><?= icon('filter', 'icon icon-sm') ?> Aplicar filtros</button>
                    <?php if ($hasFilters): ?>
                        <a class="btn btn-ghost" href="<?= e(url('/maquinas')) ?>">Limpiar</a>
                    <?php endif; ?>
                </div>
                <span class="results-meta"><?= (int) $total ?> resultado<?= (int) $total === 1 ? '' : 's' ?></span>
            </div>
        </form>

        <?php if ($hasFilters): ?>
            <div class="active-filters">
                <span>Filtros activos:</span>
                <?php if ($filters['q'] !== ''): ?><a href="<?= e(filter_url(['q' => null])) ?>">“<?= e($filters['q']) ?>” <?= icon('close', 'icon icon-xs') ?></a><?php endif; ?>
                <?php if ($filters['category'] !== ''): ?><a href="<?= e(filter_url(['category' => null])) ?>"><?= e(machine_category_label($filters['category'])) ?> <?= icon('close', 'icon icon-xs') ?></a><?php endif; ?>
                <?php if ($filters['brand'] !== ''): ?><a href="<?= e(filter_url(['brand' => null])) ?>">Marca: <?= e($filters['brand']) ?> <?= icon('close', 'icon icon-xs') ?></a><?php endif; ?>
                <?php if ($filters['traction'] !== ''): ?><a href="<?= e(filter_url(['traction' => null])) ?>"><?= e($filters['traction']) ?> <?= icon('close', 'icon icon-xs') ?></a><?php endif; ?>
                <?php if ((int) $filters['min_hp'] > 0 || (int) $filters['max_hp'] > 0): ?><a href="<?= e(filter_url(['min_hp' => null, 'max_hp' => null])) ?>"><?= (int) $filters['min_hp'] ?: '0' ?>–<?= (int) $filters['max_hp'] ?: '∞' ?> HP <?= icon('close', 'icon icon-xs') ?></a><?php endif; ?>
                <?php if ($filters['price'] !== ''): ?><a href="<?= e(filter_url(['price' => null])) ?>">Rango de precio <?= icon('close', 'icon icon-xs') ?></a><?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($machines === []): ?>
            <div class="empty-state">
                <span class="empty-icon"><?= icon('tractor') ?></span>
                <h2>No encontramos máquinas con esos filtros</h2>
                <p>Prueba con menos restricciones, revisa la ortografía del modelo o explora todo el catálogo.</p>
                <a class="btn btn-primary" href="<?= e(url('/maquinas')) ?>">Ver todo el catálogo</a>
            </div>
        <?php else: ?>
            <form method="get" action="<?= e(url('/maquinas/comparar')) ?>" data-compare-form>
                <div class="machine-grid">
                    <?php foreach ($machines as $machine): ?>
                        <?php \App\Core\View::partial('partials/machine-card', ['machine' => $machine, 'compareEnabled' => true]); ?>
                    <?php endforeach; ?>
                </div>
                <div class="compare-bar no-print">
                    <p><?= icon('layers', 'icon icon-sm') ?> Selecciona hasta 3 máquinas para comparar potencia, levante, caudal y precio.</p>
                    <div class="compare-bar-actions">
                        <span class="compare-count" data-compare-count>0 de 3</span>
                        <button class="btn btn-primary btn-sm" type="submit">Comparar seleccionadas</button>
                    </div>
                </div>
            </form>

            <div class="pagination-wrap">
                <?php \App\Core\View::partial('partials/pagination', ['paginator' => $paginator]); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-muted">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">No solo potencia</span>
                <h2 class="section-title-sm">Tres datos que evitan una mala compra</h2>
            </div>
        </div>
        <div class="pillar-grid">
            <div class="pillar-card">
                <span class="pillar-icon"><?= icon('layers') ?></span>
                <h3>Capacidad de levante</h3>
                <p>Un implemento montado pesado puede exceder el levante aunque la potencia alcance. Revisa kilos a 610 mm de los brazos, no solo el papel.</p>
                <a class="text-link" href="<?= e(url('/compatibilidad')) ?>">Calcular compatibilidad</a>
            </div>
            <div class="pillar-card">
                <span class="pillar-icon"><?= icon('droplet') ?></span>
                <h3>Caudal hidráulico</h3>
                <p>Implementos con motor hidráulico o ventilador exigen litros por minuto. Un caudal corto quema bombas y arruina la aplicación.</p>
                <a class="text-link" href="<?= e(url('/fallas?system=hidraulico')) ?>">Fallas hidráulicas</a>
            </div>
            <div class="pillar-card">
                <span class="pillar-icon"><?= icon('wrench') ?></span>
                <h3>Refacciones y servicio</h3>
                <p>La mejor máquina es la que se puede reparar cerca. Consulta el directorio de distribuidores, talleres y refaccionarias por estado.</p>
                <a class="text-link" href="<?= e(url('/proveedores')) ?>">Ver proveedores</a>
            </div>
        </div>
    </div>
</section>
