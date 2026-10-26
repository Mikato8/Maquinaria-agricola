<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\ContentRepository;

final class GlossaryController extends BaseController
{
    public function index(Request $request): Response
    {
        $content = new ContentRepository();
        $q = $request->string('q');
        $category = $request->string('category');
        $terms = $content->glossary($q, $category);

        return $this->view('glossary.index', [
            'title' => 'Glosario de maquinaria agrícola y agricultura de precisión | AgroMáquina MX',
            'metaDescription' => 'Glosario en español de términos de maquinaria agrícola, labranza, hidráulica, ISOBUS, RTK, telemetría y agricultura de precisión.',
            'terms' => $terms,
            'q' => $q,
            'category' => $category,
            'categories' => $content->glossaryCategories(),
        ]);
    }
}
