<?php
/** @var array<int, array<string, mixed>> $cases */
/** @var int $total */
/** @var \App\Core\Paginator $paginator */
/** @var array<string, mixed> $filters */
/** @var array<int, array{category: string, total: int}> $categoryCounts */
/** @var array<int, array<string, mixed>> $contributors */
/** @var array<int, array{code: string, name: string, region: string}> $states */
?><section class="page-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Taller</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Comunidad de técnicos</span>
        <h1>Reparaciones documentadas, no consejos de pasillo.</h1>
        <p class="page-hero-lead">
            Casos con síntoma, diagnóstico, refacciones, costo y resultado. Publica tu experiencia y ayuda a que otro
            taller no repita el mismo camino de prueba y error.
        </p>
        <div class="kpi-row">
            <div class="kpi"><strong><?= (int) $total ?></strong><span>Casos publicados</span></div>
            <div class="kpi"><strong><?= count($categoryCounts) ?></strong><span>Tipos de máquina</span></div>
            <div class="kpi"><strong><?= count($contributors) ?></strong><span>Talleres activos</span></div>
            <div class="kpi"><strong>Gratis</strong><span>Publicar y consultar</span></div>
        </div>
    </div>
</section>

<section class="section section-tight">
    <div class="container faults-layout">
        <aside class="faults-aside">
            <div class="aside-card">
                <h3>Por máquina</h3>
                <div class="aside-list">
                    <a href="<?= e(filter_url(['category' => null])) ?>">Todos <span><?= (int) $total ?></span></a>
                    <?php foreach (category_config('machine_categories') as $code => $category): ?>
                        <?php
                        $count = 0;
                        foreach ($categoryCounts as $row) {
                            if ($row['category'] === $code) {
                                $count = (int) $row['total'];
                            }
                        }
                        if ($count === 0) {
                            continue;
                        }
                        ?>
                        <a href="<?= e(filter_url(['category' => $code])) ?>" class="<?= $filters['category'] === $code ? 'is-current' : '' ?>"><?= e($category['emoji']) ?> <?= e($category['name']) ?> <span><?= $count ?></span></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="aside-card">
                <h3>Por resultado</h3>
                <div class="aside-list">
                    <a href="<?= e(filter_url(['outcome' => 'resuelto'])) ?>" class="<?= $filters['outcome'] === 'resuelto' ? 'is-current' : '' ?>">Resuelto</a>
                    <a href="<?= e(filter_url(['outcome' => 'parcial'])) ?>" class="<?= $filters['outcome'] === 'parcial' ? 'is-current' : '' ?>">Mejoró parcialmente</a>
                    <a href="<?= e(filter_url(['outcome' => 'no_resuelto'])) ?>" class="<?= $filters['outcome'] === 'no_resuelto' ? 'is-current' : '' ?>">No se resolvió</a>
                </div>
            </div>

            <div class="aside-card aside-card-dark">
                <h3>Publica tu caso</h3>
                <p>Necesitas cuenta verificada con cédula, registro de taller o revisión manual. Toma 15 minutos y queda con folio.</p>
                <a class="btn btn-sand btn-block btn-sm" href="<?= e(url('/taller/nuevo')) ?>">Empezar</a>
            </div>

            <?php if ($contributors !== []): ?>
                <div class="aside-card">
                    <h3>Talleres activos</h3>
                    <div class="aside-list">
                        <?php foreach ($contributors as $contributor): ?>
                            <a href="<?= e(url('/perfil-publico/' . $contributor['id'])) ?>">
                                <span><?= e($contributor['workshop_name'] ?: $contributor['name']) ?><br><small class="muted"><?= (int) $contributor['cases_count'] ?> casos</small></span>
                                <?= icon('arrow-right', 'icon icon-xs') ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </aside>

        <div>
            <form class="filter-panel" method="get" action="<?= e(url('/taller')) ?>">
                <div class="field-grid field-grid-4">
                    <div class="field">
                        <label for="q">Buscar caso</label>
                        <input type="search" id="q" name="q" value="<?= e($filters['q']) ?>" placeholder="Ej. no arranca, 5075E, hidráulico">
                    </div>
                    <div class="field">
                        <label for="categoria">Máquina</label>
                        <select id="categoria" name="category">
                            <option value="">Todas</option>
                            <?php foreach (category_config('machine_categories') as $code => $category): ?>
                                <option value="<?= e($code) ?>" <?= $filters['category'] === $code ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="estado">Estado</label>
                        <select id="estado" name="state">
                            <option value="">Todos</option>
                            <?php foreach ($states as $state): ?>
                                <option value="<?= e($state['code']) ?>" <?= $filters['state'] === $state['code'] ? 'selected' : '' ?>><?= e($state['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="sort">Ordenar</label>
                        <select id="sort" name="sort">
                            <option value="recientes" <?= $filters['sort'] === 'recientes' ? 'selected' : '' ?>>Recientes</option>
                            <option value="vistos" <?= $filters['sort'] === 'vistos' ? 'selected' : '' ?>>Más vistos</option>
                            <option value="util" <?= $filters['sort'] === 'util' ? 'selected' : '' ?>>Más útiles</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <div class="filter-actions-left">
                        <button class="btn btn-primary" type="submit"><?= icon('filter', 'icon icon-sm') ?> Filtrar</button>
                        <a class="btn btn-ghost" href="<?= e(url('/taller')) ?>">Limpiar</a>
                    </div>
                    <span class="results-meta"><?= (int) $total ?> caso<?= (int) $total === 1 ? '' : 's' ?></span>
                </div>
            </form>

            <?php if ($cases === []): ?>
                <div class="empty-state">
                    <span class="empty-icon"><?= icon('wrench') ?></span>
                    <h2>No hay casos con esos filtros</h2>
                    <p>Sé el primero en documentar una reparación de esta categoría.</p>
                    <a class="btn btn-primary" href="<?= e(url('/taller/nuevo')) ?>">Publicar un caso</a>
                </div>
            <?php else: ?>
                <div class="workshop-grid">
                    <?php foreach ($cases as $case): ?>
                        <?php \App\Core\View::partial('partials/workshop-card', ['case' => $case]); ?>
                    <?php endforeach; ?>
                </div>
                <div class="pagination-wrap">
                    <?php \App\Core\View::partial('partials/pagination', ['paginator' => $paginator]); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
