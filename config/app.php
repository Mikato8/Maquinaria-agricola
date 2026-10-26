<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'AgroMáquina MX'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env_bool('APP_DEBUG', false),
    'url' => rtrim(env('APP_URL', 'http://127.0.0.1:8080'), '/'),
    'timezone' => env('APP_TIMEZONE', 'America/Mexico_City'),
    'locale' => env('APP_LOCALE', 'es_MX'),
    'key' => env('APP_KEY', ''),
    'auto_verify_users' => env_bool('AUTO_VERIFY_USERS', false),

    'db' => [
        'url' => env('DATABASE_URL', ''),
        'unpooled_url' => env('DATABASE_URL_UNPOOLED', ''),
        'driver' => env('DB_DRIVER', 'auto'), // auto | http | pdo
    ],

    'session' => [
        'name' => env('SESSION_NAME', 'agromaquina_session'),
        'lifetime' => (int) env('SESSION_LIFETIME', '7200'),
        'secure' => env_bool('SESSION_SECURE', str_starts_with((string) env('APP_URL', ''), 'https://')),
    ],

    'uploads' => [
        'images_path' => BASE_PATH . '/public/uploads/taller',
        'images_url' => '/uploads/taller',
        'avatars_path' => BASE_PATH . '/public/uploads',
        'avatars_url' => '/uploads',
        'proofs_path' => BASE_PATH . '/storage/constancias',
        'max_bytes' => (int) env('UPLOAD_MAX_BYTES', (string) (8 * 1024 * 1024)),
        'max_files' => (int) env('UPLOAD_MAX_FILES', '4'),
        'max_document_bytes' => (int) env('UPLOAD_MAX_DOCUMENT_BYTES', (string) (12 * 1024 * 1024)),
    ],

    'mail' => [
        'enabled' => env_bool('MAIL_ENABLED', false),
        'from' => env('MAIL_FROM', 'no-reply@agromaquina.mx'),
        'from_name' => env('MAIL_FROM_NAME', 'AgroMáquina MX'),
    ],

    'pagination' => [
        'machines' => 12,
        'implements' => 12,
        'faults' => 12,
        'workshop' => 9,
        'tech' => 18,
        'guides' => 9,
        'search' => 10,
        'admin' => 20,
    ],

    'roles' => [
        'productor' => 'Productor',
        'tecnico' => 'Técnico o mecánico',
        'taller' => 'Taller o empresa de servicio',
        'editor' => 'Editor técnico',
        'admin' => 'Administrador',
    ],

    'moderators' => ['editor', 'admin'],

    'machine_categories' => [
        'tractor' => ['name' => 'Tractores', 'emoji' => '🚜', 'singular' => 'Tractor'],
        'cosechadora' => ['name' => 'Cosechadoras', 'emoji' => '🌾', 'singular' => 'Cosechadora'],
        'pulverizadora' => ['name' => 'Pulverizadoras', 'emoji' => '💧', 'singular' => 'Pulverizadora'],
        'forrajera' => ['name' => 'Forrajeras y ensiladoras', 'emoji' => '🌽', 'singular' => 'Forrajera'],
        'motocultor' => ['name' => 'Motocultores', 'emoji' => '⚙️', 'singular' => 'Motocultor'],
        'ordeno' => ['name' => 'Ordeño', 'emoji' => '🥛', 'singular' => 'Equipo de ordeño'],
        'riego' => ['name' => 'Riego y bombeo', 'emoji' => '🚿', 'singular' => 'Equipo de riego'],
        'transporte' => ['name' => 'Transporte', 'emoji' => '🚛', 'singular' => 'Transporte'],
    ],

    'implement_categories' => [
        'labranza' => ['name' => 'Labranza', 'emoji' => '🪓', 'description' => 'Arados, rastras, subsoladores y cultivadoras.'],
        'siembra' => ['name' => 'Siembra y trasplante', 'emoji' => '🌱', 'description' => 'Sembradoras, abonadoras y trasplantadoras.'],
        'fertilizacion' => ['name' => 'Fertilización', 'emoji' => '🧪', 'description' => 'Distribuidores de fertilizante y encalado.'],
        'proteccion' => ['name' => 'Protección de cultivo', 'emoji' => '🛡️', 'description' => 'Aspersoras, nebulizadores y desbrozadoras.'],
        'cosecha' => ['name' => 'Cosecha', 'emoji' => '🌾', 'description' => 'Segadoras, hileradoras y plataformas.'],
        'forraje' => ['name' => 'Forraje', 'emoji' => '🌿', 'description' => 'Picadoras, empacadoras y molinos.'],
        'transporte' => ['name' => 'Transporte', 'emoji' => '🚚', 'description' => 'Remolques, góndolas y plataformas.'],
        'riego' => ['name' => 'Riego', 'emoji' => '🚿', 'description' => 'Bombas, aspersores y tubería.'],
        'postcosecha' => ['name' => 'Postcosecha', 'emoji' => '🏭', 'description' => 'Limpieza, selección y empaque.'],
        'jardin' => ['name' => 'Jardín y huerto', 'emoji' => '🌷', 'description' => 'Equipo compacto para huertos y jardines.'],
    ],

    'fault_systems' => [
        'motor' => 'Motor',
        'combustible' => 'Combustible e inyección',
        'transmision' => 'Transmisión y diferencial',
        'hidraulico' => 'Sistema hidráulico',
        'electrico' => 'Sistema eléctrico',
        'frenos' => 'Frenos',
        'direccion' => 'Dirección',
        'embrague' => 'Embrague y TDF',
        'carga' => 'Carga y arranque',
        'enfriamiento' => 'Enfriamiento',
        'aftertreatment' => 'Postratamiento de gases',
        'neumaticos' => 'Neumáticos y rodaje',
        'cabina' => 'Cabina y aire acondicionado',
        'electronica' => 'Electrónica y códigos',
        'implemento' => 'Implementos',
    ],

    'tech_kinds' => [
        'software' => 'Software',
        'hardware' => 'Hardware',
        'dataset' => 'Conjunto de datos',
        'modelo' => 'Modelo de IA',
        'espacio' => 'Demo o espacio',
        'norma' => 'Norma o protocolo',
    ],

    'tech_categories' => [
        'guiado' => 'Guiado y autopiloto',
        'telemetria' => 'Telemetría y flotas',
        'gestion' => 'Gestión y cuaderno de campo',
        'isobus' => 'ISOBUS y compatibilidad',
        'vision' => 'Visión artificial',
        'nlp' => 'Lenguaje y asistentes',
        'teledeteccion' => 'Teledetección y NDVI',
        'datos' => 'Datos y benchmarks',
        'robotica' => 'Robótica',
        'riego' => 'Riego de precisión',
        'taller' => 'Taller y mantenimiento',
    ],

    'provider_types' => [
        'fabricante' => 'Fabricante',
        'distribuidor' => 'Distribuidor',
        'refacciones' => 'Refacciones',
        'taller' => 'Taller',
        'renta' => 'Renta de maquinaria',
        'financiamiento' => 'Financiamiento',
        'capacitacion' => 'Capacitación',
    ],

    'guide_categories' => [
        'fundamentos' => 'Fundamentos',
        'mantenimiento' => 'Mantenimiento',
        'precision' => 'Agricultura de precisión',
        'compra' => 'Compra y selección',
        'seguridad' => 'Seguridad',
        'taller' => 'Taller y reparación',
    ],
];
