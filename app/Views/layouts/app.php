<?php
/** @var array<string, mixed>|null $currentUser */
/** @var array<int, array{type: string, message: string}> $flashMessages */
/** @var string $currentPath */
$headerQuery = is_string($_GET['q'] ?? null) ? (string) $_GET['q'] : '';
$navTools = [
    '/compatibilidad' => 'Compatibilidad tractor–implemento',
    '/recomendador' => 'Recomendador de implementos',
    '/costos' => 'Costo por hora y por hectárea',
    '/fallas' => 'Fallas y diagnóstico',
    '/mantenimiento' => 'Plan de mantenimiento',
    '/codigos' => 'Códigos de falla',
];
$toolsActive = false;
foreach (array_keys($navTools) as $toolPath) {
    if ($currentPath === $toolPath || str_starts_with($currentPath, $toolPath . '/')) {
        $toolsActive = true;
    }
}
?><!doctype html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'AgroMáquina MX') ?></title>
    <meta name="description" content="<?= e($metaDescription ?? '') ?>">
    <link rel="canonical" href="<?= e($canonical ?? url(current_path())) ?>">
    <meta name="robots" content="<?= e($robots ?? 'index,follow') ?>">
    <meta name="theme-color" content="#15301f">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="AgroMáquina MX">
    <meta property="og:locale" content="es_MX">
    <meta property="og:title" content="<?= e($title ?? 'AgroMáquina MX') ?>">
    <meta property="og:description" content="<?= e($metaDescription ?? '') ?>">
    <meta property="og:url" content="<?= e($canonical ?? url(current_path())) ?>">
    <meta property="og:image" content="<?= e(url('/assets/img/og-cover.png')) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="<?= e(url('/assets/img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/maquinaria.css')) ?>">
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<a class="skip-link" href="#contenido">Saltar al contenido</a>

<header class="site-header" data-header>
    <div class="container header-inner">
        <a class="brand" href="<?= e(url('/')) ?>" aria-label="AgroMáquina MX, inicio">
            <img class="brand-mark" src="<?= e(url('/assets/img/logo.svg')) ?>" alt="" width="44" height="44">
            <span class="brand-text">
                <span class="brand-name">Agro<span class="brand-accent">Máquina</span></span>
                <span class="brand-sub">Catálogo · Taller · Precisión</span>
            </span>
        </a>

        <nav class="main-nav" id="main-nav" aria-label="Navegación principal">
            <a class="<?= e(is_active('/maquinas')) ?>" href="<?= e(url('/maquinas')) ?>">Máquinas</a>
            <a class="<?= e(is_active('/implementos')) ?>" href="<?= e(url('/implementos')) ?>">Implementos</a>
            <details class="nav-group">
                <summary class="<?= $toolsActive ? 'is-active' : '' ?>">Herramientas <?= icon('chevron-down', 'icon icon-xs') ?></summary>
                <div class="nav-group-panel">
                    <?php foreach ($navTools as $navPath => $navLabel): ?>
                        <a href="<?= e(url($navPath)) ?>"><?= e($navLabel) ?></a>
                    <?php endforeach; ?>
                </div>
            </details>
            <a class="<?= e(is_active('/tecnologia')) ?>" href="<?= e(url('/tecnologia')) ?>">Tecnología</a>
            <a class="<?= e(is_active('/guias')) ?>" href="<?= e(url('/guias')) ?>">Guías</a>
            <a class="<?= e(is_active('/taller')) ?>" href="<?= e(url('/taller')) ?>">Taller</a>
            <a class="<?= e(is_active('/proveedores')) ?>" href="<?= e(url('/proveedores')) ?>">Proveedores</a>
            <div class="mobile-account">
                <?php if ($currentUser): ?>
                    <a class="btn btn-ghost btn-block" href="<?= e(url('/mi-panel')) ?>">Mi panel</a>
                    <a class="btn btn-ghost btn-block" href="<?= e(url('/perfil')) ?>">Mi perfil</a>
                    <?php if (can('moderar')): ?>
                        <a class="btn btn-dark btn-block" href="<?= e(url('/admin')) ?>">Panel editorial</a>
                    <?php endif; ?>
                    <form method="post" action="<?= e(url('/salir')) ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn-ghost btn-block" type="submit">Cerrar sesión</button>
                    </form>
                <?php else: ?>
                    <a class="btn btn-ghost btn-block" href="<?= e(url('/acceder')) ?>">Acceder</a>
                    <a class="btn btn-primary btn-block" href="<?= e(url('/registro')) ?>">Crear cuenta</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="header-actions">
            <form class="header-search" action="<?= e(url('/buscar')) ?>" method="get" role="search">
                <?= icon('search', 'icon icon-sm') ?>
                <input type="search" name="q" value="<?= e($currentPath === '/buscar' ? $headerQuery : '') ?>" placeholder="Marca, modelo, falla, código…" aria-label="Buscar en el portal">
            </form>

            <?php if ($currentUser): ?>
                <a class="header-link" href="<?= e(url('/mi-panel')) ?>">Mi panel</a>
                <details class="user-menu">
                    <summary>
                        <?= avatar_tag((string) $currentUser['name'], $currentUser['avatar_url'] ?? null, 'avatar-sm') ?>
                        <span class="user-menu-name"><?= e(first_name((string) $currentUser['name'])) ?></span>
                        <?= icon('chevron-down', 'icon icon-xs') ?>
                    </summary>
                    <div class="user-menu-panel">
                        <div class="user-menu-head">
                            <strong><?= e($currentUser['name']) ?></strong>
                            <span><?= e($currentUser['email']) ?></span>
                            <span class="badge badge-<?= $currentUser['status'] === 'verificado' ? 'success' : 'warning' ?>"><?= $currentUser['status'] === 'verificado' ? 'Verificado' : 'Por verificar' ?></span>
                        </div>
                        <a href="<?= e(url('/mi-panel')) ?>"><?= icon('dashboard') ?> Mi panel</a>
                        <a href="<?= e(url('/perfil')) ?>"><?= icon('user') ?> Mi perfil</a>
                        <a href="<?= e(url('/taller/nuevo')) ?>"><?= icon('wrench') ?> Documentar una reparación</a>
                        <?php if (can('moderar')): ?>
                            <a href="<?= e(url('/admin')) ?>"><?= icon('shield') ?> Panel editorial</a>
                        <?php endif; ?>
                        <form method="post" action="<?= e(url('/salir')) ?>">
                            <?= csrf_field() ?>
                            <button type="submit"><?= icon('logout') ?> Cerrar sesión</button>
                        </form>
                    </div>
                </details>
            <?php else: ?>
                <a class="header-link" href="<?= e(url('/acceder')) ?>">Acceder</a>
                <a class="btn btn-primary btn-sm" href="<?= e(url('/registro')) ?>">Crear cuenta</a>
            <?php endif; ?>
        </div>

        <button class="menu-toggle" type="button" data-menu-toggle aria-controls="main-nav" aria-expanded="false" aria-label="Abrir menú">
            <span class="menu-toggle-open"><?= icon('menu') ?></span>
            <span class="menu-toggle-close"><?= icon('close') ?></span>
        </button>
    </div>
