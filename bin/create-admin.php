<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;
use App\Core\Str;

$options = getopt('', ['name::', 'email::', 'password::', 'state::', 'role::']);
$name = trim((string) ($options['name'] ?? 'Comité Editorial AgroMáquina'));
$email = Str::normalizeEmail((string) ($options['email'] ?? 'admin@agromaquina.mx'));
$password = (string) ($options['password'] ?? getenv('ADMIN_PASSWORD') ?: '');
$state = (string) ($options['state'] ?? 'CMX');
$role = (string) ($options['role'] ?? 'admin');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Correo inválido.\n");
    exit(1);
}
if (!in_array($role, ['admin', 'editor'], true)) {
    fwrite(STDERR, "El rol debe ser admin o editor.\n");
    exit(1);
}
if ($password === '') {
    fwrite(STDOUT, 'Contraseña para ' . $email . ': ');
    $password = trim((string) fgets(STDIN));
}
if (mb_strlen($password) < 12) {
    fwrite(STDERR, "La contraseña debe tener al menos 12 caracteres.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$existing = Database::value('select id from users where email = $1', [$email]);

if ($existing !== null) {
    Database::execute(
        "update users set name = $1, password_hash = $2, role = $3, status = 'verificado',
                email_verified = true, state_code = $4, deleted_at = null, verified_at = now() where id = $5",
        [$name, $hash, $role, $state, (int) $existing]
    );
    echo "✓ Cuenta actualizada: {$email} ({$role})\n";
    exit(0);
}

$row = Database::insert(
    "insert into users (name, email, password_hash, role, status, state_code, headline, bio,
                        verification_method, email_verified, verified_at, terms_accepted_at)
     values ($1, $2, $3, $4, 'verificado', $5, 'Comité editorial', 'Cuenta del comité editorial de AgroMáquina MX.', 'admin', true, now(), now())
     returning id",
    [$name, $email, $hash, $role, $state]
);

echo '✓ ' . ($role === 'admin' ? 'Administrador' : 'Editor') . ' creado con ID ' . (int) ($row['id'] ?? 0) . ": {$email}\n";
echo "  Cambia la contraseña después del primer acceso.\n";
