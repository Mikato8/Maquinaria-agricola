<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\RepairRepository;

final class CodeController extends BaseController
{
    public function index(Request $request): Response
    {
        $repair = new RepairRepository();
        $q = $request->string('q');
        $system = $request->string('system');
        $codes = $repair->dtcCodes($q, $system, 300);

        return $this->view('codes.index', [
            'title' => 'Códigos de falla SPN/FMI en maquinaria agrícola | AgroMáquina MX',
            'metaDescription' => 'Tabla de códigos de falla SPN/FMI usados en tractores y cosechadoras: significado, causas probables, acción recomendada y gravedad. Lectura orientativa.',
            'codes' => $codes,
            'q' => $q,
            'system' => $system,
            'systems' => category_config('fault_systems'),
        ]);
    }
}