</header>

<?php \App\Core\View::partial('partials/flash', ['flashMessages' => $flashMessages]); ?>

<main id="contenido" class="site-main">
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a class="brand brand-light" href="<?= e(url('/')) ?>">
                <img class="brand-mark" src="<?= e(url('/assets/img/logo.svg')) ?>" alt="" width="44" height="44">
                <span class="brand-text">
                    <span class="brand-name">Agro<span class="brand-accent">Máquina</span></span>
                    <span class="brand-sub">MX</span>
                </span>
            </a>
            <p>Base técnica abierta sobre tractores, implementos, reparación y agricultura de precisión para México. Fichas verificables, cálculos de ingeniería y experiencia de taller compartida.</p>
            <span class="footer-stamp">Hecho en México · Español · Sin registro para consultar</span>
        </div>

        <div class="footer-col">
            <h2>Máquinas</h2>
            <a href="<?= e(url('/maquinas?category=tractor')) ?>">Tractores</a>
            <a href="<?= e(url('/maquinas?category=cosechadora')) ?>">Cosechadoras</a>
            <a href="<?= e(url('/maquinas?category=pulverizadora')) ?>">Pulverizadoras</a>
            <a href="<?= e(url('/implementos')) ?>">Implementos</a>
            <a href="<?= e(url('/maquinas/comparar')) ?>">Comparador</a>
        </div>

        <div class="footer-col">
            <h2>Taller</h2>
            <a href="<?= e(url('/fallas')) ?>">Fallas y diagnóstico</a>
            <a href="<?= e(url('/mantenimiento')) ?>">Mantenimiento por horas</a>
            <a href="<?= e(url('/codigos')) ?>">Códigos de falla</a>
            <a href="<?= e(url('/taller')) ?>">Casos de taller</a>
            <a href="<?= e(url('/guias')) ?>">Guías técnicas</a>
        </div>

        <div class="footer-col">
            <h2>Precisión</h2>
            <a href="<?= e(url('/tecnologia?kind=software')) ?>">Software agrícola</a>
            <a href="<?= e(url('/tecnologia?kind=dataset')) ?>">Datos y modelos</a>
            <a href="<?= e(url('/tecnologia?kind=hardware')) ?>">Hardware y RTK</a>
            <a href="<?= e(url('/compatibilidad')) ?>">Compatibilidad</a>
            <a href="<?= e(url('/costos')) ?>">Costos de operación</a>
        </div>

        <div class="footer-col">
            <h2>Portal</h2>
            <a href="<?= e(url('/nosotros')) ?>">Nosotros</a>
            <a href="<?= e(url('/fuentes')) ?>">Fuentes y método</a>
            <a href="<?= e(url('/glosario')) ?>">Glosario</a>
            <a href="<?= e(url('/contacto')) ?>">Contacto</a>
            <a href="<?= e(url('/privacidad')) ?>">Privacidad</a>
        </div>
    </div>

    <div class="container footer-bottom">
        <p>© <?= date('Y') ?> AgroMáquina MX. Contenido educativo y de referencia; no sustituye el manual del fabricante ni la valoración de un técnico certificado.</p>
        <p class="footer-legal"><a href="<?= e(url('/terminos')) ?>">Términos</a> · <a href="<?= e(url('/licencia')) ?>">Licencia</a> · <a href="<?= e(url('/fuentes')) ?>">Metodología</a></p>
    </div>
</footer>

<script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
<script src="<?= e(asset('assets/js/maquinaria.js')) ?>" defer></script>
</body>
</html>
