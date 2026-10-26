<?php
/** @var array<int, array{code: string, name: string, region: string}> $states */
$errors = \App\Core\Flash::errors();
?><section class="auth-section auth-section-register">
    <div class="container auth-grid auth-grid-wide">
        <div class="auth-panel">
            <span class="eyebrow">Nueva cuenta</span>
            <h1>Crea tu cuenta</h1>
            <p class="auth-lead">Gratis para productores, técnicos y talleres. Consultar no requiere cuenta; para publicar casos verificamos tu cédula o registro de taller.</p>

            <?php if ($errors !== []): ?>
                <div class="form-error-summary" role="alert">
                    <?= icon('alert-triangle', 'icon') ?>
                    <div><strong>Revisa los siguientes campos</strong><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div>
                </div>
            <?php endif; ?>

            <div class="notice notice-info">
                <?= icon('shield-check', 'icon') ?>
                <div>
                    <strong>Cómo verificamos</strong>
                    <p>Puedes subir tu cédula profesional o una constancia del taller. Un editor la revisa y activa tu cuenta para publicar. Mientras tanto puedes guardar borradores y favoritos.</p>
                </div>
            </div>

            <form class="auth-form" method="post" action="<?= e(url('/registro')) ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="field-grid field-grid-2">
                    <div class="field">
                        <label for="name">Nombre completo <span class="req">*</span></label>
                        <input id="name" name="name" type="text" autocomplete="name" required maxlength="120" value="<?= e(old('name')) ?>" placeholder="Juan Pérez Hernández">
                    </div>
                    <div class="field">
                        <label for="email">Correo electrónico <span class="req">*</span></label>
                        <input id="email" name="email" type="email" autocomplete="email" required maxlength="180" value="<?= e(old('email')) ?>" placeholder="tucorreo@ejemplo.mx">
                    </div>
                </div>

                <div class="field-grid field-grid-3">
                    <div class="field">
                        <label for="role">Tipo de cuenta <span class="req">*</span></label>
                        <select id="role" name="role" required>
                            <option value="productor" <?= old('role', 'productor') === 'productor' ? 'selected' : '' ?>>Productor</option>
                            <option value="tecnico" <?= old('role') === 'tecnico' ? 'selected' : '' ?>>Técnico o mecánico</option>
                            <option value="taller" <?= old('role') === 'taller' ? 'selected' : '' ?>>Taller o empresa de servicio</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="workshop_name">Taller o empresa</label>
                        <input id="workshop_name" name="workshop_name" type="text" maxlength="120" value="<?= e(old('workshop_name')) ?>" placeholder="Taller Diesel del Bajío">
                    </div>
                    <div class="field">
                        <label for="headline">Especialidad</label>
                        <input id="headline" name="headline" type="text" maxlength="120" value="<?= e(old('headline')) ?>" placeholder="Mecánica de tractores y sistemas hidráulicos">
                    </div>
                </div>

                <div class="field-grid field-grid-3">
                    <div class="field">
                        <label for="state_code">Estado <span class="req">*</span></label>
                        <select id="state_code" name="state_code" required>
                            <option value="">Selecciona…</option>
                            <?php foreach ($states as $state): ?>
                                <option value="<?= e($state['code']) ?>" <?= old('state_code') === $state['code'] ? 'selected' : '' ?>><?= e($state['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="city">Ciudad o municipio</label>
                        <input id="city" name="city" type="text" maxlength="80" value="<?= e(old('city')) ?>" placeholder="Irapuato">
                    </div>
                    <div class="field">
                        <label for="phone">Teléfono (opcional)</label>
                        <input id="phone" name="phone" type="tel" maxlength="30" value="<?= e(old('phone')) ?>" placeholder="462 000 0000">
                    </div>
                </div>

                <div class="field">
                    <label for="bio">Semblanza breve</label>
                    <textarea id="bio" name="bio" rows="3" maxlength="1200" placeholder="Años de experiencia, marcas que atiendes, cultivos o regiones donde trabajas."><?= e(old('bio')) ?></textarea>
                </div>

                <div class="field">
                    <label for="proof">Cédula o constancia del taller (JPG, PNG o PDF)</label>
                    <input id="proof" name="proof" type="file" accept="image/jpeg,image/png,application/pdf">
                    <span class="field-help">Se guarda en un directorio privado y solo lo ve el comité editorial.</span>
                </div>

                <div class="field-grid field-grid-2">
                    <div class="field">
                        <label for="password">Contraseña <span class="req">*</span></label>
                        <div class="password-field">
                            <input id="password" name="password" type="password" autocomplete="new-password" required minlength="10" placeholder="Mínimo 10 caracteres">
                            <?php \App\Core\View::partial('partials/password-toggle', ['id' => 'password']); ?>
                        </div>
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Confirmar contraseña <span class="req">*</span></label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="10" placeholder="Repite la contraseña">
                    </div>
                </div>

                <label class="check-line">
                    <input type="checkbox" name="terms" value="1" <?= old('terms') ? 'checked' : '' ?> required>
                    <span>Acepto los <a href="<?= e(url('/terminos')) ?>" target="_blank">términos de uso</a> y el <a href="<?= e(url('/privacidad')) ?>" target="_blank">aviso de privacidad</a>.</span>
                </label>
                <?php if (field_error('terms')): ?><span class="field-error"><?= e(field_error('terms')) ?></span><?php endif; ?>

                <button class="btn btn-primary btn-block" type="submit">Crear cuenta <?= icon('arrow-right', 'icon icon-sm') ?></button>
            </form>

            <p class="auth-switch">¿Ya tienes cuenta? <a href="<?= e(url('/acceder')) ?>">Inicia sesión</a></p>
        </div>

        <aside class="auth-aside">
            <div class="auth-aside-top">
                <span class="eyebrow eyebrow-light"><span class="eyebrow-dot"></span> Verificación con criterio</span>
                <h2>Publicar requiere identidad, no burocracia.</h2>
                <p>La cédula profesional o el registro del taller permiten que un caso tenga autor responsable. No pedimos datos innecesarios ni compartimos tu comprobante.</p>
            </div>
            <ul class="check-list check-list-light">
                <li><?= icon('check', 'icon icon-sm') ?> Consulta libre del catálogo y las guías.</li>
                <li><?= icon('check', 'icon icon-sm') ?> Favoritos y borradores desde el primer día.</li>
                <li><?= icon('check', 'icon icon-sm') ?> Publicación después de la revisión editorial.</li>
                <li><?= icon('check', 'icon icon-sm') ?> Puedes eliminar tu cuenta y anonimizar tus datos.</li>
            </ul>
        </aside>
    </div>
</section>
