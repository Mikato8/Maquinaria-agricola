<?php
/** @var string $kind */
/** @var array<int, array{code: string, name: string, region: string}> $states */
$subjects = [
    'contacto' => 'Consulta general',
    'sugerencia' => 'Corrección de una ficha',
    'taller' => 'Verificación de cuenta o comprobante',
    'proveedor' => 'Alta de proveedor o taller',
];
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Contacto</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Escríbenos</span>
        <h1>Correcciones, altas y consultas.</h1>
        <p class="page-hero-lead">
            Si un dato no coincide con tu manual, si conoces un taller que debería estar en el directorio o si quieres
            verificar tu cuenta, este es el canal.
        </p>
    </div>
</section>

<section class="section section-tight">
    <div class="container contact-grid">
        <form class="case-form" method="post" action="<?= e(url('/contacto')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="kind" value="<?= e($kind) ?>">
            <div class="form-section">
                <div class="form-section-head">
                    <h2>Tu mensaje</h2>
                    <p>Todos los campos con * son obligatorios. Respondemos en días hábiles.</p>
                </div>

                <div class="field">
                    <label for="subject">Asunto</label>
                    <select id="subject" name="subject">
                        <?php foreach ($subjects as $value => $label): ?>
                            <option value="<?= e($label) ?>" <?= $kind === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="field-grid field-grid-2">
                    <div class="field">
                        <label for="name">Nombre *</label>
                        <input id="name" name="name" type="text" required maxlength="120" value="<?= e(old('name')) ?>">
                    </div>
                    <div class="field">
                        <label for="email">Correo *</label>
                        <input id="email" name="email" type="email" required maxlength="180" value="<?= e(old('email')) ?>">
                    </div>
                </div>

                <div class="field-grid field-grid-2">
                    <div class="field">
                        <label for="phone">Teléfono (opcional)</label>
                        <input id="phone" name="phone" type="tel" maxlength="30" value="<?= e(old('phone')) ?>">
                    </div>
                    <div class="field">
                        <label for="state_code">Estado</label>
                        <select id="state_code" name="state_code">
                            <option value="">Sin especificar</option>
                            <?php foreach ($states as $state): ?>
                                <option value="<?= e($state['code']) ?>" <?= old('state_code') === $state['code'] ? 'selected' : '' ?>><?= e($state['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="message">Mensaje *</label>
                    <textarea id="message" name="message" rows="7" required minlength="20" maxlength="3000" placeholder="Si reportas una corrección, incluye el modelo, la página del manual y la edición."><?= e(old('message')) ?></textarea>
                    <span class="field-help">Mínimo 20 caracteres. No incluyas datos personales de terceros.</span>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary" type="submit">Enviar mensaje</button>
                </div>
            </div>
        </form>

        <aside class="contact-aside">
            <div class="aside-card">
                <h3>Para correcciones técnicas</h3>
                <p>Incluye:</p>
                <ul class="check-list">
                    <li><?= icon('check', 'icon icon-sm') ?> <span>Modelo y año exactos.</span></li>
                    <li><?= icon('check', 'icon icon-sm') ?> <span>Dato correcto y fuente (página del manual, boletín, ficha de distribuidor).</span></li>
                    <li><?= icon('check', 'icon icon-sm') ?> <span>Si es una falla, síntoma, código y solución comprobada.</span></li>
                </ul>
            </div>
            <div class="aside-card">
                <h3>Para verificar tu cuenta</h3>
                <p>Envía tu cédula profesional o constancia del taller en PDF o imagen. Se guarda en un directorio privado y solo lo revisa el comité.</p>
            </div>
            <div class="aside-card">
                <h3>Para proveedores</h3>
                <p>Mándanos nombre comercial, dirección, teléfono, marcas que atiendes y sitio web. Publicamos solo datos verificables y sin costo.</p>
            </div>
            <div class="callout warning-note">
                <?= icon('alert-triangle', 'icon icon-sm') ?>
                <p>No damos soporte de emergencia ni diagnóstico por mensaje. Para una máquina detenida, contacta a tu distribuidor o a un taller certificado.</p>
            </div>
        </aside>
    </div>
</section>
