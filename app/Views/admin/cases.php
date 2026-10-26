<?php
/** @var array<int, array<string, mixed>> $cases */
/** @var string $status */
/** @var array<string, int> $counts */
$tabs = [
    'en_revision' => 'En revisión',
    'cambios_solicitados' => 'Cambios solicitados',
    'publicado' => 'Publicados',
    'rechazado' => 'Rechazados',
    'borrador' => 'Borradores',
];
?><section class="page-hero page-hero-compact">
    <div class="container">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Moderación</span>
        <h1>Casos de taller</h1>
        <p class="page-hero-lead">Revisa coherencia técnica, datos verificables, privacidad y riesgos antes de publicar.</p>
        <?php \App\Core\View::partial('partials/admin-nav'); ?>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="tag-row category-tabs">
            <?php foreach ($tabs as $code => $label): ?>
                <a class="tag tag-sm <?= $status === $code ? 'is-active' : '' ?>" href="<?= e(url('/admin/casos?status=' . $code)) ?>"><?= e($label) ?> · <?= (int) ($counts[$code] ?? 0) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if ($cases === []): ?>
            <div class="empty-state">
                <span class="empty-icon"><?= icon('check-circle') ?></span>
                <h2>Nada en esta bandeja</h2>
                <p>No hay casos con ese estado.</p>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                    <tr><th>Caso</th><th>Autor</th><th>Máquina</th><th>Estado</th><th>Actualizado</th><th></th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cases as $case): ?>
                        <tr>
                            <td><strong><?= e($case['title']) ?></strong><p class="table-muted"><?= e($case['folio']) ?></p></td>
                            <td><?= e($case['author_name']) ?></td>
                            <td><?= e($case['machine_label'] ?: machine_category_singular((string) $case['machine_category'])) ?></td>
                            <td><span class="badge badge-<?= e(workshop_status_tone((string) $case['status'])) ?>"><?= e(status_label((string) $case['status'])) ?></span></td>
                            <td><?= e(time_ago((string) $case['updated_at'])) ?></td>
                            <td class="table-actions"><a class="text-link" href="<?= e(url('/admin/revision/' . $case['id'])) ?>">Revisar</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
