<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class ContentRepository
{
    /**
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    public function tech(array $filters, int $page = 1, int $perPage = 18): array
    {
        $where = ['1 = 1'];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $params[] = '%' . $q . '%';
            $p = count($params);
            $where[] = "(name ilike \$$p or vendor ilike \$$p or description ilike \$$p)";
        }
        if (($filters['kind'] ?? '') !== '') {
            $params[] = $filters['kind'];
            $where[] = 'kind = $' . count($params);
        }
        if (($filters['category'] ?? '') !== '') {
            $params[] = $filters['category'];
            $where[] = 'category = $' . count($params);
        }
        if (($filters['license'] ?? '') === 'abierta') {
            $where[] = "(license ilike '%MIT%' or license ilike '%GPL%' or license ilike '%Apache%' or license ilike '%abiert%' or license ilike '%Creative%')";
        }

        $whereSql = implode(' and ', $where);
        $total = (int) Database::value('select count(*) from tech_resources where ' . $whereSql, $params);

        $limit = max(1, min(60, $perPage));
        $params[] = $limit;
        $params[] = max(0, ($page - 1) * $limit);

        $items = Database::select(
            'select * from tech_resources where ' . $whereSql .
            ' order by kind, sort_order asc, name asc limit $' . (count($params) - 1) . ' offset $' . count($params),
            $params
        );

        return ['items' => $items, 'total' => $total];
    }

    public function techBySlug(string $slug): ?array
    {
        return Database::first('select * from tech_resources where slug = $1', [$slug]);
    }

    /** @return array<string, int> */
    public function techKindCounts(): array
    {
        $rows = Database::select('select kind, count(*)::int as count from tech_resources group by kind');
        $counts = [];
        foreach ($rows as $row) {
            $counts[(string) $row['kind']] = (int) $row['count'];
        }
        return $counts;
    }

    /**
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    public function providers(array $filters, int $page = 1, int $perPage = 24): array
    {
        $where = ['1 = 1'];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $params[] = '%' . $q . '%';
            $p = count($params);
            $where[] = "(name ilike \$$p or city ilike \$$p or notes ilike \$$p)";
        }
        if (($filters['type'] ?? '') !== '') {
            $params[] = $filters['type'];
            $where[] = 'type = $' . count($params);
        }
        if (($filters['state'] ?? '') !== '') {
            $params[] = $filters['state'];
            $where[] = '($' . count($params) . ' = any(states))';
        }
        if (($filters['brand'] ?? '') !== '') {
            $params[] = $filters['brand'];
            $where[] = '($' . count($params) . ' = any(brands))';
        }

        $whereSql = implode(' and ', $where);
        $total = (int) Database::value('select count(*) from providers where ' . $whereSql, $params);

        $limit = max(1, min(60, $perPage));
        $params[] = $limit;
        $params[] = max(0, ($page - 1) * $limit);

        $items = Database::select(
            'select * from providers where ' . $whereSql .
            ' order by type, name limit $' . (count($params) - 1) . ' offset $' . count($params),
            $params
        );

        return ['items' => $items, 'total' => $total];
    }

    public function providerBySlug(string $slug): ?array
    {
        return Database::first('select * from providers where slug = $1', [$slug]);
    }

    /** @return array<string, int> */
    public function providerTypeCounts(): array
    {
        $rows = Database::select('select type, count(*)::int as count from providers group by type');
        $counts = [];
        foreach ($rows as $row) {
            $counts[(string) $row['type']] = (int) $row['count'];
        }
        return $counts;
    }

    /**
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    public function guides(array $filters, int $page = 1, int $perPage = 9): array
    {
        $where = ['1 = 1'];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $params[] = $q;
            $where[] = 'search_vector @@ plainto_tsquery(\'spanish\', $' . count($params) . ')';
        }
        if (($filters['category'] ?? '') !== '') {
            $params[] = $filters['category'];
            $where[] = 'category = $' . count($params);
        }
        if (($filters['level'] ?? '') !== '') {
            $params[] = $filters['level'];
            $where[] = 'level = $' . count($params);
        }

        $whereSql = implode(' and ', $where);
        $total = (int) Database::value('select count(*) from guides where ' . $whereSql, $params);

        $limit = max(1, min(36, $perPage));
        $params[] = $limit;
        $params[] = max(0, ($page - 1) * $limit);

        $items = Database::select(
            'select id, slug, title, category, summary, level, reading_min, author, featured, published_at, updated_at
             from guides where ' . $whereSql .
            ' order by featured desc, published_at desc limit $' . (count($params) - 1) . ' offset $' . count($params),
            $params
        );

        return ['items' => $items, 'total' => $total];
    }

    public function guideBySlug(string $slug): ?array
    {
        return Database::first('select * from guides where slug = $1', [$slug]);
    }

    /** @return array<int, array<string, mixed>> */
    public function relatedGuides(string $category, int $excludeId, int $limit = 3): array
    {
        return Database::select(
            'select id, slug, title, category, summary, level, reading_min, published_at
             from guides where category = $1 and id <> $2 order by published_at desc limit $3',
            [$category, $excludeId, $limit]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function featuredGuides(int $limit = 3): array
    {
        return Database::select(
            'select id, slug, title, category, summary, level, reading_min, published_at
             from guides order by featured desc, published_at desc limit $1',
            [$limit]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function glossary(string $q = '', string $category = ''): array
    {
        $where = ['1 = 1'];
        $params = [];
        if ($q !== '') {
            $params[] = '%' . $q . '%';
            $p = count($params);
            $where[] = "(term ilike \$$p or definition ilike \$$p)";
        }
        if ($category !== '') {
            $params[] = $category;
            $where[] = 'category = $' . count($params);
        }
        return Database::select(
            'select * from glossary where ' . implode(' and ', $where) . ' order by sort_order asc, term asc',
            $params
        );
    }

    /** @return array<int, array{category: string, total: int}> */
    public function glossaryCategories(): array
    {
        return Database::select('select category, count(*)::int as total from glossary group by category order by category');
    }
}
