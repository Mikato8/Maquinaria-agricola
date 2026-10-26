<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Paginator;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\ContentRepository;

final class ProviderController extends BaseController
{
    public function index(Request $request): Response
    {
        $content = new ContentRepository();
        $filters = [
            'q' => $request->string('q'),
            'type' => $request->string('type'),
            'state' => $request->string('state'),
            'brand' => $request->string('brand'),
        ];
        $page = max(1, $request->int('page', 1));
        $perPage = 24;
        $result = $content->providers($filters, $page, $perPage);
        $paginator = new Paginator($result['total'], $perPage, $page);

        $brands = \App\Core\Database::select(
            "select distinct unnest(brands) as brand from providers order by brand"
        );

        return $this->view('providers.index', [
            'title' => 'Distribuidores, refacciones y talleres de maquinaria agrícola en México | AgroMáquina MX',
            'metaDescription' => 'Directorio de distribuidores, refaccionarias, talleres, renta y financiamiento de maquinaria agrícola por estado y marca.',
            'providers' => $result['items'],
            'total' => $result['total'],
            'paginator' => $paginator,
            'filters' => $filters,
            'types' => $content->providerTypeCounts(),
            'brands' => array_map(static fn (array $row): string => (string) $row['brand'], $brands),
            'states' => state_options(),
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $content = new ContentRepository();
        $provider = $content->providerBySlug($slug);
        if ($provider === null) {
            $this->notFound('No encontramos ese proveedor.');
        }

        return $this->view('providers.show', [
            'title' => $provider['name'] . ' · ' . provider_type_label((string) $provider['type']) . ' | AgroMáquina MX',
            'metaDescription' => mb_substr($provider['name'] . ': ' . $provider['notes'], 0, 300),
            'provider' => $provider,
        ]);
    }
}
