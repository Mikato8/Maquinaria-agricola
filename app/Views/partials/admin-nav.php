<?php
$path = current_path();
$links = [
    '/admin' => 'Resumen',
    '/admin/casos' => 'Casos de taller',
    '/admin/usuarios' => 'Cuentas',
    '/admin/comentarios' => 'Comentarios',
    '/admin/mensajes' => 'Mensajes',
    '/admin/auditoria' => 'Auditoría',
];
?><nav class="admin-tabs no-print" aria-label="Panel editorial">
    <?php foreach ($links as $href => $label): ?>
        <a href="<?= e(url($href)) ?>" class="<?= $path === $href ? 'is-active' : '' ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
</nav>
