<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Paginator;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\CatalogRepository;
use App\Repositories\RepairRepository;

final class FaultController extends BaseController
{
    public function index(Request $request): Response
    {
        $repair = new RepairRepository();
        $filters = [
            'q' => $request->string('q'),
            'system' => $request->string('system'),
            'category' => $request->string('category'),
            'severity' => $request->string('severity'),
            'sort' => $request->string('sort', 'relevancia'),
        ];
        $page = max(1, $request->int('page', 1));
        $perPage = (int) config('app.pagination.faults', 12);
        $result = $repair->faults($filters, $page, $perPage);
        $paginator = new Paginator($result['total'], $perPage, $page);

        return $this->view('faults.index', [
            'title' => 'Fallas de maquinaria agrícola · diagnóstico paso a paso | AgroMáquina MX',
            'metaDescription' => 'Fichas de fallas de tractores, cosechadoras e implementos: síntomas, causas probables, pruebas de diagnóstico, refacciones, costo y prevención.',
            'faults' => $result['items'],
            'total' => $result['total'],
            'paginator' => $paginator,
            'filters' => $filters,
            'systems' => $repair->systemCounts(),
            'categories' => category_config('machine_categories'),
            'codes' => (int) \App\Core\Database::value('select count(*) from dtc_codes'),
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $repair = new RepairRepository();
        $catalog = new CatalogRepository();
        $fault = $repair->faultBySlug($slug);
        if ($fault === null) {
            $this->notFound('No encontramos esa falla. Revisa el índice de fallas o busca por síntoma.');
        }

        $catalog->incrementViews('faults', (int) $fault['id']);

        $causes = decode_json_list($fault['causes']);
        $steps = decode_json_list($fault['steps']);
        $tools = pg_array($fault['tools'] ?? []);
        $parts = pg_array($fault['parts'] ?? []);
        $related = $repair->relatedFaults((string) $fault['system'], (int) $fault['id'], 4);

        return $this->view('faults.show', [
            'title' => $fault['title'] . ' · diagnóstico y reparación | AgroMáquina MX',
            'metaDescription' => mb_substr('Síntoma: ' . $fault['symptom'] . ' Causas, pasos de diagnóstico y prevención para ' . mb_strtolower(machine_category_singular((string) $fault['machine_category'])) . '.', 0, 300),
            'fault' => $fault,
            'causes' => $causes,
            'steps' => $steps,
            'tools' => $tools,
            'parts' => $parts,
            'related' => $related,
        ]);
    }
}
