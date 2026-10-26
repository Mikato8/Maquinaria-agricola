<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

final class SearchController extends BaseController
{
    public function index(Request $request): Response
    {
        $q = trim($request->string('q'));
        $type = $request->string('tipo');
        $results = [];
        $total = 0;

        if (mb_strlen($q) >= 2) {
            $like = '%' . $q . '%';
            $matches = [
                'maquinas' => [
                    'label' => 'Máquinas', 'base' => '/maquinas/',
                    'sql' => "select id, slug, name, summary as description, brand_name as extra, '/maquinas/' as base
                              from (select m.id, m.slug, m.name, m.summary, b.name as brand_name from machines m join brands b on b.id = m.brand_id where m.status = 'publicado') m
                              where name ilike \$1 or brand_name ilike \$1 or summary ilike \$1 order by name limit 12",
                ],
                'implementos' => [
                    'label' => 'Implementos', 'base' => '/implementos/',
                    'sql' => "select id, slug, name, function as description, category as extra, '/implementos/' as base
                              from implements where status = 'publicado' and (name ilike \$1 or function ilike \$1 or description ilike \$1) order by name limit 12",
                ],
                'fallas' => [
                    'label' => 'Fallas', 'base' => '/fallas/',
                    'sql' => "select id, slug, title as name, symptom as description, system as extra, '/fallas/' as base
                              from faults where status = 'publicado' and (title ilike \$1 or symptom ilike \$1 or prevention ilike \$1) order by views desc limit 12",
                ],
                'guias' => [
                    'label' => 'Guías', 'base' => '/guias/',
                    'sql' => "select id, slug, title as name, summary as description, category as extra, '/guias/' as base
                              from guides where title ilike \$1 or summary ilike \$1 or body ilike \$1 order by published_at desc limit 12",
                ],
                'tecnologia' => [
                    'label' => 'Tecnología y datos', 'base' => '/tecnologia/',
                    'sql' => "select id, slug, name, description, kind as extra, '/tecnologia/' as base
                              from tech_resources where name ilike \$1 or description ilike \$1 or vendor ilike \$1 order by name limit 12",
                ],
                'taller' => [
                    'label' => 'Casos de taller', 'base' => '/taller/',
                    'sql' => "select id, slug, title as name, summary as description, machine_label as extra, '/taller/' as base
                              from workshop_cases where status = 'publicado' and (title ilike \$1 or symptom ilike \$1 or repair ilike \$1 or machine_label ilike \$1) order by published_at desc limit 12",
                ],
                'proveedores' => [
                    'label' => 'Proveedores', 'base' => '/proveedores/',
                    'sql' => "select id, slug, name, notes as description, city as extra, '/proveedores/' as base
                              from providers where name ilike \$1 or notes ilike \$1 or city ilike \$1 order by name limit 12",
                ],
            ];

            foreach ($matches as $key => $config) {
                if ($type !== '' && $type !== $key) {
                    continue;
                }
                $rows = Database::select($config['sql'], [$like]);
                $total += count($rows);
                if ($rows !== []) {
                    $results[$key] = [
                        'label' => $config['label'],
                        'items' => array_map(static function (array $row) use ($config): array {
                            $row['url'] = url($config['base'] . $row['slug']);
                            return $row;
                        }, $rows),
                    ];
                }
            }
        }

        return $this->view('search.index', [
            'title' => ($q !== '' ? 'Buscar: ' . $q . ' · ' : 'Buscar · ') . 'AgroMáquina MX',
            'metaDescription' => 'Busca máquinas, implementos, fallas, guías, tecnología, casos de taller y proveedores de maquinaria agrícola en México.',
            'q' => $q,
            'type' => $type,
            'results' => $results,
            'total' => $total,
            'robots' => 'noindex,follow',
        ]);
    }
}
