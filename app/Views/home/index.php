<?php
/** @var array<string, mixed> $stats */
/** @var array<int, array<string, mixed>> $featuredMachines */
/** @var array<int, array<string, mixed>> $faults */
/** @var array<int, array<string, mixed>> $guides */
/** @var array<int, array<string, mixed>> $workshopCases */
/** @var array<int, array<string, mixed>> $contributors */
/** @var array<int, array<string, mixed>> $techHighlights */
/** @var array<int, array{category: string, count: int}> $implementCounts */
/** @var array<int, array{name: string, slug: string}> $brands */
$hero = $featuredMachines[0] ?? null;
$categories = category_config('machine_categories');
$implementTotal = 0;
foreach ($implementCounts as $row) {
    $implementTotal += (int) $row['count'];
}
?><section class="hero hero-agro">
    <div class="hero-pattern" aria-hidden="true"></div>
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow eyebrow-light"><span class="eyebrow-dot"></span> Base técnica para el campo mexicano</span>
            <h1>La máquina correcta, <em>reparada a tiempo</em>.</h1>
            <p class="hero-lead">
                Tractores e implementos con ficha técnica, compatibilidad calculada y precios de referencia;
                fallas diagnosticadas paso a paso, mantenimiento por horas y un directorio de software,
                datos y proveedores que sí se consiguen en México.
            </p>

            <form class="hero-search" action="<?= e(url('/buscar')) ?>" method="get" role="search">
                <div class="hero-search-field">
                    <?= icon('search', 'icon') ?>
                    <input type="search" name="q" placeholder="Ej. John Deere 5075E, hidráulico débil, SPN 100…" aria-label="Buscar máquinas, fallas, guías o recursos">
                </div>
                <select name="tipo" aria-label="Tipo de contenido">
                    <option value="">Todo el portal</option>
                    <option value="maquinas">Máquinas</option>
                    <option value="implementos">Implementos</option>
                    <option value="fallas">Fallas</option>
                    <option value="tecnologia">Tecnología</option>
                    <option value="taller">Taller</option>
                </select>
                <button class="btn btn-primary" type="submit">Buscar</button>
            </form>

            <div class="hero-tags">
                <span>Atajos:</span>
                <a href="<?= e(url('/maquinas?category=tractor&min_hp=60&max_hp=100')) ?>">Tractores 60–100 HP</a>
                <a href="<?= e(url('/fallas?system=hidraulico')) ?>">Hidráulico</a>
                <a href="<?= e(url('/mantenimiento')) ?>">Mantenimiento 500 h</a>
                <a href="<?= e(url('/tecnologia?kind=dataset')) ?>">Datos abiertos</a>
            </div>

            <dl class="hero-stats">
                <div><dt><?= (int) $stats['machines'] ?></dt><dd>Máquinas fichadas</dd></div>
                <div><dt><?= (int) $stats['implements'] ?></dt><dd>Implementos</dd></div>
                <div><dt><?= (int) $stats['faults'] ?></dt><dd>Fallas documentadas</dd></div>
                <div><dt><?= (int) $stats['guides'] + (int) $stats['tech'] ?></dt><dd>Guías y recursos</dd></div>
            </dl>
        </div>

        <div class="hero-visual">
            <?php if ($hero !== null): ?>
                <div class="hero-case">
                    <div class="hero-case-top">
                        <span class="hero-case-crop"><?= e(machine_category_emoji((string) $hero['category'])) ?> <?= e(machine_category_label((string) $hero['category'])) ?></span>
                        <span class="hero-case-folio">Ficha destacada</span>
                    </div>
                    <h2><?= e($hero['brand_name']) ?> <?= e($hero['model']) ?></h2>
                    <p class="case-deck"><?= e(excerpt($hero['summary'], 120)) ?></p>
                    <div class="hero-case-diagnosis">
                        <span>Potencia</span>
                        <strong><?= e(hp_label($hero['power_hp'])) ?></strong>
                    </div>
                    <div class="hero-case-meta">
                        <span><?= icon('cog', 'icon icon-xs') ?> <?= e($hero['traction']) ?></span>
                        <?php if (!empty($hero['lift_capacity_kg'])): ?>
                            <span><?= icon('layers', 'icon icon-xs') ?> <?= e(num($hero['lift_capacity_kg'])) ?> kg</span>
                        <?php endif; ?>
                        <?php if (!empty($hero['hydraulic_flow_lpm'])): ?>
                            <span><?= icon('droplet', 'icon icon-xs') ?> <?= e(num($hero['hydraulic_flow_lpm'], 1)) ?> L/min</span>
                        <?php endif; ?>
                    </div>
                    <div class="hero-case-footer">
                        <span class="price-tag"><?= e(price_range($hero['price_min_mxn'], $hero['price_max_mxn'])) ?></span>
                        <a class="btn btn-dark btn-sm" href="<?= e(url('/maquinas/' . $hero['slug'])) ?>">Abrir ficha</a>
                    </div>
                </div>
                <div class="hero-note">
                    <?= icon('info', 'icon icon-sm') ?>
                    <span>Precios y especificaciones de referencia. Confirma con el distribuidor antes de comprar.</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Catálogo técnico</span>
                <h2 class="section-title">¿Qué máquina necesitas revisar?</h2>
                <p class="section-sub">Fichas con potencia, levante, caudal hidráulico, transmisión, precios y notas de disponibilidad en México.</p>
            </div>
            <a class="text-link" href="<?= e(url('/maquinas')) ?>">Ver todas las máquinas <?= icon('arrow-right', 'icon icon-sm') ?></a>
        </div>

        <div class="category-strip">
            <?php foreach ($categories as $code => $category): ?>
                <a class="category-tile" href="<?= e(url('/maquinas?category=' . $code)) ?>">
                    <span class="category-emoji" aria-hidden="true"><?= e($category['emoji']) ?></span>
                    <span class="category-name"><?= e($category['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="machine-grid">
            <?php foreach ($featuredMachines as $machine): ?>
                <?php \App\Core\View::partial('partials/machine-card', ['machine' => $machine]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-muted">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Herramientas de decisión</span>
                <h2 class="section-title">Calcula antes de comprar, acoplar o reparar.</h2>
                <p class="section-sub">Modelos de ingeniería explicados, con supuestos visibles y resultados en hectáreas, horas y pesos.</p>
            </div>
        </div>

        <div class="pillar-grid">
            <a class="pillar-card" href="<?= e(url('/compatibilidad')) ?>">
                <span class="pillar-icon"><?= icon('tractor') ?></span>
                <h3>Compatibilidad tractor–implemento</h3>
                <p>Fuerza de tiro, potencia necesaria, levante, caudal y lastre para acoplar sin romper el tractor ni el implemento.</p>
                <span class="text-link">Calcular acople <?= icon('arrow-right', 'icon icon-sm') ?></span>
            </a>
            <a class="pillar-card" href="<?= e(url('/recomendador')) ?>">
                <span class="pillar-icon"><?= icon('layers') ?></span>
                <h3>Recomendador de implementos</h3>
                <p>Según cultivo, superficie, suelo y potencia: secuencia de labranza, siembra y protección con anchos realistas.</p>
                <span class="text-link">Armar mi secuencia <?= icon('arrow-right', 'icon icon-sm') ?></span>
            </a>
            <a class="pillar-card" href="<?= e(url('/mantenimiento')) ?>">
                <span class="pillar-icon"><?= icon('clipboard') ?></span>
                <h3>Plan de mantenimiento</h3>
                <p>Checklist por horas (50, 250, 500, 1000 y 2000) para tractor, cosechadora, pulverizadora y compacto.</p>
                <span class="text-link">Generar plan <?= icon('arrow-right', 'icon icon-sm') ?></span>
            </a>
            <a class="pillar-card" href="<?= e(url('/costos')) ?>">
                <span class="pillar-icon"><?= icon('calculator') ?></span>
                <h3>Costo por hora y por hectárea</h3>
                <p>Depreciación, interés, seguro, combustible, mantenimiento y mano de obra para saber cuánto cuesta trabajar.</p>
                <span class="text-link">Calcular costo <?= icon('arrow-right', 'icon icon-sm') ?></span>
            </a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="split-grid">
            <div>
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Taller</span>
                        <h2 class="section-title-sm">Fallas que se repiten en el campo</h2>
                    </div>
                    <a class="text-link" href="<?= e(url('/fallas')) ?>">Todas las fallas <?= icon('arrow-right', 'icon icon-sm') ?></a>
                </div>
                <div class="fault-list">
                    <?php foreach ($faults as $fault): ?>
                        <?php \App\Core\View::partial('partials/fault-card', ['fault' => $fault]); ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="home-aside">
                <div class="aside-card">
                    <h3><?= icon('wrench', 'icon icon-sm') ?> ¿Reparaste algo esta semana?</h3>
                    <p>Documenta síntoma, diagnóstico, refacciones y costo. Tu caso ahorra horas de prueba y error a otros técnicos.</p>
                    <a class="btn btn-primary btn-sm btn-block" href="<?= e(url('/taller/nuevo')) ?>">Publicar caso de taller</a>
                    <p class="muted" style="margin-top:8px">Requiere cuenta verificada (cédula, taller o revisión manual).</p>
                </div>

                <div class="aside-card">
                    <h3>Por sistema</h3>
                    <div class="aside-list">
                        <a href="<?= e(url('/fallas?system=motor')) ?>">Motor <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                        <a href="<?= e(url('/fallas?system=hidraulico')) ?>">Hidráulico <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                        <a href="<?= e(url('/fallas?system=electrico')) ?>">Eléctrico <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                        <a href="<?= e(url('/fallas?system=transmision')) ?>">Transmisión <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                        <a href="<?= e(url('/codigos')) ?>">Códigos SPN/FMI <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                    </div>
                </div>

                <div class="aside-card aside-card-dark">
                    <h3>Implementos por función</h3>
                    <p><?= (int) $implementTotal ?> fichas de labranza, siembra, protección, cosecha y forraje.</p>
                    <div class="tag-row">
                        <?php foreach (array_slice($implementCounts, 0, 6) as $row): ?>
                            <a class="tag tag-sm" href="<?= e(url('/implementos?category=' . $row['category'])) ?>"><?= e(implement_category_label((string) $row['category'])) ?> · <?= (int) $row['count'] ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<section class="section section-muted">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Agricultura de precisión</span>
                <h2 class="section-title">Software, hardware y datos que se pueden usar en México.</h2>
                <p class="section-sub">Desde AgOpenGPS y RTK de bajo costo hasta modelos de visión para malezas y asistentes de refacciones. Cada ficha indica licencia, requisitos y qué tan viable es en campo mexicano.</p>
            </div>
            <a class="text-link" href="<?= e(url('/tecnologia')) ?>">Explorar tecnología <?= icon('arrow-right', 'icon icon-sm') ?></a>
        </div>

        <div class="tech-grid">
            <?php foreach ($techHighlights as $resource): ?>
                <?php \App\Core\View::partial('partials/tech-card', ['resource' => $resource]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($workshopCases !== []): ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Comunidad de taller</span>
                <h2 class="section-title">Reparaciones contadas por quienes las hicieron</h2>
                <p class="section-sub">Casos con síntoma, diagnóstico, refacciones, costo y resultado. Sin promesas de taller: experiencia documentada.</p>
            </div>
            <a class="text-link" href="<?= e(url('/taller')) ?>">Todos los casos <?= icon('arrow-right', 'icon icon-sm') ?></a>
        </div>
        <div class="workshop-grid">
            <?php foreach ($workshopCases as $case): ?>
                <?php \App\Core\View::partial('partials/workshop-card', ['case' => $case]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section section-tight">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Guías editoriales</span>
                <h2 class="section-title-sm">Para decidir con criterio</h2>
            </div>
            <a class="text-link" href="<?= e(url('/guias')) ?>">Ver todas <?= icon('arrow-right', 'icon icon-sm') ?></a>
        </div>
        <div class="guide-grid">
            <?php foreach ($guides as $guide): ?>
                <a class="guide-card" href="<?= e(url('/guias/' . $guide['slug'])) ?>">
                    <span class="guide-number"><?= e(guide_category_label((string) $guide['category'])) ?></span>
                    <h3><?= e($guide['title']) ?></h3>
                    <p><?= e(excerpt($guide['summary'], 120)) ?></p>
                    <span class="guide-meta"><?= (int) $guide['reading_min'] ?> min de lectura · <?= e(level_label((string) $guide['level'])) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($contributors !== []): ?>
<section class="section section-muted">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Quiénes escriben</span>
                <h2 class="section-title-sm">Técnicos y talleres que documentan</h2>
            </div>
        </div>
        <div class="contributor-grid">
            <?php foreach ($contributors as $contributor): ?>
                <a class="contributor-card" href="<?= e(url('/perfil-publico/' . $contributor['id'])) ?>">
                    <?= avatar_tag((string) $contributor['name'], $contributor['avatar_url'] ?? null, 'avatar-lg') ?>
                    <span>
                        <strong><?= e($contributor['name']) ?></strong>
                        <small><?= e($contributor['workshop_name'] ?: role_label((string) $contributor['role'])) ?><?= $contributor['state_name'] ? ' · ' . e($contributor['state_name']) : '' ?></small>
                    </span>
                    <span class="contributor-count"><?= (int) $contributor['cases_count'] ?> casos</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container">
        <div class="callout-band">
            <div class="callout-band-copy">
                <h2>¿Tienes un manual, una falla o un dato que falta?</h2>
                <p>Este portal se construye con evidencia de campo. Si detectas una especificación incorrecta, un precio desactualizado o una falla que no está documentada, escríbenos con la fuente: la revisamos y la publicamos con crédito.</p>
                <a class="btn btn-sand" href="<?= e(url('/contacto')) ?>">Enviar una corrección <?= icon('arrow-right', 'icon icon-sm') ?></a>
            </div>
            <div class="callout-band-note">
                <?= icon('shield-check', 'icon icon-lg') ?>
                <p>Contenido educativo. Verifica siempre el manual del fabricante, la normativa aplicable (NOM, STPS) y las condiciones reales del lote.</p>
            </div>
        </div>
    </div>
</section>
