<?php
/** @var array<string, mixed>|null $case */
/** @var array<int, array<string, mixed>> $images */
/** @var array<int, array<string, mixed>> $machinesList */
/** @var array<int, array{code: string, name: string, region: string}> $states */
/** @var string $action */
$isEdit = $case !== null;
$value = static function (string $key, string $default = '') use ($case): string {
    $old = old($key, null);
    if ($old !== null && $old !== '') {
        return (string) $old;
    }
    return $case !== null && isset($case[$key]) ? (string) $case[$key] : $default;
};
$selectedCategory = $value('machine_category', 'tractor');
$selectedOutcome = $value('outcome', 'resuelto');
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <a href="<?= e(url('/taller')) ?>">Taller</a>
            <span aria-current="page"><?= $isEdit ? 'Editar caso' : 'Nuevo caso' ?></span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Documentar reparación</span>
        <h1><?= $isEdit ? 'Edita tu caso de taller' : 'Cuenta cómo lo reparaste.' ?></h1>
        <p class="page-hero-lead">
            Un buen caso ahorra tiempo: síntoma exacto, cómo confirmaste el diagnóstico, qué refacción usaste,
            cuánto costó y si funcionó. Las fotos ayudan más que los adjetivos.
        </p>
    </div>
</section>

<section class="section section-tight">
    <div class="container form-layout">
        <form class="case-form" method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="form-section">
                <div class="form-section-head">
                    <h2>Identificación</h2>
                    <p>Datos para que otros encuentren el caso por máquina o síntoma.</p>
                </div>
                <div class="field">
                    <label for="title">Título del caso *</label>
                    <input type="text" id="title" name="title" maxlength="140" required value="<?= e($value('title')) ?>" placeholder="Ej. Tractor 5075E no arranca en frío: bomba de transferencia obstruida">
                    <?php if (field_error('title')): ?><span class="field-error"><?= e(field_error('title')) ?></span><?php endif; ?>
                </div>
                <div class="field">
                    <label for="summary">Resumen (una o dos frases)</label>
                    <textarea id="summary" name="summary" rows="2" maxlength="300" placeholder="Qué pasaba, qué era y cuánto costó, en una línea."><?= e($value('summary')) ?></textarea>
                </div>
                <div class="field-grid field-grid-3">
                    <div class="field">
                        <label for="machine_category">Tipo de máquina *</label>
                        <select id="machine_category" name="machine_category">
                            <?php foreach (category_config('machine_categories') as $code => $category): ?>
                                <option value="<?= e($code) ?>" <?= $selectedCategory === $code ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                            <?php endforeach; ?>
                            <option value="implemento" <?= $selectedCategory === 'implemento' ? 'selected' : '' ?>>Implemento</option>
                            <option value="general" <?= $selectedCategory === 'general' ? 'selected' : '' ?>>General</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="machine_label">Marca y modelo</label>
                        <input type="text" id="machine_label" name="machine_label" maxlength="120" value="<?= e($value('machine_label')) ?>" placeholder="Ej. John Deere 5075E 2019">
                    </div>
                    <div class="field">
                        <label for="machine_id">Ficha del catálogo (opcional)</label>
                        <select id="machine_id" name="machine_id">
                            <option value="">Sin vincular</option>
                            <?php foreach ($machinesList as $machine): ?>
                                <option value="<?= (int) $machine['id'] ?>" <?= (int) $value('machine_id') === (int) $machine['id'] ? 'selected' : '' ?>>
                                    <?= e($machine['brand_name'] . ' ' . $machine['model']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="field-grid field-grid-2">
                    <div class="field">
                        <label for="state_code">Estado</label>
                        <select id="state_code" name="state_code">
                            <option value="">Sin especificar</option>
                            <?php foreach ($states as $state): ?>
                                <option value="<?= e($state['code']) ?>" <?= $value('state_code') === $state['code'] ? 'selected' : '' ?>><?= e($state['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="cost_mxn">Costo de refacciones (MXN)</label>
                        <input type="number" id="cost_mxn" name="cost_mxn" min="0" step="50" value="<?= e($value('cost_mxn')) ?>" placeholder="1850">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-head">
                    <h2>Síntoma y diagnóstico</h2>
                    <p>Qué se observó, en qué condiciones y cómo se confirmó la causa.</p>
                </div>
                <div class="field">
                    <label for="symptom">Síntoma *</label>
                    <textarea id="symptom" name="symptom" rows="4" minlength="20" required placeholder="Ej. Arranca en caliente pero en frío tarda 8–10 segundos; humo blanco al primer intento y olor fuerte a diésel."><?= e($value('symptom')) ?></textarea>
                    <?php if (field_error('symptom')): ?><span class="field-error"><?= e(field_error('symptom')) ?></span><?php endif; ?>
                </div>
                <div class="field">
                    <label for="diagnosis">Diagnóstico y pruebas *</label>
                    <textarea id="diagnosis" name="diagnosis" rows="4" minlength="20" required placeholder="Ej. Se midió presión en el filtro (1.2 bar, especificación 2.5); se sopló la línea y salió sedimento. Se descartaron bujías y batería con prueba de carga."><?= e($value('diagnosis')) ?></textarea>
                    <?php if (field_error('diagnosis')): ?><span class="field-error"><?= e(field_error('diagnosis')) ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-head">
                    <h2>Reparación</h2>
                    <p>Procedimiento, refacciones, tiempo y resultado.</p>
                </div>
                <div class="field">
                    <label for="repair">Reparación paso a paso *</label>
                    <textarea id="repair" name="repair" rows="6" minlength="20" required placeholder="1. Se despresurizó el sistema… 2. Se cambió el filtro… 3. Se purgó…"><?= e($value('repair')) ?></textarea>
                    <?php if (field_error('repair')): ?><span class="field-error"><?= e(field_error('repair')) ?></span><?php endif; ?>
                </div>
                <div class="field">
                    <label for="parts">Refacciones e insumos</label>
                    <input type="text" id="parts" name="parts" maxlength="400" value="<?= e($value('parts')) ?>" placeholder="Filtro de combustible primario, abrazadera 8 mm, 2 L de diésel">
                </div>
                <div class="field-grid field-grid-3">
                    <div class="field">
                        <label for="time_hours">Horas de trabajo</label>
                        <input type="number" id="time_hours" name="time_hours" min="0" max="200" step="0.5" value="<?= e($value('time_hours')) ?>" placeholder="2.5">
                    </div>
                    <div class="field">
                        <label for="difficulty">Dificultad (1 a 5)</label>
                        <select id="difficulty" name="difficulty">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= (int) $value('difficulty', '3') === $i ? 'selected' : '' ?>><?= $i ?> · <?= e(difficulty_short($i)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="outcome">Resultado</label>
                        <select id="outcome" name="outcome">
                            <option value="resuelto" <?= $selectedOutcome === 'resuelto' ? 'selected' : '' ?>>Resuelto</option>
                            <option value="parcial" <?= $selectedOutcome === 'parcial' ? 'selected' : '' ?>>Mejoró parcialmente</option>
                            <option value="no_resuelto" <?= $selectedOutcome === 'no_resuelto' ? 'selected' : '' ?>>No se resolvió</option>
                        </select>
                    </div>
                </div>
                <div class="field">
                    <label for="safety">Seguridad</label>
                    <textarea id="safety" name="safety" rows="2" maxlength="600" placeholder="Qué precauciones hay que tomar antes de repetir esta reparación."><?= e($value('safety')) ?></textarea>
                </div>
                <div class="field">
                    <label for="tips">Consejos y advertencias</label>
                    <textarea id="tips" name="tips" rows="3" maxlength="1200" placeholder="Ej. No soples la línea con la bomba conectada; el sedimento llega al riel."><?= e($value('tips')) ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-head">
                    <h2>Fotos</h2>
                    <p>Hasta 4 imágenes JPG, PNG o WebP, máximo 8 MB cada una. Evita placas, caras y datos personales.</p>
                </div>
                <div class="field">
                    <label for="images">Agregar imágenes</label>
                    <input type="file" id="images" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
                </div>
                <?php if ($images !== []): ?>
                    <div class="image-previews">
                        <?php foreach ($images as $image): ?>
                            <figure>
                                <img src="<?= e($image['url']) ?>" alt="Imagen actual del caso" loading="lazy">
                            </figure>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit"><?= icon('check', 'icon icon-sm') ?> Guardar borrador</button>
                <a class="btn btn-ghost" href="<?= e($isEdit ? url('/taller/' . $case['slug']) : url('/taller')) ?>">Cancelar</a>
            </div>
            <p class="field-help">Al guardar queda como borrador. Cuando esté completo, envíalo a revisión desde tu panel. Necesitas cuenta verificada para publicar.</p>
        </form>

        <aside class="form-sidebar">
            <div class="aside-card">
                <h3>Qué hace útil un caso</h3>
                <ul class="check-list">
                    <li><?= icon('check', 'icon icon-sm') ?> <span>Síntoma con condiciones: frío/caliente, carga, horas, clima.</span></li>
                    <li><?= icon('check', 'icon icon-sm') ?> <span>Prueba que confirmó la causa, no solo la sospecha.</span></li>
                    <li><?= icon('check', 'icon icon-sm') ?> <span>Número de parte o equivalente de la refacción.</span></li>
                    <li><?= icon('check', 'icon icon-sm') ?> <span>Costo real y tiempo, aunque salió mal.</span></li>
                    <li><?= icon('check', 'icon icon-sm') ?> <span>Qué harías distinto la próxima vez.</span></li>
                </ul>
            </div>
            <div class="aside-card">
                <h3>Revisión editorial</h3>
                <p>Un editor revisa que el caso tenga coherencia técnica, datos verificables y respeto a la privacidad. Puede pedir cambios antes de publicar.</p>
                <p class="muted">Los casos no sustituyen el manual del fabricante ni una reparación certificada.</p>
            </div>
            <?php if ($isEdit && $case !== null && in_array($case['status'], ['borrador', 'cambios_solicitados'], true)): ?>
                <div class="aside-card aside-card-dark">
                    <h3>Listo para revisión</h3>
                    <p>Si ya está completo, envíalo al comité editorial.</p>
                    <form method="post" action="<?= e(url('/taller/' . $case['slug'] . '/enviar')) ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn-sand btn-block btn-sm" type="submit">Enviar a revisión</button>
                    </form>
                </div>
            <?php endif; ?>
        </aside>
    </div>
</section>
