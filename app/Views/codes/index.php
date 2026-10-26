<?php
/** @var array<int, array<string, mixed>> $codes */
/** @var string $q */
/** @var string $system */
/** @var array<string, string> $systems */
?><section class="page-hero page-hero-compact">
    <div class="container">
        <nav class="breadcrumb" aria-label="Ruta">
            <a href="<?= e(url('/')) ?>">Inicio</a>
            <span aria-current="page">Códigos de falla</span>
        </nav>
        <span class="eyebrow"><span class="eyebrow-dot"></span> Electrónica y diagnóstico</span>
        <h1>Leer el código es el principio, no el diagnóstico.</h1>
        <p class="page-hero-lead">
            Tabla de referencia SPN/FMI del estándar J1939, el que usan la mayoría de tractores y cosechadoras.
            Cada fabricante puede cambiar el significado exacto: confirma siempre en su manual de servicio.
        </p>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="notice notice-warning">
            <?= icon('alert-triangle', 'icon icon-sm') ?>
            <p><strong>Lectura orientativa.</strong> Un código indica el circuito o la condición que el controlador detectó, no la pieza que hay que cambiar. Antes de sustituir sensores, revisa arnés, conectores, tierra y presión real con manómetro.</p>
        </div>

        <div class="code-explainer">
            <div class="code-explainer-item">
                <strong>SPN</strong>
                <p>Suspect Parameter Number: qué se está midiendo (presión de aceite, temperatura, régimen, etc.).</p>
            </div>
            <div class="code-explainer-item">
                <strong>FMI</strong>
                <p>Failure Mode Identifier: cómo falla (señal alta, baja, fuera de rango, intermitente, sin respuesta).</p>
            </div>
            <div class="code-explainer-item">
                <strong>Ejemplo</strong>
                <p><span class="formula-inline">SPN 100 · FMI 1</span> = presión de aceite del motor por debajo de lo esperado. Detén la máquina y verifica con manómetro.</p>
            </div>
        </div>

        <form class="filter-panel" method="get" action="<?= e(url('/codigos')) ?>">
            <div class="field-grid field-grid-3">
                <div class="field">
                    <label for="q">Buscar código o descripción</label>
                    <input type="search" id="q" name="q" value="<?= e($q) ?>" placeholder="Ej. SPN 100, temperatura, combustible">
                </div>
                <div class="field">
                    <label for="system">Sistema</label>
                    <select id="system" name="system">
                        <option value="">Todos</option>
                        <?php foreach ($systems as $code => $label): ?>
                            <option value="<?= e($code) ?>" <?= $system === $code ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary" type="submit"><?= icon('search', 'icon icon-sm') ?> Buscar</button>
                </div>
            </div>
        </form>

        <?php if ($codes === []): ?>
            <div class="empty-state">
                <span class="empty-icon"><?= icon('cpu') ?></span>
                <h2>Sin resultados</h2>
                <p>Prueba con el número SPN solo, o revisa todos los códigos.</p>
                <a class="btn btn-primary" href="<?= e(url('/codigos')) ?>">Ver todos</a>
            </div>
        <?php else: ?>
            <div class="table-wrap codes-table-wrap">
                <table class="data-table codes-table">
                    <thead>
                    <tr>
                        <th scope="col">Código</th>
                        <th scope="col">Sistema</th>
                        <th scope="col">Qué significa</th>
                        <th scope="col">Causas probables</th>
                        <th scope="col">Acción recomendada</th>
                        <th scope="col">Gravedad</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($codes as $code): ?>
                        <?php $causes = pg_array($code['causes'] ?? []); ?>
                        <tr>
                            <td class="code-cell">
                                <strong><?= e($code['code']) ?></strong>
                                <?php if ((string) $code['brand_scope'] !== ''): ?><small><?= e($code['brand_scope']) ?></small><?php endif; ?>
                            </td>
                            <td><?= e(fault_system_label((string) $code['system'])) ?></td>
                            <td>
                                <strong><?= e($code['title']) ?></strong>
                                <?php if ((string) $code['description'] !== ''): ?><p class="table-muted"><?= e($code['description']) ?></p><?php endif; ?>
                            </td>
                            <td>
                                <?php if ($causes === []): ?>—<?php else: ?>
                                    <ul class="mini-list">
                                        <?php foreach (array_slice($causes, 0, 3) as $cause): ?><li><?= e($cause) ?></li><?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </td>
                            <td><?= e($code['action']) ?></td>
                            <td><span class="badge badge-<?= e(severity_tone($code['severity'])) ?>"><?= e(severity_label($code['severity'])) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="calc-note"><?= count($codes) ?> códigos en la tabla. La nomenclatura SPN/FMI es del estándar SAE J1939; algunos fabricantes usan códigos propietarios (por ejemplo, Deere, CNH o AGCO) que requieren su software de diagnóstico.</p>
        <?php endif; ?>

        <div class="cta-actions">
            <a class="btn btn-primary" href="<?= e(url('/fallas?system=electronica')) ?>">Fallas electrónicas <?= icon('arrow-right', 'icon icon-sm') ?></a>
            <a class="btn btn-outline" href="<?= e(url('/guias/leer-codigos-de-falla-j1939')) ?>">Cómo leer un código J1939</a>
        </div>
    </div>
</section>
