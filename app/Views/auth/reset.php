<?php
/** @var string $token */
$errors = \App\Core\Flash::errors();
?><section class="auth-section">
    <div class="container auth-grid">
        <div class="auth-panel">
            <span class="eyebrow">Nueva contraseña</span>
            <h1>Define una contraseña nueva</h1>
            <p class="auth-lead">Mínimo 10 caracteres. Usa una combinación que no repitas en otros sitios.</p>

            <?php if ($errors !== []): ?>
                <div class="form-error-summary" role="alert">
                    <?= icon('alert-triangle', 'icon') ?>
                    <div><strong>Revisa los campos</strong><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="<?= e(url('/recuperar/' . $token)) ?>">
                <?= csrf_field() ?>
                <div class="field">
                    <label for="password">Contraseña nueva</label>
                    <div class="password-field">
                        <input id="password" name="password" type="password" autocomplete="new-password" required minlength="10">
                        <?php \App\Core\View::partial('partials/password-toggle', ['id' => 'password']); ?>
                    </div>
                </div>
                <div class="field">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="10">
                </div>
                <button class="btn btn-primary btn-block" type="submit">Guardar contraseña</button>
            </form>
        </div>
        <aside class="auth-aside">
            <div class="auth-aside-top">
                <span class="eyebrow eyebrow-light"><span class="eyebrow-dot"></span> Seguridad</span>
                <h2>El enlace vence en 60 minutos y solo funciona una vez.</h2>
                <p>Si ya venció, solicita uno nuevo desde la pantalla de recuperación.</p>
            </div>
        </aside>
    </div>
</section>
