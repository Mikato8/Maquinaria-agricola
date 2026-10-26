<?php
/** @var array<string, mixed> $profile */
/** @var array<int, array{code: string, name: string, region: string}> $states */
$user = auth();
?><section class="page-hero page-hero-compact">
    <div class="container">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Mi cuenta</span>
        <h1>Perfil y seguridad</h1>
        <p class="page-hero-lead">Estos datos aparecen en tus casos de taller. La verificación no se muestra públicamente.</p>
    </div>
</section>

<section class="section section-tight">
    <div class="container profile-layout">
        <div class="profile-main">
            <div class="profile-hero">
                <div class="profile-hero-main">
                    <?= avatar_tag((string) $profile['name'], $profile['avatar_url'] ?? null, 'avatar-xl') ?>
                    <div>
                        <h2><?= e($profile['name']) ?></h2>
                        <p><?= e($profile['headline'] ?: role_label((string) $profile['role'])) ?><?= $profile['workshop_name'] ? ' · ' . e($profile['workshop_name']) : '' ?></p>
                        <p class="muted"><?= e(location_label($profile['city'] ?? null, $profile['state_code'] ?? null)) ?> · Miembro desde <?= e(format_date((string) $profile['created_at'], 'M Y')) ?></p>
                        <span class="badge badge-<?= $profile['status'] === 'verificado' ? 'success' : 'warning' ?>"><?= e(user_status_label((string) $profile['status'])) ?></span>
                    </div>
                </div>
                <div class="profile-hero-stats">
                    <div><strong><?= (int) ($profile['cases_count'] ?? 0) ?></strong><span>Casos publicados</span></div>
                    <div><strong><?= (int) ($profile['views_total'] ?? 0) ?></strong><span>Lecturas</span></div>
                    <div><strong><?= (int) ($profile['thanks_total'] ?? 0) ?></strong><span>Marcas de útil</span></div>
                </div>
            </div>

            <form class="case-form" method="post" action="<?= e(url('/perfil')) ?>">
                <?= csrf_field() ?>
                <div class="form-section">
                    <div class="form-section-head">
                        <h2>Datos públicos</h2>
                        <p>Así te verán otros técnicos en tus casos y en el perfil público.</p>
                    </div>
                    <div class="field-grid field-grid-2">
                        <div class="field">
                            <label for="name">Nombre completo *</label>
                            <input id="name" name="name" type="text" required maxlength="120" value="<?= e(old('name', $profile['name'])) ?>">
                        </div>
                        <div class="field">
                            <label for="headline">Especialidad</label>
                            <input id="headline" name="headline" type="text" maxlength="120" value="<?= e(old('headline', $profile['headline'])) ?>" placeholder="Mecánica de tractores, hidráulica">
                        </div>
                        <div class="field">
                            <label for="workshop_name">Taller o empresa</label>
                            <input id="workshop_name" name="workshop_name" type="text" maxlength="120" value="<?= e(old('workshop_name', $profile['workshop_name'])) ?>">
                        </div>
                        <div class="field">
                            <label for="phone">Teléfono</label>
                            <input id="phone" name="phone" type="tel" maxlength="30" value="<?= e(old('phone', $profile['phone'])) ?>">
                        </div>
                        <div class="field">
                            <label for="city">Ciudad o municipio</label>
                            <input id="city" name="city" type="text" maxlength="80" value="<?= e(old('city', $profile['city'])) ?>">
                        </div>
                        <div class="field">
                            <label for="state_code">Estado *</label>
                            <select id="state_code" name="state_code" required>
                                <?php foreach ($states as $state): ?>
                                    <option value="<?= e($state['code']) ?>" <?= (old('state_code', $profile['state_code']) === $state['code']) ? 'selected' : '' ?>><?= e($state['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label for="bio">Semblanza</label>
                        <textarea id="bio" name="bio" rows="4" maxlength="1200" placeholder="Experiencia, marcas que atiendes, región y cultivos."><?= e(old('bio', $profile['bio'])) ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-primary" type="submit">Guardar cambios</button>
                    </div>
                </div>
            </form>

            <div class="form-section" id="seguridad">
                <div class="form-section-head">
                    <h2>Contraseña</h2>
                    <p>Cambia tu contraseña si alguien más tuvo acceso a tu cuenta.</p>
                </div>
                <form class="case-form" method="post" action="<?= e(url('/perfil/password')) ?>">
                    <?= csrf_field() ?>
                    <div class="field-grid field-grid-3">
                        <div class="field">
                            <label for="current_password">Contraseña actual</label>
                            <input id="current_password" name="current_password" type="password" required autocomplete="current-password">
                        </div>
                        <div class="field">
                            <label for="password">Nueva contraseña</label>
                            <input id="password" name="password" type="password" required minlength="10" autocomplete="new-password">
                        </div>
                        <div class="field">
                            <label for="password_confirmation">Confirmar</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="10" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-outline" type="submit">Actualizar contraseña</button>
                    </div>
                </form>
            </div>

            <div class="form-section form-section-danger" id="eliminar">
                <div class="form-section-head">
                    <h2>Eliminar cuenta</h2>
                    <p>Se anonimizan tus datos personales. Los casos publicados quedan sin tu nombre ni contacto.</p>
                </div>
                <form method="post" action="<?= e(url('/perfil/eliminar')) ?>" onsubmit="return confirm('¿Eliminar tu cuenta? Esta acción no se puede deshacer.');">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label for="confirm">Escribe tu contraseña para confirmar</label>
                        <input id="confirm" name="confirm" type="password" required>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-danger" type="submit">Eliminar mi cuenta</button>
                    </div>
                </form>
            </div>
        </div>

        <aside class="profile-sidebar">
            <div class="aside-card">
                <h3>Verificación</h3>
                <p class="verification-box verification-<?= e((string) $profile['status']) ?>">
                    <strong><?= e(user_status_label((string) $profile['status'])) ?></strong><br>
                    Método: <?= e(verification_label((string) $profile['verification_method'])) ?>
                </p>
                <?php if ($profile['status'] !== 'verificado'): ?>
                    <p>Envíanos tu cédula profesional o una constancia del taller desde el formulario de contacto; el comité revisa y activa la cuenta.</p>
                    <a class="btn btn-outline btn-sm btn-block" href="<?= e(url('/contacto?motivo=taller')) ?>">Enviar comprobante</a>
                <?php endif; ?>
            </div>

            <div class="aside-card">
                <h3>Foto de perfil</h3>
                <p>JPG, PNG o WebP. Se recorta a 600 px.</p>
                <form method="post" action="<?= e(url('/perfil/avatar')) ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="field">
                        <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" required>
                    </div>
                    <button class="btn btn-outline btn-sm btn-block" type="submit">Subir foto</button>
                </form>
                <?php if (!empty($profile['avatar_url'])): ?>
                    <form method="post" action="<?= e(url('/perfil/avatar/eliminar')) ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn-ghost btn-sm btn-block" type="submit">Quitar foto</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="aside-card">
                <h3>Correo</h3>
                <p><?= e($profile['email']) ?></p>
                <p class="muted">Para cambiar el correo, escríbenos desde <a href="<?= e(url('/contacto')) ?>">contacto</a>.</p>
            </div>
        </aside>
    </div>
</section>
