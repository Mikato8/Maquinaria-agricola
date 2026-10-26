<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Paginator;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\CatalogRepository;
use App\Services\MachineryMath;

final class ImplementController extends BaseController
{
    public function index(Request $request): Response
    {
        $catalog = new CatalogRepository();
        $filters = [
            'q' => $request->string('q'),
            'category' => $request->string('category'),
            'tractor_hp' => $request->int('tractor_hp'),
            'budget' => $request->int('budget'),
            'sort' => $request->string('sort', 'relevancia'),
        ];
        $page = max(1, $request->int('page', 1));
        $perPage = (int) config('app.pagination.implements', 12);
        $result = $catalog->implements($filters, $page, $perPage);
        $paginator = new Paginator($result['total'], $perPage, $page);

        return $this->view('implements.index', [
            'title' => 'Implementos agrícolas en México · labranza, siembra, protección y cosecha | AgroMáquina MX',
            'metaDescription' => 'Fichas de arados, rastras, sembradoras, aspersoras, empacadoras y remolques: potencia requerida, ancho de trabajo, precio de referencia y calibración.',
            'implements' => $result['items'],
            'total' => $result['total'],
            'paginator' => $paginator,
            'filters' => $filters,
            'categories' => category_config('implement_categories'),
            'counts' => $catalog->implementCounts(),
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $catalog = new CatalogRepository();
        $implement = $catalog->implementBySlug($slug);
        if ($implement === null) {
            $this->notFound('No encontramos ese implemento en el catálogo.');
        }

        $catalog->incrementViews('implements', (int) $implement['id']);

        $minHp = (int) ($implement['power_min_hp'] ?? 0);
        $maxHp = (int) ($implement['power_max_hp'] ?? 0);
        $tractors = $catalog->machinesForImplement($minHp, $maxHp, 6);
        $related = $catalog->relatedImplements((string) $implement['category'], (int) $implement['id'], 3);

        $example = null;
        if ($tractors !== []) {
            $tractor = $tractors[0];
            $example = [
                'tractor' => $tractor,
                'result' => MachineryMath::compatibility($tractor, $implement, [
                    'soil' => 'franco',
                    'moisture' => 'optimo',
                    'speed_kmh' => (string) $implement['category'] === 'labranza' ? 6.5 : 8.0,
                ]),
            ];
        }

        return $this->view('implements.show', [
            'title' => $implement['name'] . ' · ficha técnica, precio y calibración | AgroMáquina MX',
            'metaDescription' => mb_substr(
                'Ficha de ' . $implement['name'] . ': ' . ($implement['summary'] ?: $implement['description']) . ' Potencia requerida ' . power_range($implement['power_min_hp'], $implement['power_max_hp']) . '.',
                0,
                300
            ),
            'implement' => $implement,
            'tractors' => $tractors,
            'related' => $related,
            'example' => $example,
        ]);
    }
}
