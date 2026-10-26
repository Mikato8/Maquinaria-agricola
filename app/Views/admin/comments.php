<?php
/** @var array<int, array<string, mixed>> $comments */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Moderación</span>
        <h1>Comentarios</h1>
        <p class="page-hero-lead">Oculta comentarios con datos personales, publicidad o desinformación técnica.</p>
        <?php \App\Core\View::partial('partials/admin-nav'); ?>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <?php if ($comments === []): ?>
            <div class="empty-state"><span class="empty-icon"><?= icon('message-circle') ?></span><h2>Sin comentarios</h2><p>Todavía no hay comentarios en los casos.</p></div>
        <?php else: ?>
            <div class="admin-list">
                <?php foreach ($comments as $comment): ?>
                    <div class="admin-comment-row">
                        <div>
                            <strong><?= e($comment['author_name']) ?></strong>
                            <span class="badge badge-<?= $comment['status'] === 'visible' ? 'success' : 'neutral' ?>"><?= e($comment['status'] === 'visible' ? 'Visible' : 'Oculto') ?></span>
                            <p><?= nl2br(e((string) $comment['body'])) ?></p>
                            <small class="muted">En <a href="<?= e(url('/taller/' . ($comment['case_slug'] ?? ''))) ?>"><?= e($comment['case_title'] ?? 'caso') ?></a> · <?= e(time_ago((string) $comment['created_at'])) ?></small>
                        </div>
                        <?php if ($comment['status'] === 'visible'): ?>
                            <form method="post" action="<?= e(url('/admin/comentarios/' . $comment['id'] . '/accion')) ?>">
                                <?= csrf_field() ?>
                                <button class="btn btn-ghost btn-sm" type="submit" name="action" value="ocultar">Ocultar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
