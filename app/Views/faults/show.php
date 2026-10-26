<?php
/** @var array<string, mixed> $fault */
/** @var array<int, array<string, mixed>> $causes */
/** @var array<int, mixed> $steps */
/** @var array<int, string> $tools */
/** @var array<int, string> $parts */
/** @var array<int, array<string, mixed>> $related */
$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'HowTo',
    'name' => $fault['title'],
    'description' => excerpt($fault['symptom'], 250),
    'totalTime' => $fault['time_hours'] !== null ? 'PT' . (int) round((float) $fault['time_hours']) . 'H' : null,
    'tool' => array_map(static fn (string $tool): array => ['@type' => 'HowToTool', 'name' => $tool], $tools),
    'step' => array_map(static fn ($step): array => [
        '@type' => 'HowToStep',
        'text' => is_array($step) ? (string) ($step['instruction'] ?? '') : (string) $step,
    ], $steps),
];
?><script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>

<section class="detail-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <a href="<?= e(url('/fallas')) ?>">Fallas</a>
            <a href="<?= e(url('/fallas?system=' . $fault['system'])) ?>"><?= e(fault_system_label((string) $fault['system'])) ?></a>
            <span aria-current="page"><?= e($fault['title']) ?></span>
        </nav>

        <div class="detail-hero-grid">
            <div>
                <span class="detail-kicker">
                    <span class="badge badge-<?= e(severity_tone($fault['severity'])) ?>"><?= e(severity_label($fault['severity'])) ?></span>
                    <?= e(machine_category_singular($fault['machine_category'])) ?> · <?= e(fault_system_label((string) $fault['system'])) ?>
                </span>
                <h1><?= e($fault['title']) ?></h1>
                <p class="detail-lead"><strong>Síntoma:</strong> <?= e($fault['symptom']) ?></p>

                <dl class="detail-facts">
                    <div class="detail-fact"><dt>Dificultad</dt><dd><?= e(difficulty_label($fault['difficulty'] !== null ? (int) $fault['difficulty'] : null)) ?></dd></div>
                    <div class="detail-fact"><dt>Tiempo estimado</dt><dd><?= e(hours_label($fault['time_hours'])) ?></dd></div>
                    <div class="detail-fact"><dt>Costo de refacción</dt><dd><?= $fault['cost_min_mxn'] !== null ? e(compact_money($fault['cost_min_mxn'])) . ' – ' . e(compact_money($fault['cost_max_mxn'])) : 'Variable' ?></dd></div>
                    <div class="detail-fact"><dt>Frecuencia</dt><dd><?= e(frequency_label($fault['frequency'])) ?></dd></div>
                </dl>

                <div class="detail-actions">
                    <a class="btn btn-primary" href="<?= e(url('/taller/nuevo?falla=' . $fault['slug'])) ?>"><?= icon('wrench', 'icon icon-sm') ?> Documentar mi reparación</a>
                    <a class="btn btn-outline" href="<?= e(url('/codigos')) ?>"><?= icon('cpu', 'icon icon-sm') ?> Códigos de falla</a>
                    <button class="btn btn-ghost" type="button" data-print><?= icon('printer', 'icon icon-sm') ?> Imprimir</button>
                </div>
            </div>

            <div class="aside-card fault-summary">
                <h3>Ficha rápida</h3>
                <div class="aside-list">
                    <span><?= icon('cog', 'icon icon-xs') ?> Sistema <strong><?= e(fault_system_label((string) $fault['system'])) ?></strong></span>
                    <span><?= icon('gauge', 'icon icon-xs') ?> Dificultad <strong><?= e(difficulty_short($fault['difficulty'] !== null ? (int) $fault['difficulty'] : null)) ?></strong></span>
                    <span><?= icon('clock', 'icon icon-xs') ?> Tiempo <strong><?= e(hours_label($fault['time_hours'])) ?></strong></span>
                    <span><?= icon('tool', 'icon icon-xs') ?> Herramientas <strong><?= count($tools) ?: 'Básicas' ?></strong></span>
                </div>
                <?php if ($fault['when_to_stop'] !== ''): ?>
                    <div class="notice notice-danger">
                        <?= icon('alert-triangle', 'icon icon-sm') ?>
                        <p><?= e($fault['when_to_stop']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<div class="container detail-layout">
    <div class="detail-main">
        <section class="detail-section">
            <h2>Causas probables, de la más a la menos frecuente</h2>
            <p>No cambies piezas por suposición. Empieza por la causa marcada como más probable y confirma con la prueba indicada antes de desarmar.</p>
            <div class="cause-list">
                <?php foreach ($causes as $cause): ?>
                    <article class="cause-card">
                        <div class="cause-head">
                            <strong><?= e($cause['cause'] ?? 'Causa') ?></strong>
                            <span class="badge badge-<?= e(likelihood_tone($cause['likelihood'] ?? 'media')) ?>"><?= e(likelihood_label($cause['likelihood'] ?? 'media')) ?></span>
                        </div>
                        <div class="cause-grid">
                            <?php if (!empty($cause['check'])): ?>
                                <div>
                                    <h4>Prueba de diagnóstico</h4>
                                    <p><?= e($cause['check']) ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($cause['fix'])): ?>
                                <div>
                                    <h4>Corrección</h4>
                                    <p><?= e($cause['fix']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if ($steps !== []): ?>
            <section class="detail-section">
                <h2>Procedimiento paso a paso</h2>
                <div class="step-list">
                    <?php foreach ($steps as $step): ?>
                        <div class="step-item">
                            <p><?= e(is_array($step) ? (string) ($step['instruction'] ?? json_encode($step, JSON_UNESCAPED_UNICODE)) : (string) $step) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="detail-section two-col">
            <div>
                <h2>Herramientas necesarias</h2>
                <?php if ($tools === []): ?>
                    <p>Herramienta básica de taller: juego de dados y llaves, desarmadores, multímetro y lámpara.</p>
                <?php else: ?>
                    <ul class="check-list">
                        <?php foreach ($tools as $tool): ?>
                            <li><?= icon('tool', 'icon icon-sm') ?> <span><?= e($tool) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div>
                <h2>Refacciones típicas</h2>
                <?php if ($parts === []): ?>
                    <p>Consulta el despiece del modelo; las refacciones cambian entre series y años.</p>
                <?php else: ?>
                    <ul class="check-list">
                        <?php foreach ($parts as $part): ?>
                            <li><?= icon('cog', 'icon icon-sm') ?> <span><?= e($part) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>

        <section class="detail-section two-col">
            <div>
                <h2>Seguridad</h2>
                <div class="callout callout-danger">
                    <?= icon('hard-hat', 'icon icon-sm') ?>
                    <p><?= e($fault['safety'] ?: 'Desconecta la batería, despresuriza el sistema hidráulico y bloquea la máquina antes de trabajar. Usa lentes y guantes.') ?></p>
                </div>
            </div>
            <div>
                <h2>Prevención</h2>
                <div class="callout">
                    <?= icon('shield-check', 'icon icon-sm') ?>
                    <p><?= e($fault['prevention'] ?: 'Respeta los intervalos de mantenimiento y usa filtros y aceites con la especificación del fabricante.') ?></p>
                </div>
            </div>
        </section>

        <?php if ($fault['references_text'] !== ''): ?>
            <section class="detail-section">
                <h2>Referencias</h2>
                <p class="muted"><?= nl2br(e((string) $fault['references_text'])) ?></p>
            </section>
        <?php endif; ?>
    </div>

    <aside class="detail-aside">
        <div class="aside-card">
            <h3>¿Te sirvió?</h3>
            <p>Guarda esta falla en tu panel o compártela con tu taller. Si tienes una corrección, escríbenos con la fuente.</p>
            <?php if (auth() !== null): ?>
                <form method="post" action="<?= e(url('/guardar/falla/' . $fault['id'])) ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline btn-sm btn-block" type="submit"><?= icon('bookmark', 'icon icon-sm') ?> Guardar en mi panel</button>
                </form>
            <?php else: ?>
                <a class="btn btn-outline btn-sm btn-block" href="<?= e(url('/acceder')) ?>">Entrar para guardar</a>
            <?php endif; ?>
        </div>

        <?php if ($related !== []): ?>
            <div class="aside-card">
                <h3>Otras fallas del sistema</h3>
                <div class="aside-list">
                    <?php foreach ($related as $item): ?>
                        <a href="<?= e(url('/fallas/' . $item['slug'])) ?>">
                            <span><?= e($item['title']) ?><br><small class="muted"><?= e(severity_label((string) $item['severity'])) ?> · <?= e(difficulty_short((int) $item['difficulty'])) ?></small></span>
                            <?= icon('arrow-right', 'icon icon-xs') ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="aside-card">
            <h3>Antes de desarmar</h3>
            <div class="aside-list">
                <a href="<?= e(url('/mantenimiento')) ?>">Plan de mantenimiento <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/codigos')) ?>">Leer códigos SPN/FMI <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/proveedores?type=refacciones')) ?>">Buscar refacciones <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/compatibilidad')) ?>">Compatibilidad de implemento <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
            </div>
        </div>
    </aside>
</div>
