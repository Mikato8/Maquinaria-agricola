<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Paginator;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\ContentRepository;

final class GuideController extends BaseController
{
    public function index(Request $request): Response
    {
        $content = new ContentRepository();
        $filters = [
            'q' => $request->string('q'),
            'category' => $request->string('category'),
            'level' => $request->string('level'),
        ];
        $page = max(1, $request->int('page', 1));
        $perPage = (int) config('app.pagination.guides', 9);
        $result = $content->guides($filters, $page, $perPage);
        $paginator = new Paginator($result['total'], $perPage, $page);

        return $this->view('guides.index', [
            'title' => 'Guías de maquinaria agrícola, mantenimiento y precisión | AgroMáquina MX',
            'metaDescription' => 'Guías largas y verificables: cómo elegir tractor, labranza, mantenimiento por horas, costos, ISOBUS, RTK y agricultura de precisión de bajo costo en México.',
            'guides' => $result['items'],
            'total' => $result['total'],
            'paginator' => $paginator,
            'filters' => $filters,
            'categories' => category_config('guide_categories'),
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $content = new ContentRepository();
        $guide = $content->guideBySlug($slug);
        if ($guide === null) {
            $this->notFound('No encontramos esa guía.');
        }

        $related = $content->relatedGuides((string) $guide['category'], (int) $guide['id'], 3);
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $guide['title'],
            'description' => $guide['summary'],
            'author' => ['@type' => 'Organization', 'name' => $guide['author']],
            'datePublished' => date('c', strtotime((string) $guide['published_at'])),
            'dateModified' => date('c', strtotime((string) $guide['updated_at'])),
            'inLanguage' => 'es-MX',
        ];

        return $this->view('guides.show', [
            'title' => $guide['title'] . ' | AgroMáquina MX',
            'metaDescription' => mb_substr((string) $guide['summary'], 0, 300),
            'guide' => $guide,
            'related' => $related,
            'jsonLd' => $jsonLd,
        ]);
    }
}
