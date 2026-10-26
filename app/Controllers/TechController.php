<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Paginator;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\ContentRepository;

final class TechController extends BaseController
{
    public function index(Request $request): Response
    {
        $content = new ContentRepository();
        $filters = [
            'q' => $request->string('q'),
            'kind' => $request->string('kind'),
            'category' => $request->string('category'),
            'license' => $request->string('license'),
        ];
        $page = max(1, $request->int('page', 1));
        $perPage = (int) config('app.pagination.tech', 18);
        $result = $content->tech($filters, $page, $perPage);
        $paginator = new Paginator($result['total'], $perPage, $page);

        return $this->view('tech.index', [
            'title' => 'Software, hardware, datos y modelos de agricultura de precisión | AgroMáquina MX',
            'metaDescription' => 'Directorio de software agrícola, guiado RTK, ISOBUS, telemetría, datasets y modelos de IA aplicables en México, con licencia, requisitos y viabilidad.',
            'resources' => $result['items'],
            'total' => $result['total'],
            'paginator' => $paginator,
            'filters' => $filters,
            'kinds' => $content->techKindCounts(),
            'categories' => category_config('tech_categories'),
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $content = new ContentRepository();
        $resource = $content->techBySlug($slug);
        if ($resource === null) {
            $this->notFound('No encontramos ese recurso tecnológico.');
        }

        $related = Database::select(
            'select * from tech_resources where category = $1 and id <> $2 order by sort_order asc limit 3',
            [$resource['category'], $resource['id']]
        );

        return $this->view('tech.show', [
            'title' => $resource['name'] . ' · ' . tech_kind_label((string) $resource['kind']) . ' para agricultura | AgroMáquina MX',
            'metaDescription' => mb_substr((string) $resource['description'], 0, 300),
            'resource' => $resource,
            'related' => $related,
        ]);
    }
}
