<?php $errors = \App\Core\Flash::errors(); ?>
<section class="auth-section">
    <div class="container auth-grid">
        <div class="auth-panel">
            <span class="eyebrow">Recuperar acceso</span>
            <h1>Restablece tu contraseña</h1>
            <p class="auth-lead">Escribe el correo de tu cuenta y te enviaremos un enlace válido por 60 minutos.</p>

            <?php if ($errors !== []): ?>
                <div class="form-error-summary" role="alert">
                    <?= icon('alert-triangle', 'icon') ?>
                    <div><strong>Revisa el correo</strong><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="<?= e(url('/recuperar')) ?>">
                <?= csrf_field() ?>
                <div class="field">
                    <label for="email">Correo electrónico</label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="<?= e(old('email')) ?>" placeholder="tucorreo@ejemplo.mx">
                </div>
                <button class="btn btn-primary btn-block" type="submit">Enviar enlace</button>
            </form>
            <p class="auth-switch"><a href="<?= e(url('/acceder')) ?>">Volver a iniciar sesión</a></p>
        </div>
        <aside class="auth-aside">
            <div class="auth-aside-top">
                <span class="eyebrow eyebrow-light"><span class="eyebrow-dot"></span> Seguridad</span>
                <h2>Nunca te pediremos tu contraseña por teléfono ni por mensaje.</h2>
                <p>Si recibes un enlace de recuperación que no solicitaste, ignóralo. Tu cuenta seguirá segura.</p>
            </div>
        </aside>
    </div>
</section>
