<?php
/** @var array<string, int> $stats */
/** @var array<int, array<string, mixed>> $pending */
/** @var array<int, array<string, mixed>> $verification */
/** @var array<int, array<string, mixed>> $comments */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <span class="eyebrow eyebrow-light"><span class="eyebrow-dot"></span> Panel editorial</span>
        <h1>Resumen de operación</h1>
        <p class="page-hero-lead">Casos por revisar, cuentas pendientes de verificación y salud del contenido.</p>
        <?php \App\Core\View::partial('partials/admin-nav'); ?>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="admin-stat-grid">
            <div class="admin-stat"><span>Casos publicados</span><strong><?= (int) $stats['casos'] ?></strong><a href="<?= e(url('/admin/casos?status=publicado')) ?>">Ver</a></div>
            <div class="admin-stat admin-stat-warning"><span>En revisión</span><strong><?= (int) $stats['revision'] ?></strong><a href="<?= e(url('/admin/casos')) ?>">Revisar</a></div>
            <div class="admin-stat admin-stat-warning"><span>Cuentas pendientes</span><strong><?= (int) $stats['pendientes'] ?></strong><a href="<?= e(url('/admin/usuarios?status=pendiente')) ?>">Verificar</a></div>
            <div class="admin-stat"><span>Mensajes sin atender</span><strong><?= (int) $stats['mensajes'] ?></strong><a href="<?= e(url('/admin/mensajes')) ?>">Abrir</a></div>
            <div class="admin-stat"><span>Comentarios</span><strong><?= (int) $stats['comentarios'] ?></strong><a href="<?= e(url('/admin/comentarios')) ?>">Moderar</a></div>
            <div class="admin-stat"><span>Usuarios</span><strong><?= (int) $stats['usuarios'] ?></strong><a href="<?= e(url('/admin/usuarios')) ?>">Gestionar</a></div>
        </div>

        <div class="admin-grid-2">
            <div class="admin-card">
                <div class="admin-card-head">
                    <h2>Casos por revisar</h2>
                    <a class="text-link" href="<?= e(url('/admin/casos')) ?>">Todos</a>
                </div>
                <?php if ($pending === []): ?>
                    <p class="muted">No hay casos en cola. Buen trabajo.</p>
                <?php else: ?>
                    <div class="admin-list">
                        <?php foreach ($pending as $case): ?>
                            <a class="admin-list-row" href="<?= e(url('/admin/revision/' . $case['id'])) ?>">
                                <span class="admin-list-main">
                                    <strong><?= e($case['title']) ?></strong>
                                    <small><?= e($case['author_name']) ?> · <?= e(machine_category_singular((string) $case['machine_category'])) ?></small>
                                </span>
                                <span class="admin-list-time"><?= e(time_ago((string) $case['updated_at'])) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="admin-card">
                <div class="admin-card-head">
                    <h2>Verificaciones pendientes</h2>
                    <a class="text-link" href="<?= e(url('/admin/usuarios?status=pendiente')) ?>">Todas</a>
                </div>
                <?php if ($verification === []): ?>
                    <p class="muted">Sin cuentas pendientes.</p>
                <?php else: ?>
                    <div class="admin-list">
                        <?php foreach ($verification as $user): ?>
                            <a class="admin-list-row" href="<?= e(url('/admin/usuarios/' . $user['id'])) ?>">
                                <span class="admin-list-main">
                                    <strong><?= e($user['name']) ?></strong>
                                    <small><?= e(role_label((string) $user['role'])) ?> · <?= e($user['state_name'] ?? 'México') ?></small>
                                </span>
                                <span class="admin-list-time"><?= e(time_ago((string) $user['created_at'])) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-head">
                <h2>Últimos comentarios</h2>
                <a class="text-link" href="<?= e(url('/admin/comentarios')) ?>">Moderar</a>
            </div>
            <?php if ($comments === []): ?>
                <p class="muted">Sin comentarios.</p>
            <?php else: ?>
                <div class="admin-comment-row">
                    <?php foreach ($comments as $comment): ?>
                        <div>
                            <strong><?= e($comment['author_name']) ?></strong> en <a href="<?= e(url('/taller/' . ($comment['case_slug'] ?? ''))) ?>"><?= e($comment['case_title'] ?? 'caso') ?></a>
                            <p><?= e(excerpt($comment['body'], 140)) ?></p>
                        </div>
                        <span class="admin-list-time"><?= e(time_ago((string) $comment['created_at'])) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
