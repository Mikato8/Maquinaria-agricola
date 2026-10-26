<?php
/** @var array<int, array<string, mixed>> $providers */
/** @var int $total */
/** @var \App\Core\Paginator $paginator */
/** @var array<string, mixed> $filters */
/** @var array<string, int> $types */
/** @var array<int, string> $brands */
/** @var array<int, array{code: string, name: string, region: string}> $states */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Proveedores</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Directorio</span>
        <h1>La refacción más cercana vale más que el folleto.</h1>
        <p class="page-hero-lead">
            Distribuidores, refaccionarias, talleres, renta y financiamiento. Filtra por estado, tipo de servicio y marca.
            Los datos son de referencia: confirma horarios y existencias por teléfono antes de viajar.
        </p>
        <div class="kpi-row">
            <div class="kpi"><strong><?= (int) $total ?></strong><span>Proveedores listados</span></div>
            <div class="kpi"><strong><?= count($types) ?></strong><span>Tipos de servicio</span></div>
            <div class="kpi"><strong><?= count($brands) ?></strong><span>Marcas cubiertas</span></div>
            <div class="kpi"><strong>32</strong><span>Estados</span></div>
        </div>
    </div>
</section>

<section class="section section-tight">
    <div class="container faults-layout">
        <aside class="faults-aside">
            <form class="aside-card" method="get" action="<?= e(url('/proveedores')) ?>">
                <h3>Filtrar</h3>
                <div class="field">
                    <label for="q">Nombre o ciudad</label>
                    <input type="search" id="q" name="q" value="<?= e($filters['q']) ?>" placeholder="Ej. Guadalajara">
                </div>
                <div class="field">
                    <label for="type">Tipo</label>
                    <select id="type" name="type">
                        <option value="">Todos</option>
                        <?php foreach (category_config('provider_types') as $code => $label): ?>
                            <option value="<?= e($code) ?>" <?= $filters['type'] === $code ? 'selected' : '' ?>><?= e($label) ?><?= isset($types[$code]) ? ' (' . (int) $types[$code] . ')' : '' ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="state">Estado</label>
                    <select id="state" name="state">
                        <option value="">Todos</option>
                        <?php foreach ($states as $state): ?>
                            <option value="<?= e($state['code']) ?>" <?= $filters['state'] === $state['code'] ? 'selected' : '' ?>><?= e($state['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="brand">Marca</label>
                    <select id="brand" name="brand">
                        <option value="">Todas</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= e($brand) ?>" <?= $filters['brand'] === $brand ? 'selected' : '' ?>><?= e($brand) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-primary btn-block btn-sm" type="submit">Aplicar</button>
                <a class="text-link" href="<?= e(url('/proveedores')) ?>">Limpiar filtros</a>
            </form>

            <div class="aside-card">
                <h3>Por tipo</h3>
                <div class="aside-list">
                    <a href="<?= e(filter_url(['type' => null])) ?>">Todos <span><?= (int) $total ?></span></a>
                    <?php foreach ($types as $type => $count): ?>
                        <a href="<?= e(filter_url(['type' => $type])) ?>" class="<?= $filters['type'] === $type ? 'is-current' : '' ?>"><?= e(provider_type_label((string) $type)) ?> <span><?= (int) $count ?></span></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="aside-card aside-card-dark">
                <h3>¿Falta tu taller?</h3>
                <p>Publicamos talleres y refaccionarias con datos verificables. Escríbenos con dirección, teléfono y marcas que atiendes.</p>
                <a class="btn btn-sand btn-sm btn-block" href="<?= e(url('/contacto?motivo=proveedor')) ?>">Solicitar alta</a>
            </div>
        </aside>

        <div>
            <?php if ($providers === []): ?>
                <div class="empty-state">
                    <span class="empty-icon"><?= icon('map-pin') ?></span>
                    <h2>Sin proveedores con esos filtros</h2>
                    <p>Prueba con otro estado o tipo de servicio.</p>
                    <a class="btn btn-primary" href="<?= e(url('/proveedores')) ?>">Ver todos</a>
                </div>
            <?php else: ?>
                <div class="provider-list">
                    <?php foreach ($providers as $provider): ?>
                        <?php $providerBrands = pg_array($provider['brands'] ?? []); $providerStates = pg_array($provider['states'] ?? []); ?>
                        <article class="provider-row">
                            <div>
                                <span class="badge badge-soft"><?= e(provider_type_label((string) $provider['type'])) ?></span>
                                <h3><a href="<?= e(url('/proveedores/' . $provider['slug'])) ?>"><?= e($provider['name']) ?></a></h3>
                                <div class="provider-meta">
                                    <span><?= icon('map-pin', 'icon icon-xs') ?> <?= e($provider['city'] ?: 'Varias sucursales') ?><?= $providerStates !== [] ? ', ' . e(implode(' · ', array_map('state_name', $providerStates))) : '' ?></span>
                                    <?php if ($provider['phone'] !== ''): ?><span><?= icon('clock', 'icon icon-xs') ?> <?= e($provider['phone']) ?></span><?php endif; ?>
                                    <?php if ($provider['hours'] !== ''): ?><span><?= e($provider['hours']) ?></span><?php endif; ?>
                                </div>
                                <?php if ($providerBrands !== []): ?>
                                    <div class="provider-brand">
                                        <?php foreach (array_slice($providerBrands, 0, 6) as $brand): ?><span class="tag tag-sm tag-muted"><?= e($brand) ?></span><?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="provider-actions">
                                <?php if ($provider['phone'] !== ''): ?>
                                    <a class="btn btn-outline btn-sm" href="tel:<?= e(preg_replace('/[^0-9+]/', '', (string) $provider['phone'])) ?>">Llamar</a>
                                <?php endif; ?>
                                <a class="btn btn-ghost btn-sm" href="<?= e(url('/proveedores/' . $provider['slug'])) ?>">Ver ficha</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <div class="pagination-wrap">
                    <?php \App\Core\View::partial('partials/pagination', ['paginator' => $paginator]); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
