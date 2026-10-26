<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\RepairRepository;

final class MaintenanceController extends BaseController
{
    public function index(Request $request): Response
    {
        $repair = new RepairRepository();
        $allowed = ['tractor', 'cosechadora', 'pulverizadora', 'forrajera', 'motocultor', 'tractor_compacto'];
        $category = $request->string('categoria', 'tractor');
        if (!in_array($category, $allowed, true)) {
            $category = 'tractor';
        }

        $plan = $repair->maintenancePlan($category);
        $labels = [
            'tractor' => 'Tractor agrícola',
            'cosechadora' => 'Cosechadora',
            'pulverizadora' => 'Pulverizadora',
            'forrajera' => 'Ensiladora o forrajera',
            'motocultor' => 'Motocultor',
            'tractor_compacto' => 'Tractor compacto',
        ];

        return $this->view('maintenance.index', [
            'title' => 'Plan de mantenimiento por horas para maquinaria agrícola | AgroMáquina MX',
            'metaDescription' => 'Checklist de mantenimiento a 50, 250, 500, 1000 y 2000 horas para tractores, cosechadoras, pulverizadoras y equipos compactos. Imprimible y gratuito.',
            'plan' => $plan,
            'category' => $category,
            'categoryLabel' => $labels[$category] ?? 'Tractor agrícola',
            'categories' => $labels,
            'systems' => $repair->maintenanceSystems($category),
        ]);
    }
}
