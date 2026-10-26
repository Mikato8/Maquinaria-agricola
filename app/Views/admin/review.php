<?php
/** @var array<string, mixed> $case */
/** @var array<int, array<string, mixed>> $images */
/** @var array<int, array<string, mixed>> $history */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/admin')) ?>">Panel</a>
            <a href="<?= e(url('/admin/casos')) ?>">Casos</a>
            <span aria-current="page"><?= e($case['folio']) ?></span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Revisión editorial</span>
        <h1><?= e($case['title']) ?></h1>
        <p class="page-hero-lead"><?= e($case['summary'] ?: excerpt((string) $case['symptom'], 200)) ?></p>
        <?php \App\Core\View::partial('partials/admin-nav'); ?>
    </div>
</section>

<section class="section section-tight">
    <div class="container admin-review-layout">
        <div class="admin-review-main">
            <div class="admin-card">
                <h2>Síntoma</h2>
                <p><?= nl2br(e((string) $case['symptom'])) ?></p>
            </div>
            <div class="admin-card">
                <h2>Diagnóstico</h2>
                <p><?= nl2br(e((string) $case['diagnosis'])) ?></p>
            </div>
            <div class="admin-card">
                <h2>Reparación</h2>
                <p><?= nl2br(e((string) $case['repair'])) ?></p>
                <?php if ((string) $case['parts'] !== ''): ?><p class="muted">Refacciones: <?= e($case['parts']) ?></p><?php endif; ?>
            </div>
            <?php if ($images !== []): ?>
                <div class="admin-card">
                    <h2>Imágenes</h2>
                    <div class="case-gallery case-gallery-sm">
                        <?php foreach ($images as $image): ?>
                            <figure><img src="<?= e($image['url']) ?>" alt="Imagen del caso" loading="lazy"></figure>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($history !== []): ?>
                <div class="admin-card">
                    <h2>Historial de revisión</h2>
                    <div class="review-history">
                        <?php foreach ($history as $entry): ?>
                            <div>
                                <strong><?= e($entry['reviewer_name'] ?: 'Editor') ?></strong>
                                <span class="badge badge-soft"><?= e(['publicar' => 'Publicado', 'cambios' => 'Cambios', 'rechazar' => 'Rechazado'][$entry['decision']] ?? $entry['decision']) ?></span>
                                <p><?= e($entry['notes']) ?></p>
                                <small><?= e(time_ago((string) $entry['created_at'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <aside class="admin-review-side">
            <div class="admin-card">
                <h2>Ficha</h2>
                <div class="aside-list">
                    <span>Autor <strong><?= e($case['author_name'] ?? '') ?></strong></span>
                    <span>Máquina <strong><?= e($case['machine_label'] ?: machine_category_singular((string) $case['machine_category'])) ?></strong></span>
                    <span>Resultado <strong><?= e(outcome_label((string) $case['outcome'])) ?></strong></span>
                    <span>Costo <strong><?= $case['cost_mxn'] !== null ? e(compact_money($case['cost_mxn'])) : '—' ?></strong></span>
                    <span>Estado <strong><?= e(status_label((string) $case['status'])) ?></strong></span>
                </div>
                <a class="btn btn-outline btn-sm btn-block" href="<?= e(url('/taller/' . $case['slug'])) ?>" target="_blank" rel="noopener">Ver borrador público</a>
            </div>

            <form class="admin-card" method="post" action="<?= e(url('/admin/revision/' . $case['id'])) ?>">
                <?= csrf_field() ?>
                <h2>Decisión</h2>
                <div class="field">
                    <label for="decision">Resultado de la revisión</label>
                    <select id="decision" name="decision" required>
                        <option value="publicar">Publicar</option>
                        <option value="cambios">Solicitar cambios</option>
                        <option value="rechazar">Rechazar</option>
                    </select>
                </div>
                <div class="field">
                    <label for="notes">Notas para el autor</label>
                    <textarea id="notes" name="notes" rows="5" placeholder="Qué falta, qué corregir o por qué se rechaza. Sé específico y respetuoso."></textarea>
                    <span class="field-help">Obligatorio para solicitar cambios o rechazar.</span>
                </div>
                <button class="btn btn-primary btn-block" type="submit">Registrar decisión</button>
            </form>

            <form class="admin-card" method="post" action="<?= e(url('/admin/destacar/' . $case['id'])) ?>">
                <?= csrf_field() ?>
                <h2>Visibilidad</h2>
                <p class="muted"><?= !empty($case['featured']) ? 'Este caso está destacado en la portada.' : 'Destacar lo muestra primero en el taller.' ?></p>
                <button class="btn btn-outline btn-sm btn-block" type="submit"><?= !empty($case['featured']) ? 'Quitar de destacados' : 'Destacar caso' ?></button>
            </form>
        </aside>
    </div>
</section>
