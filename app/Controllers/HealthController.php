<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

final class HealthController extends BaseController
{
    public function index(Request $request): Response
    {
        $checks = [];
        $ok = true;

        $checks[] = ['label' => 'PHP', 'value' => PHP_VERSION, 'ok' => version_compare(PHP_VERSION, '8.2.0', '>=')];
        foreach (['pdo_pgsql' => 'PostgreSQL (PDO)', 'curl' => 'cURL', 'mbstring' => 'mbstring', 'gd' => 'GD'] as $ext => $label) {
            $checks[] = ['label' => 'Extensión ' . $label, 'value' => $ext, 'ok' => extension_loaded($ext)];
        }

        $connected = false;
        $driver = '—';
        try {
            $connected = Database::ping();
            $driver = Database::driverName();
        } catch (\Throwable) {
            $driver = 'sin conexión';
        }
        $checks[] = ['label' => 'Base de datos', 'value' => $driver, 'ok' => $connected];

        $migrations = 0;
        $counts = [];
        if ($connected) {
            $migrations = (int) Database::value('select count(*) from schema_migrations');
            foreach (['machines', 'implements', 'faults', 'tech_resources', 'guides', 'workshop_cases'] as $table) {
                $counts[$table] = (int) Database::value('select count(*) from ' . $table);
            }
        }

        foreach ($checks as $check) {
            $ok = $ok && $check['ok'];
        }

        if ($request->wantsJson()) {
            return Response::json([
                'ok' => $ok,
                'php' => PHP_VERSION,
                'database' => $connected,
                'driver' => $driver,
                'migrations' => $migrations,
                'counts' => $counts,
            ], $ok ? 200 : 503);
        }

        $response = $this->view('pages/health', [
            'title' => 'Estado del sistema · AgroMáquina MX',
            'metaDescription' => 'Comprobación técnica del portal.',
            'checks' => $checks,
            'ok' => $ok,
            'counts' => $counts,
            'migrations' => $migrations,
            'robots' => 'noindex,nofollow',
        ]);

        return $ok ? $response : $response->withStatus(503);
    }
}
