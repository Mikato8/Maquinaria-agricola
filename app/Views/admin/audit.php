<?php
/** @var array<int, array<string, mixed>> $entries */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Moderación</span>
        <h1>Auditoría</h1>
        <p class="page-hero-lead">Registro de acciones editoriales, verificaciones y cambios de cuenta.</p>
        <?php \App\Core\View::partial('partials/admin-nav'); ?>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <?php if ($entries === []): ?>
            <div class="empty-state"><span class="empty-icon"><?= icon('shield') ?></span><h2>Sin registros</h2><p>Las acciones aparecerán aquí.</p></div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="data-table">
                    <thead><tr><th>Fecha</th><th>Actor</th><th>Acción</th><th>Objeto</th><th>Detalle</th><th>IP</th></tr></thead>
                    <tbody>
                    <?php foreach ($entries as $entry): ?>
                        <tr>
                            <td><?= e(format_date((string) $entry['created_at'], 'd/m/Y H:i')) ?></td>
                            <td><?= e($entry['actor_name'] ?: 'Sistema') ?></td>
                            <td><code><?= e($entry['action']) ?></code></td>
                            <td><?= e($entry['target_type']) ?><?= $entry['target_id'] ? ' #' . (int) $entry['target_id'] : '' ?></td>
                            <td><small><?= e(excerpt((string) $entry['detail'], 120)) ?></small></td>
                            <td><small class="muted"><?= e($entry['ip']) ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
