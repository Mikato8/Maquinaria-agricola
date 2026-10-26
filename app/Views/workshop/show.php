<?php
/** @var array<string, mixed> $case */
/** @var array<int, array<string, mixed>> $images */
/** @var array<int, array<string, mixed>> $comments */
/** @var bool $hasReacted */
/** @var bool $isBookmarked */
$user = auth();
$isAuthor = $user !== null && (int) $user['id'] === (int) $case['author_id'];
?><section class="detail-hero">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <a href="<?= e(url('/taller')) ?>">Taller</a>
            <a href="<?= e(url('/taller?category=' . $case['machine_category'])) ?>"><?= e(machine_category_label((string) $case['machine_category'])) ?></a>
            <span aria-current="page"><?= e($case['folio']) ?></span>
        </nav>

        <div class="detail-hero-grid">
            <div>
                <span class="detail-kicker">
                    <span class="badge badge-<?= e(outcome_tone($case['outcome'])) ?>"><?= e(outcome_label($case['outcome'])) ?></span>
                    <?= e(machine_category_singular($case['machine_category'])) ?><?= $case['machine_label'] ? ' · ' . e($case['machine_label']) : '' ?>
                </span>
                <h1><?= e($case['title']) ?></h1>
                <p class="detail-lead"><?= e($case['summary'] ?: excerpt($case['symptom'], 200)) ?></p>

                <div class="byline-meta">
                    <span><?= avatar_tag((string) $case['author_name'], $case['author_avatar'] ?? null, 'avatar-xs') ?> <?= e($case['author_name']) ?><?= $case['author_workshop'] ? ' · ' . e($case['author_workshop']) : '' ?></span>
                    <span><?= icon('map-pin', 'icon icon-xs') ?> <?= e(location_label(null, $case['state_code'])) ?></span>
                    <span><?= icon('calendar', 'icon icon-xs') ?> <?= e(format_date($case['published_at'] ?? $case['created_at'])) ?></span>
                    <span><?= icon('clock', 'icon icon-xs') ?> <?= e(hours_label($case['time_hours'])) ?> de trabajo</span>
                </div>

                <div class="detail-actions">
                    <form method="post" action="<?= e(url('/taller/' . $case['slug'] . '/util')) ?>" class="inline-form">
                        <?= csrf_field() ?>
                        <button class="btn <?= $hasReacted ? 'btn-primary' : 'btn-outline' ?>" type="submit">
                            <?= icon('heart-pulse', 'icon icon-sm') ?> <?= $hasReacted ? 'Útil para mí' : 'Me resultó útil' ?> · <?= (int) $case['reactions_count'] ?>
                        </button>
                    </form>
                    <?php if ($user !== null): ?>
                        <form method="post" action="<?= e(url('/guardar/taller/' . $case['id'])) ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button class="btn btn-ghost" type="submit"><?= icon('bookmark', 'icon icon-sm') ?> <?= $isBookmarked ? 'Guardado' : 'Guardar' ?></button>
                        </form>
                    <?php endif; ?>
                    <a class="btn btn-ghost" href="<?= e(url('/taller/' . $case['slug'] . '/pdf')) ?>" target="_blank" rel="noopener"><?= icon('download', 'icon icon-sm') ?> PDF</a>
                    <?php if ($isAuthor): ?>
                        <a class="btn btn-ghost" href="<?= e(url('/taller/' . $case['slug'] . '/editar')) ?>"><?= icon('edit', 'icon icon-sm') ?> Editar</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="aside-card">
                <h3>Ficha del caso</h3>
                <div class="aside-list">
                    <span>Folio <strong><?= e($case['folio']) ?></strong></span>
                    <span><?= icon('gauge', 'icon icon-xs') ?> Dificultad <strong><?= e(difficulty_short((int) $case['difficulty'])) ?></strong></span>
                    <span><?= icon('clock', 'icon icon-xs') ?> Tiempo <strong><?= e(hours_label($case['time_hours'])) ?></strong></span>
                    <span><?= icon('tool', 'icon icon-xs') ?> Refacciones <strong><?= $case['cost_mxn'] !== null ? e(compact_money($case['cost_mxn'])) : 'No declarado' ?></strong></span>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container detail-layout">
    <div class="detail-main">
        <section class="detail-section">
            <h2>Síntoma</h2>
            <p><?= nl2br(e((string) $case['symptom'])) ?></p>
        </section>

        <section class="detail-section">
            <h2>Diagnóstico</h2>
            <p><?= nl2br(e((string) $case['diagnosis'])) ?></p>
        </section>

        <section class="detail-section">
            <h2>Reparación</h2>
            <p><?= nl2br(e((string) $case['repair'])) ?></p>
            <?php if ((string) $case['parts'] !== ''): ?>
                <h3>Refacciones utilizadas</h3>
                <p><?= nl2br(e((string) $case['parts'])) ?></p>
            <?php endif; ?>
            <div class="metrics-grid">
                <div class="metric"><span>Costo de refacciones</span><strong><?= $case['cost_mxn'] !== null ? e(compact_money($case['cost_mxn'])) : '—' ?></strong><small>MXN, sin mano de obra</small></div>
                <div class="metric"><span>Tiempo</span><strong><?= e(hours_label($case['time_hours'])) ?></strong><small>de trabajo efectivo</small></div>
                <div class="metric"><span>Dificultad</span><strong><?= e(difficulty_short((int) $case['difficulty'])) ?></strong><small>1 a 5</small></div>
                <div class="metric"><span>Resultado</span><strong><?= e(outcome_label((string) $case['outcome'])) ?></strong><small>reportado por el autor</small></div>
            </div>
        </section>

        <?php if ((string) $case['safety'] !== ''): ?>
            <section class="detail-section">
                <h2>Seguridad</h2>
                <div class="callout callout-danger">
                    <?= icon('hard-hat', 'icon icon-sm') ?>
                    <p><?= nl2br(e((string) $case['safety'])) ?></p>
                </div>
            </section>
        <?php endif; ?>

        <?php if ((string) $case['tips'] !== ''): ?>
            <section class="detail-section">
                <h2>Consejos del autor</h2>
                <p><?= nl2br(e((string) $case['tips'])) ?></p>
            </section>
        <?php endif; ?>

        <?php if ($images !== []): ?>
            <section class="detail-section">
                <h2>Imágenes</h2>
                <div class="case-gallery">
                    <?php foreach ($images as $image): ?>
                        <figure>
                            <img src="<?= e($image['url']) ?>" alt="<?= e($image['caption'] ?: 'Imagen del caso de taller') ?>" loading="lazy">
                            <?php if ((string) $image['caption'] !== ''): ?><figcaption><?= e($image['caption']) ?></figcaption><?php endif; ?>
                        </figure>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="detail-section" id="comentarios">
            <h2>Comentarios (<?= count($comments) ?>)</h2>
            <?php if ($comments === []): ?>
                <p class="muted">Todavía no hay comentarios. Si este caso te sirvió, cuenta cómo lo aplicaste.</p>
            <?php else: ?>
                <div class="comment-list">
                    <?php foreach ($comments as $comment): ?>
                        <article class="comment">
                            <div class="comment-head">
                                <?= avatar_tag((string) $comment['author_name'], $comment['author_avatar'] ?? null, 'avatar-sm') ?>
                                <div>
                                    <strong><?= e($comment['author_name']) ?></strong>
                                    <span class="muted"><?= e($comment['author_workshop'] ?: role_label((string) $comment['author_role'])) ?> · <?= e(time_ago((string) $comment['created_at'])) ?></span>
                                </div>
                            </div>
                            <p><?= nl2br(e((string) $comment['body'])) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($user !== null && $user['status'] === 'verificado'): ?>
                <form class="comment-form" method="post" action="<?= e(url('/taller/' . $case['slug'] . '/comentarios')) ?>">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label for="body">Aporta algo concreto</label>
                        <textarea id="body" name="body" rows="4" minlength="10" maxlength="2000" required placeholder="Ej. En mi 6110J el mismo síntoma era el sensor de presión del riel; lo confirmé con manómetro antes de cambiarlo."></textarea>
                        <span class="field-help">Máximo 2000 caracteres. Sin datos personales de terceros ni publicidad.</span>
                    </div>
                    <button class="btn btn-primary" type="submit">Publicar comentario</button>
                </form>
            <?php elseif ($user === null): ?>
                <p class="muted"><a href="<?= e(url('/acceder')) ?>">Inicia sesión</a> para comentar.</p>
            <?php else: ?>
                <p class="muted">Tu cuenta está por verificar: podrás comentar cuando el comité revise tu cédula o registro de taller.</p>
            <?php endif; ?>
        </section>
    </div>

    <aside class="detail-aside">
        <div class="aside-card">
            <h3>Sobre el autor</h3>
            <div class="author-card">
                <?= avatar_tag((string) $case['author_name'], $case['author_avatar'] ?? null, 'avatar-lg') ?>
                <div>
                    <strong><?= e($case['author_name']) ?></strong>
                    <p><?= e($case['author_headline'] ?: role_label((string) $case['author_role'])) ?></p>
                    <?php if ((string) $case['author_workshop'] !== ''): ?><p class="muted"><?= e($case['author_workshop']) ?></p><?php endif; ?>
                </div>
            </div>
            <?php if ((string) $case['author_bio'] !== ''): ?><p><?= e(excerpt($case['author_bio'], 160)) ?></p><?php endif; ?>
            <a class="text-link" href="<?= e(url('/perfil-publico/' . $case['author_id'])) ?>">Ver perfil público</a>
        </div>

        <div class="aside-card">
            <h3>Compartir</h3>
            <div class="share-grid">
                <a class="share-link" href="https://wa.me/?text=<?= rawurlencode($case['title'] . ' ' . url('/taller/' . $case['slug'])) ?>" target="_blank" rel="noopener">WhatsApp</a>
                <a class="share-link" href="mailto:?subject=<?= rawurlencode($case['title']) ?>&body=<?= rawurlencode(url('/taller/' . $case['slug'])) ?>">Correo</a>
                <button class="share-link" type="button" data-copy="<?= e(url('/taller/' . $case['slug'])) ?>">Copiar enlace</button>
            </div>
        </div>

        <div class="aside-card">
            <h3>Fallas relacionadas</h3>
            <div class="aside-list">
                <a href="<?= e(url('/fallas?category=' . $case['machine_category'])) ?>">Fallas de <?= e(mb_strtolower(machine_category_singular($case['machine_category']))) ?> <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/mantenimiento')) ?>">Mantenimiento preventivo <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                <a href="<?= e(url('/proveedores?type=refacciones')) ?>">Refaccionarias <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
            </div>
        </div>

        <div class="callout warning-note">
            <?= icon('alert-triangle', 'icon icon-sm') ?>
            <p>Los casos son experiencia de campo, no instrucciones oficiales. Verifica torques, presiones y procedimientos con el manual del fabricante.</p>
        </div>
    </aside>
</div>
