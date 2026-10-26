<?php
/** @var array<int, array<string, mixed>> $users */
/** @var int $total */
/** @var array<string, mixed> $filters */
/** @var array<string, int> $counts */
/** @var \App\Core\Paginator $paginator */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Moderación</span>
        <h1>Cuentas</h1>
        <p class="page-hero-lead">Verificación, suspensión y roles. La verificación acredita identidad, no avala técnicamente los casos.</p>
        <?php \App\Core\View::partial('partials/admin-nav'); ?>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <form class="filter-panel" method="get" action="<?= e(url('/admin/usuarios')) ?>">
            <div class="field-grid field-grid-4">
                <div class="field">
                    <label for="q">Buscar</label>
                    <input type="search" id="q" name="q" value="<?= e($filters['q']) ?>" placeholder="Nombre, correo o taller">
                </div>
                <div class="field">
                    <label for="status">Estado</label>
                    <select id="status" name="status">
                        <option value="">Todos</option>
                        <option value="pendiente" <?= $filters['status'] === 'pendiente' ? 'selected' : '' ?>>Pendientes (<?= (int) $counts['pendientes'] ?>)</option>
                        <option value="verificado" <?= $filters['status'] === 'verificado' ? 'selected' : '' ?>>Verificados (<?= (int) $counts['verificados'] ?>)</option>
                        <option value="suspendido" <?= $filters['status'] === 'suspendido' ? 'selected' : '' ?>>Suspendidos (<?= (int) $counts['suspendidos'] ?>)</option>
                    </select>
                </div>
                <div class="field">
                    <label for="role">Rol</label>
                    <select id="role" name="role">
                        <option value="">Todos</option>
                        <?php foreach (category_config('roles') as $code => $label): ?>
                            <option value="<?= e($code) ?>" <?= $filters['role'] === $code ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Cuenta</th><th>Rol</th><th>Estado</th><th>Casos</th><th>Alta</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <strong><?= e($user['name']) ?></strong>
                            <p class="table-muted"><?= e($user['email']) ?><?= $user['workshop_name'] ? ' · ' . e($user['workshop_name']) : '' ?></p>
                        </td>
                        <td><?= e(role_label((string) $user['role'])) ?></td>
                        <td><span class="badge badge-<?= $user['status'] === 'verificado' ? 'success' : ($user['status'] === 'suspendido' ? 'danger' : 'warning') ?>"><?= e(user_status_label((string) $user['status'])) ?></span></td>
                        <td><?= (int) $user['cases_count'] ?></td>
                        <td><?= e(format_date((string) $user['created_at'], 'd/m/Y')) ?></td>
                        <td class="table-actions"><a class="text-link" href="<?= e(url('/admin/usuarios/' . $user['id'])) ?>">Gestionar</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php \App\Core\View::partial('partials/pagination', ['paginator' => $paginator]); ?>
    </div>
</section>
