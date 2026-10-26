<?php
/** @var array<int, array{label: string, value: string, ok: bool}> $checks */
/** @var bool $ok */
/** @var array<string, int> $counts */
/** @var int $migrations */
?><section class="section">
    <div class="container">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Estado técnico</span>
        <h1><?= $ok ? 'El sistema está operativo' : 'Hay comprobaciones pendientes' ?></h1>
        <p class="page-hero-lead">Diagnóstico de PHP, extensiones, base de datos y contenido. Sin datos sensibles.</p>

        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Comprobación</th><th>Valor</th><th>Estado</th></tr></thead>
                <tbody>
                <?php foreach ($checks as $check): ?>
                    <tr>
                        <td><?= e($check['label']) ?></td>
                        <td><code><?= e($check['value']) ?></code></td>
                        <td><span class="badge badge-<?= $check['ok'] ? 'success' : 'danger' ?>"><?= $check['ok'] ? 'OK' : 'Falla' ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <tr><td>Migraciones aplicadas</td><td><?= (int) $migrations ?></td><td><span class="badge badge-<?= $migrations > 0 ? 'success' : 'warning' ?>"><?= $migrations > 0 ? 'OK' : 'Pendiente' ?></span></td></tr>
                </tbody>
            </table>
        </div>

        <?php if ($counts !== []): ?>
            <h2 class="section-title-sm">Contenido en base</h2>
            <div class="kpi-row">
                <?php foreach ($counts as $table => $count): ?>
                    <div class="kpi"><strong><?= (int) $count ?></strong><span><?= e($table) ?></span></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
