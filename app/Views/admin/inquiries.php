<?php
/** @var array<int, array<string, mixed>> $inquiries */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Moderación</span>
        <h1>Mensajes de contacto</h1>
        <p class="page-hero-lead">Correcciones, altas de proveedores, reportes de fallas y consultas del formulario público.</p>
        <?php \App\Core\View::partial('partials/admin-nav'); ?>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <?php if ($inquiries === []): ?>
            <div class="empty-state"><span class="empty-icon"><?= icon('mail') ?></span><h2>Bandeja vacía</h2><p>No hay mensajes recibidos.</p></div>
        <?php else: ?>
            <div class="admin-list">
                <?php foreach ($inquiries as $inquiry): ?>
                    <article class="admin-card">
                        <div class="admin-card-head">
                            <h2><?= e($inquiry['subject'] ?: 'Sin asunto') ?></h2>
                            <span class="badge badge-<?= $inquiry['handled'] ? 'success' : 'warning' ?>"><?= e($inquiry['handled'] ? 'Atendido' : 'Pendiente') ?></span>
                        </div>
                        <p class="muted"><?= e($inquiry['name']) ?> · <a href="mailto:<?= e($inquiry['email']) ?>"><?= e($inquiry['email']) ?></a><?= $inquiry['phone'] ? ' · ' . e($inquiry['phone']) : '' ?> · <?= e($inquiry['kind']) ?> · <?= e(time_ago((string) $inquiry['created_at'])) ?></p>
                        <p><?= nl2br(e((string) $inquiry['message'])) ?></p>
                        <form method="post" action="<?= e(url('/admin/mensajes/' . $inquiry['id'] . '/accion')) ?>">
                            <?= csrf_field() ?>
                            <button class="btn btn-ghost btn-sm" type="submit" name="action" value="<?= $inquiry['handled'] ? 'reabrir' : 'atender' ?>"><?= $inquiry['handled'] ? 'Marcar pendiente' : 'Marcar atendido' ?></button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
