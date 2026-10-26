<?php

declare(strict_types=1);

// Router para el servidor de desarrollo de PHP: sirve archivos estáticos
// y envía el resto al front controller.

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . '/../public' . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/../public/index.php';
