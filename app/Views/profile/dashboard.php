<?php
/** @var array<int, array<string, mixed>> $cases */
/** @var array{total: int, by_type: array<string, int>} $bookmarks */
/** @var array<int, array<string, mixed>> $bookmarkList */
/** @var array<string, mixed> $profile */
$user = auth();
?><section class="page-hero page-hero-compact">
    <div class="container">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Mi panel</span>
        <h1>Hola, <?= e(first_name((string) $user['name'])) ?>.</h1>
        <p class="page-hero-lead">Tus casos de taller, lo que guardaste y el estado de tu cuenta en un solo lugar.</p>
        <?php if ($user['status'] !== 'verificado'): ?>
            <div class="notice notice-warning">
                <?= icon('alert-triangle', 'icon') ?>
                <div>
                    <strong>Tu cuenta está pendiente de verificación</strong>
                    <p>Puedes guardar borradores y favoritos. Para publicar o comentar, sube tu cédula o constancia de taller en <a href="<?= e(url('/perfil')) ?>">tu perfil</a>.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="panel-grid">
            <div class="panel-card">
                <span class="panel-label">Casos de taller</span>
                <strong><?= count($cases) ?></strong>
                <a href="<?= e(url('/taller/nuevo')) ?>">Documentar una reparación</a>
            </div>
            <div class="panel-card">
                <span class="panel-label">Guardados</span>
                <strong><?= (int) $bookmarks['total'] ?></strong>
                <a href="#guardados">Ver favoritos</a>
            </div>
            <div class="panel-card">
                <span class="panel-label">Casos publicados</span>
                <strong><?= (int) ($profile['cases_count'] ?? 0) ?></strong>
                <a href="<?= e(url('/perfil-publico/' . $user['id'])) ?>">Ver perfil público</a>
            </div>
            <div class="panel-card">
                <span class="panel-label">Estado</span>
                <strong><?= e($user['status'] === 'verificado' ? 'Verificado' : 'Por verificar') ?></strong>
                <a href="<?= e(url('/perfil')) ?>">Gestionar cuenta</a>
            </div>
        </div>

        <div class="dashboard-layout">
            <div class="dashboard-main">
                <div class="section-head section-head-spaced">
                    <div>
                        <span class="eyebrow">Tus casos</span>
                        <h2 class="section-title-sm">Reparaciones documentadas</h2>
                    </div>
                    <a class="btn btn-primary btn-sm" href="<?= e(url('/taller/nuevo')) ?>">Nuevo caso</a>
                </div>

                <?php if ($cases === []): ?>
                    <div class="empty-state">
                        <span class="empty-icon"><?= icon('wrench') ?></span>
                        <h2>Aún no has documentado una reparación</h2>
                        <p>Empieza por la última falla que resolviste. Con fotos y datos concretos, tu caso ayuda a otros talleres.</p>
                        <a class="btn btn-primary" href="<?= e(url('/taller/nuevo')) ?>">Empezar mi primer caso</a>
                    </div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                            <tr><th>Caso</th><th>Máquina</th><th>Estado</th><th>Actualizado</th><th></th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($cases as $case): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($case['title']) ?></strong>
                                        <p class="table-muted"><?= e($case['folio']) ?> · <?= e(outcome_label((string) $case['outcome'])) ?></p>
                                    </td>
                                    <td><?= e($case['machine_label'] ?: machine_category_singular((string) $case['machine_category'])) ?></td>
                                    <td><span class="badge badge-<?= e(workshop_status_tone((string) $case['status'])) ?>"><?= e(status_label((string) $case['status'])) ?></span></td>
                                    <td><?= e(time_ago((string) $case['updated_at'])) ?></td>
                                    <td class="table-actions">
                                        <a class="text-link" href="<?= e(url('/taller/' . $case['slug'] . '/editar')) ?>">Editar</a>
                                        <?php if ($case['status'] === 'publicado'): ?>
                                            <a class="text-link" href="<?= e(url('/taller/' . $case['slug'])) ?>">Ver</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="dashboard-aside" id="guardados">
                <div class="aside-card">
                    <h3>Guardados</h3>
                    <?php if ($bookmarkList === []): ?>
                        <p>Todavía no guardas fichas. Usa el botón «Guardar» en máquinas, fallas o guías.</p>
                    <?php else: ?>
                        <div class="aside-list">
                            <?php foreach ($bookmarkList as $item): ?>
                                <a href="<?= e($item['url']) ?>">
                                    <span><?= e($item['title']) ?><br><small class="muted"><?= e(bookmark_type_label($item['type'])) ?> · <?= e(time_ago($item['date'])) ?></small></span>
                                    <?= icon('arrow-right', 'icon icon-xs') ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="aside-card">
                    <h3>Atajos</h3>
                    <div class="aside-list">
                        <a href="<?= e(url('/compatibilidad')) ?>">Calculadora de compatibilidad <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                        <a href="<?= e(url('/mantenimiento')) ?>">Plan de mantenimiento <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                        <a href="<?= e(url('/fallas')) ?>">Fallas y diagnóstico <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                        <a href="<?= e(url('/perfil')) ?>">Mi perfil y seguridad <span><?= icon('arrow-right', 'icon icon-xs') ?></span></a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
