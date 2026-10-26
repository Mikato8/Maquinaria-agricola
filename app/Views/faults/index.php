<?php
/** @var array<int, array<string, mixed>> $faults */
/** @var int $total */
/** @var \App\Core\Paginator $paginator */
/** @var array<string, mixed> $filters */
/** @var array<int, array{system: string, count: int}> $systems */
/** @var array<string, array{name: string, emoji: string, singular: string}> $categories */
$hasFilters = $filters['q'] !== '' || $filters['system'] !== '' || $filters['category'] !== '' || $filters['severity'] !== '';
?><section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Fallas</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Taller y diagnóstico</span>
        <h1>Del síntoma a la causa, sin cambiar piezas al azar.</h1>
        <p class="page-hero-lead">
            Fichas de falla con causas ordenadas por probabilidad, pruebas concretas, herramientas necesarias,
            costo estimado de refacción y qué hacer para que no vuelva a pasar.
        </p>
        <div class="kpi-row">
            <div class="kpi"><strong><?= (int) $total ?></strong><span>Fallas documentadas</span></div>
            <div class="kpi"><strong><?= count($systems) ?></strong><span>Sistemas cubiertos</span></div>
            <div class="kpi"><strong><?= (int) $codes ?></strong><span>Códigos de falla</span></div>
            <div class="kpi"><strong>PDF</strong><span>Fichas imprimibles</span></div>
        </div>
    </div>
</section>

<section class="section section-tight">
    <div class="container faults-layout">
        <aside class="faults-aside">
            <div class="aside-card">
                <h3>Por sistema</h3>
                <div class="aside-list">
                    <a href="<?= e(filter_url(['system' => null])) ?>" class="<?= $filters['system'] === '' ? 'is-current' : '' ?>">Todos <span><?= (int) $total ?></span></a>
                    <?php foreach ($systems as $system): ?>
                        <a href="<?= e(filter_url(['system' => $system['system']])) ?>" class="<?= $filters['system'] === $system['system'] ? 'is-current' : '' ?>">
                            <?= e(fault_system_label((string) $system['system'])) ?> <span><?= (int) $system['count'] ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="aside-card">
                <h3>Por máquina</h3>
                <div class="aside-list">
                    <?php foreach ($categories as $code => $category): ?>
                        <a href="<?= e(filter_url(['category' => $code])) ?>" class="<?= $filters['category'] === $code ? 'is-current' : '' ?>"><?= e($category['emoji']) ?> <?= e($category['name']) ?></a>
                    <?php endforeach; ?>
                    <a href="<?= e(filter_url(['category' => 'implemento'])) ?>" class="<?= $filters['category'] === 'implemento' ? 'is-current' : '' ?>">🔧 Implementos</a>
                </div>
            </div>
            <div class="aside-card aside-card-dark">
                <h3>¿Ya lo reparaste?</h3>
                <p>Publica el caso con fotos, costo y resultado. Ayudas a que otro técnico no repita el mismo camino.</p>
                <a class="btn btn-sand btn-sm btn-block" href="<?= e(url('/taller/nuevo')) ?>">Documentar reparación</a>
            </div>
        </aside>

        <div>
            <form class="filter-panel" method="get" action="<?= e(url('/fallas')) ?>">
                <div class="field-grid field-grid-4">
                    <div class="field">
                        <label for="q">Síntoma o palabra clave</label>
                        <input type="search" id="q" name="q" value="<?= e($filters['q']) ?>" placeholder="Ej. humo negro, no arranca, TDF">
                    </div>
                    <div class="field">
                        <label for="system">Sistema</label>
                        <select id="system" name="system">
                            <option value="">Todos</option>
                            <?php foreach (category_config('fault_systems') as $code => $label): ?>
                                <option value="<?= e($code) ?>" <?= $filters['system'] === $code ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="categoria">Máquina</label>
                        <select id="categoria" name="category">
                            <option value="">Todas</option>
                            <?php foreach ($categories as $code => $category): ?>
                                <option value="<?= e($code) ?>" <?= $filters['category'] === $code ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                            <?php endforeach; ?>
                            <option value="general" <?= $filters['category'] === 'general' ? 'selected' : '' ?>>General</option>
                            <option value="implemento" <?= $filters['category'] === 'implemento' ? 'selected' : '' ?>>Implementos</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="severity">Gravedad</label>
                        <select id="severity" name="severity">
                            <option value="">Todas</option>
                            <option value="leve" <?= $filters['severity'] === 'leve' ? 'selected' : '' ?>>Leve</option>
                            <option value="media" <?= $filters['severity'] === 'media' ? 'selected' : '' ?>>Media</option>
                            <option value="alta" <?= $filters['severity'] === 'alta' ? 'selected' : '' ?>>Alta</option>
                            <option value="critica" <?= $filters['severity'] === 'critica' ? 'selected' : '' ?>>Crítica</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <div class="filter-actions-left">
                        <button class="btn btn-primary" type="submit"><?= icon('filter', 'icon icon-sm') ?> Filtrar</button>
                        <?php if ($hasFilters): ?><a class="btn btn-ghost" href="<?= e(url('/fallas')) ?>">Limpiar</a><?php endif; ?>
                    </div>
                    <label class="inline-select">Ordenar
                        <select name="sort" onchange="this.form.submit()">
                            <option value="relevancia" <?= $filters['sort'] === 'relevancia' ? 'selected' : '' ?>>Gravedad</option>
                            <option value="vistas" <?= $filters['sort'] === 'vistas' ? 'selected' : '' ?>>Más consultadas</option>
                            <option value="dificultad_asc" <?= $filters['sort'] === 'dificultad_asc' ? 'selected' : '' ?>>Más sencillas primero</option>
                            <option value="nuevas" <?= $filters['sort'] === 'nuevas' ? 'selected' : '' ?>>Recientes</option>
                        </select>
                    </label>
                </div>
            </form>

            <?php if ($faults === []): ?>
                <div class="empty-state">
                    <span class="empty-icon"><?= icon('wrench') ?></span>
                    <h2>No encontramos esa falla</h2>
                    <p>Describe el síntoma con otras palabras, elige el sistema o revisa los códigos de falla.</p>
                    <a class="btn btn-primary" href="<?= e(url('/codigos')) ?>">Ver códigos de falla</a>
                </div>
            <?php else: ?>
                <div class="fault-grid">
                    <?php foreach ($faults as $fault): ?>
                        <?php \App\Core\View::partial('partials/fault-card', ['fault' => $fault]); ?>
                    <?php endforeach; ?>
                </div>
                <div class="pagination-wrap">
                    <?php \App\Core\View::partial('partials/pagination', ['paginator' => $paginator]); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
