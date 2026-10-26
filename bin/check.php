<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

$ok = true;
$check = static function (string $label, bool $value, string $detail = '') use (&$ok): void {
    $ok = $ok && $value;
    printf("[%s] %-30s %s\n", $value ? 'OK' : '!!', $label, $detail);
};

$check('PHP 8.2+', version_compare(PHP_VERSION, '8.2.0', '>='), PHP_VERSION);
foreach (['pdo_pgsql', 'curl', 'mbstring', 'fileinfo', 'gd', 'intl'] as $extension) {
    $check('Extensión ' . $extension, extension_loaded($extension));
}
$check('storage escribible', is_writable(BASE_PATH . '/storage'), BASE_PATH . '/storage');
$check('sesiones escribible', is_writable(BASE_PATH . '/storage/sessions'), BASE_PATH . '/storage/sessions');
$check('uploads escribible', is_writable(BASE_PATH . '/public/uploads'), BASE_PATH . '/public/uploads');
$check('.env presente', is_file(BASE_PATH . '/.env'));

$connected = Database::ping();
$check('Conexión PostgreSQL', $connected, $connected ? Database::driverName() : 'sin conexión');

if ($connected) {
    $migrations = (int) Database::value("select count(*) from information_schema.tables where table_schema = 'public' and table_name = 'schema_migrations'");
    $applied = $migrations ? (int) Database::value('select count(*) from schema_migrations') : 0;
    $check('Migraciones aplicadas', $applied > 0, (string) $applied);

    foreach (['machines', 'implements', 'faults', 'maintenance_tasks', 'dtc_codes', 'tech_resources', 'guides', 'providers', 'glossary'] as $table) {
        $count = (int) Database::value("select count(*) from {$table}");
        $check('Tabla ' . $table, $count > 0, $count . ' registros');
    }

    $published = (int) Database::value("select count(*) from workshop_cases where status = 'publicado'");
    $check('Casos de taller publicados', $published > 0, (string) $published);

    $admin = (int) Database::value("select count(*) from users where role = 'admin' and deleted_at is null");
    $check('Cuenta administradora', $admin > 0, (string) $admin);

    $search = (int) Database::value("select count(*) from machines where search_vector @@ plainto_tsquery('spanish', 'tractor')");
    $check('Búsqueda full-text', $search > 0, $search . ' máquinas coinciden con «tractor»');
}

echo $ok ? "\nTodo listo.\n" : "\nHay comprobaciones pendientes.\n";
exit($ok ? 0 : 1);
