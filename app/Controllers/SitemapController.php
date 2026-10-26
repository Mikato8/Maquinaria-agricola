<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

final class SitemapController extends BaseController
{
    public function index(Request $request): Response
    {
        $urls = [
            ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => url('/maquinas'), 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => url('/implementos'), 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => url('/fallas'), 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => url('/mantenimiento'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => url('/codigos'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => url('/tecnologia'), 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => url('/compatibilidad'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => url('/recomendador'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => url('/costos'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => url('/guias'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/taller'), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => url('/proveedores'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => url('/glosario'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => url('/nosotros'), 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['loc' => url('/fuentes'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => url('/contacto'), 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['loc' => url('/privacidad'), 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => url('/terminos'), 'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        foreach (['/maquinas/' => "select slug, updated_at from machines where status = 'publicado'",
                     '/implementos/' => "select slug, updated_at from implements where status = 'publicado'",
                     '/fallas/' => "select slug, updated_at from faults where status = 'publicado'",
                     '/guias/' => 'select slug, updated_at from guides',
                     '/tecnologia/' => 'select slug, updated_at from tech_resources',
                     '/proveedores/' => 'select slug, updated_at from providers',
                     '/taller/' => "select slug, updated_at from workshop_cases where status = 'publicado'"] as $base => $sql) {
            foreach (Database::select($sql) as $row) {
                $urls[] = [
                    'loc' => url($base . $row['slug']),
                    'lastmod' => date('c', strtotime((string) $row['updated_at'])),
                    'priority' => '0.7',
                    'changefreq' => 'monthly',
                ];
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $entry) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($entry['loc'], ENT_XML1) . "</loc>\n";
            if (isset($entry['lastmod'])) {
                $xml .= '    <lastmod>' . $entry['lastmod'] . "</lastmod>\n";
            }
            $xml .= '    <changefreq>' . $entry['changefreq'] . "</changefreq>\n";
            $xml .= '    <priority>' . $entry['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return Response::html($xml, 200)->withHeader('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(Request $request): Response
    {
        $body = "User-agent: *\n";
        $body .= "Allow: /\n";
        $body .= "Disallow: /admin\n";
        $body .= "Disallow: /mi-panel\n";
        $body .= "Disallow: /perfil\n";
        $body .= "Disallow: /acceder\n";
        $body .= "Disallow: /registro\n";
        $body .= "Disallow: /recuperar\n";
        $body .= "Disallow: /buscar\n";
        $body .= "Disallow: /taller/nuevo\n";
        $body .= "Disallow: /uploads/\n";
        $body .= "\nSitemap: " . url('/sitemap.xml') . "\n";

        return Response::html($body, 200)->withHeader('Content-Type', 'text/plain; charset=UTF-8');
    }
}
