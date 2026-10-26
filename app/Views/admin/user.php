<?php
/** @var array<string, mixed> $user */
/** @var array<int, array<string, mixed>> $cases */
$moderator = auth();
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/admin')) ?>">Panel</a>
            <a href="<?= e(url('/admin/usuarios')) ?>">Cuentas</a>
            <span aria-current="page"><?= e($user['name']) ?></span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Gestión de cuenta</span>
        <h1><?= e($user['name']) ?></h1>
        <p class="page-hero-lead"><?= e($user['email']) ?> · <?= e(role_label((string) $user['role'])) ?> · <?= e(location_label($user['city'] ?? null, $user['state_code'] ?? null)) ?></p>
        <?php \App\Core\View::partial('partials/admin-nav'); ?>
    </div>
</section>

<section class="section section-tight">
    <div class="container admin-review-layout">
        <div class="admin-review-main">
            <div class="admin-card">
                <h2>Datos de la cuenta</h2>
                <table class="spec-table">
                    <tbody>
                    <tr><th>Nombre</th><td><?= e($user['name']) ?></td></tr>
                    <tr><th>Correo</th><td><?= e($user['email']) ?></td></tr>
                    <tr><th>Rol</th><td><?= e(role_label((string) $user['role'])) ?></td></tr>
                    <tr><th>Estado</th><td><?= e(user_status_label((string) $user['status'])) ?></td></tr>
                    <tr><th>Especialidad</th><td><?= e($user['headline'] ?: '—') ?></td></tr>
                    <tr><th>Taller</th><td><?= e($user['workshop_name'] ?: '—') ?></td></tr>
                    <tr><th>Teléfono</th><td><?= e($user['phone'] ?: '—') ?></td></tr>
                    <tr><th>Verificación</th><td><?= e(verification_label((string) $user['verification_method'])) ?><?= $user['verified_at'] ? ' · ' . e(format_date((string) $user['verified_at'])) : '' ?></td></tr>
                    <tr><th>Comprobante</th><td><?= $user['proof_path'] ? 'Archivo recibido (privado)' : 'Sin archivo' ?></td></tr>
                    <tr><th>Semblanza</th><td><?= nl2br(e($user['bio'] ?: '—')) ?></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="admin-card">
                <h2>Casos de taller</h2>
                <?php if ($cases === []): ?>
                    <p class="muted">Sin casos.</p>
                <?php else: ?>
                    <div class="admin-list">
                        <?php foreach ($cases as $case): ?>
                            <a class="admin-list-row" href="<?= e(url('/admin/revision/' . $case['id'])) ?>">
                                <span class="admin-list-main">
                                    <strong><?= e($case['title']) ?></strong>
                                    <small><?= e(status_label((string) $case['status'])) ?> · <?= e(time_ago((string) $case['updated_at'])) ?></small>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <aside class="admin-review-side">
            <form class="admin-card" method="post" action="<?= e(url('/admin/usuarios/' . $user['id'] . '/accion')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="verificar">
                <h2>Verificar</h2>
                <div class="field">
                    <label for="method">Método</label>
                    <select id="method" name="method">
                        <option value="cedula">Cédula profesional</option>
                        <option value="taller">Registro de taller</option>
                        <option value="manual">Revisión manual</option>
                    </select>
                </div>
                <div class="field">
                    <label for="note">Nota interna</label>
                    <input id="note" name="note" type="text" maxlength="200" placeholder="Ej. Cédula 1234567 verificada">
                </div>
                <button class="btn btn-primary btn-block" type="submit">Verificar cuenta</button>
            </form>

            <form class="admin-card" method="post" action="<?= e(url('/admin/usuarios/' . $user['id'] . '/accion')) ?>">
                <?= csrf_field() ?>
                <h2>Estado</h2>
                <button class="btn btn-outline btn-block" type="submit" name="action" value="suspender">Suspender cuenta</button>
                <button class="btn btn-ghost btn-block" type="submit" name="action" value="reactivar">Reactivar (pendiente)</button>
            </form>

            <?php if ($moderator !== null && $moderator['role'] === 'admin'): ?>
                <form class="admin-card" method="post" action="<?= e(url('/admin/usuarios/' . $user['id'] . '/accion')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="rol">
                    <h2>Rol</h2>
                    <div class="field">
                        <select name="role">
                            <?php foreach (category_config('roles') as $code => $label): ?>
                                <option value="<?= e($code) ?>" <?= $user['role'] === $code ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-dark btn-block" type="submit">Cambiar rol</button>
                </form>
            <?php endif; ?>
        </aside>
    </div>
</section>
