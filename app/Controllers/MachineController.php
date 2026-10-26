<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Paginator;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\CatalogRepository;
use App\Repositories\RepairRepository;
use App\Services\MachineryMath;

final class MachineController extends BaseController
{
    public function index(Request $request): Response
    {
        $catalog = new CatalogRepository();
        $filters = [
            'q' => $request->string('q'),
            'category' => $request->string('category'),
            'brand' => $request->string('brand'),
            'traction' => $request->string('traction'),
            'min_hp' => $request->int('min_hp'),
            'max_hp' => $request->int('max_hp'),
            'price' => $request->string('price'),
            'sort' => $request->string('sort', 'relevancia'),
        ];
        $page = max(1, $request->int('page', 1));
        $perPage = (int) config('app.pagination.machines', 12);
        $result = $catalog->machines($filters, $page, $perPage);
        $paginator = new Paginator($result['total'], $perPage, $page);

        $category = $filters['category'];
        $title = $category !== ''
            ? machine_category_label($category) . ' en México · fichas técnicas y precios | AgroMáquina MX'
            : 'Máquinas agrícolas en México · tractores, cosechadoras y pulverizadoras | AgroMáquina MX';
        $description = $category !== ''
            ? 'Fichas técnicas de ' . mb_strtolower(machine_category_label($category)) . ' disponibles en México: potencia, levante, caudal hidráulico, precios de referencia y fallas frecuentes.'
            : 'Catálogo de tractores, cosechadoras, pulverizadoras y equipos agrícolas con especificaciones, precios de referencia en México y fallas documentadas.';

        return $this->view('machines.index', [
            'title' => $title,
            'metaDescription' => $description,
            'machines' => $result['items'],
            'total' => $result['total'],
            'paginator' => $paginator,
            'filters' => $filters,
            'brands' => $catalog->brandsWithCounts(),
            'categories' => category_config('machine_categories'),
            'bodyClass' => 'machines-page',
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $catalog = new CatalogRepository();
        $repair = new RepairRepository();
        $machine = $catalog->machineBySlug($slug);
        if ($machine === null) {
            $this->notFound('No encontramos esa máquina. Puede haber cambiado de nombre o estar fuera del catálogo.');
        }

        $catalog->incrementViews('machines', (int) $machine['id']);

        $power = (int) round((float) $machine['power_hp']);
        $related = $catalog->relatedMachines((string) $machine['category'], (int) $machine['id'], 3);
        $implements = $power > 0 ? $catalog->implementsForPower($power, 6) : [];
        $faults = $repair->faultsForCategory((string) $machine['category'], 5);

        $title = $machine['brand_name'] . ' ' . $machine['model'] . ' · ficha técnica, precio y fallas | AgroMáquina MX';
        $description = mb_substr(
            trim(($machine['summary'] ?: $machine['description']) . ' Potencia ' . hp_label($machine['power_hp']) . '. Precio de referencia en México: ' . price_range($machine['price_min_mxn'], $machine['price_max_mxn']) . '.'),
            0,
            300
        );

        return $this->view('machines.show', [
            'title' => $title,
            'metaDescription' => $description,
            'machine' => $machine,
            'related' => $related,
            'implements' => $implements,
            'faults' => $faults,
            'bodyClass' => 'machine-detail',
        ]);
    }

    public function compare(Request $request): Response
    {
        $catalog = new CatalogRepository();
        $ids = $request->query('ids');
        $ids = is_array($ids) ? array_map('intval', $ids) : [];
        $machines = $catalog->machineByIds($ids);

        $available = $catalog->machines(['sort' => 'potencia_desc'], 1, 24)['items'];

        return $this->view('machines.compare', [
            'title' => 'Comparar tractores y máquinas agrícolas | AgroMáquina MX',
            'metaDescription' => 'Compara hasta tres máquinas por potencia, levante hidráulico, caudal, peso, transmisión y precio de referencia en México.',
            'machines' => $machines,
            'available' => $available,
            'bodyClass' => 'compare-page',
        ]);
    }

    public function pdf(Request $request, string $slug): Response
    {
        $catalog = new CatalogRepository();
        $machine = $catalog->machineBySlug($slug);
        if ($machine === null) {
            $this->notFound();
        }

        $compat = null;
        if ($machine['category'] === 'tractor' && $machine['power_hp'] !== null) {
            $implements = $catalog->implementsForPower((int) $machine['power_hp'], 4);
            $compat = array_map(static function (array $implement) use ($machine): array {
                return [
                    'implement' => $implement,
                    'result' => MachineryMath::compatibility($machine, $implement, [
                        'soil' => 'franco',
                        'moisture' => 'optimo',
                        'speed_kmh' => (float) ($implement['category'] === 'labranza' ? 6.5 : 8.0),
                    ]),
                ];
            }, $implements);
        }

        $pdf = new \App\Support\MachineSheet($machine, $compat);
        $binary = $pdf->render();

        return Response::html('', 200)->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'inline; filename="ficha-' . $slug . '.pdf"')
            ->withHeader('Content-Length', (string) strlen($binary))
            ->withBody($binary);
    }
}
