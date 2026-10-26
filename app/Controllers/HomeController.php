<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\CatalogRepository;
use App\Repositories\ContentRepository;
use App\Repositories\RepairRepository;
use App\Repositories\UserRepository;
use App\Repositories\WorkshopRepository;

final class HomeController extends BaseController
{
    public function index(Request $request): Response
    {
        $catalog = new CatalogRepository();
        $repair = new RepairRepository();
        $content = new ContentRepository();
        $workshop = new WorkshopRepository();
        $users = new UserRepository();

        $stats = $catalog->catalogStats();
        $stats['usuarios'] = (int) Database::value('select count(*) from users where deleted_at is null');
        $stats['codes'] = (int) Database::value('select count(*) from dtc_codes');

        return $this->view('home.index', [
            'title' => 'AgroMáquina MX · Maquinaria agrícola, taller y precisión para México',
            'metaDescription' => 'Catálogo técnico de tractores e implementos, fichas de fallas con diagnóstico paso a paso, planes de mantenimiento, calculadoras de compatibilidad y directorio de software, datos y proveedores para el campo mexicano.',
            'stats' => $stats,
            'featuredMachines' => $catalog->featuredMachines(3),
            'faults' => $repair->recentFaults(4),
            'guides' => $content->featuredGuides(3),
            'workshopCases' => $workshop->featured(2),
            'contributors' => $users->topContributors(4),
            'techHighlights' => Database::select(
                "select * from tech_resources where kind in ('software', 'dataset', 'modelo') order by sort_order asc limit 4"
            ),
            'implementCounts' => $catalog->implementCounts(),
            'brands' => Database::select('select name, slug from brands order by name limit 12'),
        ]);
    }
}
