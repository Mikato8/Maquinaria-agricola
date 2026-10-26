<?php
/** @var array<int, array{interval_hours: int, interval_label: string, tasks: array<int, array<string, mixed>>}> $plan */
/** @var string $category */
/** @var string $categoryLabel */
/** @var array<string, string> $categories */
/** @var array<int, array{system: string, total: int}> $systems */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Mantenimiento</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Plan por horas</span>
        <h1>Mantenimiento preventivo: lo barato es revisar, no reparar.</h1>
        <p class="page-hero-lead">
            Checklist por intervalos de 50, 250, 500, 1000 y 2000 horas para <?= e(mb_strtolower($categoryLabel)) ?>.
            Marca lo que ya hiciste, imprime la hoja y fírmala en el taller.
        </p>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="tag-row category-tabs no-print">
            <?php foreach ($categories as $code => $label): ?>
                <a class="tag tag-sm <?= $category === $code ? 'is-active' : '' ?>" href="<?= e(url('/mantenimiento?categoria=' . $code)) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="maintenance-toolbar">
            <div class="maintenance-meta">
                <strong><?= count($plan) ?></strong> intervalos · <strong><?= array_sum(array_map(static fn (array $group): int => count($group['tasks']), $plan)) ?></strong> tareas · <?= count($systems) ?> sistemas
            </div>
            <button class="btn btn-outline btn-sm no-print" type="button" data-print><?= icon('printer', 'icon icon-sm') ?> Imprimir checklist</button>
        </div>

        <?php if ($plan === []): ?>
            <div class="empty-state">
                <span class="empty-icon"><?= icon('clipboard') ?></span>
                <h2>Este plan está en preparación</h2>
                <p>Estamos documentando los intervalos para esta categoría. Mientras tanto, revisa el plan del tractor agrícola.</p>
                <a class="btn btn-primary" href="<?= e(url('/mantenimiento?categoria=tractor')) ?>">Ver plan de tractor</a>
            </div>
        <?php else: ?>
            <div class="maintenance-plan">
                <?php foreach ($plan as $group): ?>
                    <section class="interval-block">
                        <div class="interval-head">
                            <h2><?= e($group['interval_label'] ?: $group['interval_hours'] . ' horas') ?></h2>
                            <span><?= $group['interval_hours'] ?> h · <?= count($group['tasks']) ?> tareas</span>
                        </div>
                        <ul class="task-list">
                            <?php foreach ($group['tasks'] as $task): ?>
                                <?php $parts = pg_array($task['parts'] ?? []); ?>
                                <li class="task-item">
                                    <label class="task-check">
                                        <input type="checkbox" class="no-print">
                                        <span class="task-system"><?= e(fault_system_label((string) $task['system'])) ?></span>
                                    </label>
                                    <div class="task-body">
                                        <strong><?= e($task['task']) ?></strong>
                                        <?php if ((string) $task['detail'] !== ''): ?><p><?= e($task['detail']) ?></p><?php endif; ?>
                                        <?php if ($parts !== []): ?>
                                            <p class="task-parts"><?= icon('cog', 'icon icon-xs') ?> <?= e(implode(' · ', $parts)) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="maintenance-notes">
            <div class="notice notice-info">
                <?= icon('info', 'icon icon-sm') ?>
                <p>Los intervalos son una guía para condiciones normales de trabajo. En polvo, lodo, pendiente o jornadas largas, acorta los cambios de aceite y filtros hasta en 30 %. El manual del fabricante y el análisis de aceite mandan.</p>
            </div>
            <div class="notice notice-warning">
                <?= icon('alert-triangle', 'icon icon-sm') ?>
                <p>Antes de cualquier revisión: motor apagado, llave fuera, implemento en el suelo, freno de estacionamiento puesto, calzos y sistema hidráulico despresurizado.</p>
            </div>
        </div>

        <div class="cta-actions no-print">
            <a class="btn btn-primary" href="<?= e(url('/fallas')) ?>">Ver fallas y diagnóstico <?= icon('arrow-right', 'icon icon-sm') ?></a>
            <a class="btn btn-outline" href="<?= e(url('/costos')) ?>">Calcular costo de mantenimiento</a>
        </div>
    </div>
</section>
