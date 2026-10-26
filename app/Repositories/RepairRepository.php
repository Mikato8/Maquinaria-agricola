<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class RepairRepository
{
    /**
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    public function faults(array $filters, int $page = 1, int $perPage = 12): array
    {
        $where = ["status = 'publicado'"];
        $params = [];
        $tsParam = null;

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $params[] = $q;
            $tsParam = count($params);
            $params[] = '%' . $q . '%';
            $likeParam = count($params);
            $where[] = "(search_vector @@ plainto_tsquery('spanish', \$$tsParam) or title ilike \$$likeParam)";
        }
        if (($filters['system'] ?? '') !== '') {
            $params[] = $filters['system'];
            $where[] = 'system = $' . count($params);
        }
        if (($filters['category'] ?? '') !== '') {
            $params[] = $filters['category'];
            $where[] = 'machine_category = $' . count($params);
        }
        if (($filters['severity'] ?? '') !== '') {
            $params[] = $filters['severity'];
            $where[] = 'severity = $' . count($params);
        }

        $whereSql = implode(' and ', $where);
        $total = (int) Database::value('select count(*) from faults where ' . $whereSql, $params);

        $order = match ((string) ($filters['sort'] ?? 'relevancia')) {
            'dificultad_asc' => 'difficulty asc, title',
            'dificultad_desc' => 'difficulty desc, title',
            'vistas' => 'views desc, title',
            'nuevas' => 'created_at desc',
            default => $tsParam !== null
                ? 'ts_rank(search_vector, plainto_tsquery(\'spanish\', $' . $tsParam . ')) desc, views desc'
                : "case severity when 'critica' then 0 when 'alta' then 1 when 'media' then 2 else 3 end asc, views desc",
        };

        $limit = max(1, min(48, $perPage));
        $params[] = $limit;
        $params[] = max(0, ($page - 1) * $limit);

        $items = Database::select(
            'select * from faults where ' . $whereSql . ' order by ' . $order .
            ' limit $' . (count($params) - 1) . ' offset $' . count($params),
            $params
        );

        return ['items' => $items, 'total' => $total];
    }

    public function faultBySlug(string $slug): ?array
    {
        return Database::first("select * from faults where slug = \$1 and status = 'publicado'", [$slug]);
    }

    /** @return array<int, array<string, mixed>> */
    public function relatedFaults(string $system, int $excludeId, int $limit = 4): array
    {
        return Database::select(
            "select id, slug, title, system, severity, difficulty, machine_category, frequency, time_hours, symptom from faults
             where status = 'publicado' and system = \$1 and id <> \$2 order by views desc limit \$3",
            [$system, $excludeId, $limit]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function faultsForCategory(string $category, int $limit = 6): array
    {
        return Database::select(
            "select id, slug, title, system, severity, difficulty, time_hours, machine_category, frequency, symptom from faults
             where status = 'publicado' and (machine_category = \$1 or machine_category = 'general')
             order by views desc limit \$2",
            [$category, $limit]
        );
    }

    /** @return array<int, array{system: string, count: int}> */
    public function systemCounts(): array
    {
        return Database::select(
            "select system, count(*)::int as count from faults where status = 'publicado' group by system order by count desc"
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function dtcCodes(string $q = '', string $system = '', int $limit = 200): array
    {
        $where = ['1 = 1'];
        $params = [];
        if ($q !== '') {
            $params[] = '%' . $q . '%';
            $likeParam = count($params);
            $where[] = "(code ilike \$$likeParam or title ilike \$$likeParam or description ilike \$$likeParam)";
        }
        if ($system !== '') {
            $params[] = $system;
            $where[] = 'system = $' . count($params);
        }
        $params[] = $limit;
        return Database::select(
            'select * from dtc_codes where ' . implode(' and ', $where) . ' order by system, code limit $' . count($params),
            $params
        );
    }

    /**
     * @return array<int, array{interval_hours: int, interval_label: string, tasks: array<int, array<string, mixed>>}>
     */
    public function maintenancePlan(string $category): array
    {
        $rows = Database::select(
            'select * from maintenance_tasks where machine_category = $1 order by interval_hours asc, sort_order asc, id asc',
            [$category]
        );
        $groups = [];
        foreach ($rows as $row) {
            $hours = (int) $row['interval_hours'];
            if (!isset($groups[$hours])) {
                $groups[$hours] = [
                    'interval_hours' => $hours,
                    'interval_label' => (string) $row['interval_label'],
                    'tasks' => [],
                ];
            }
            $groups[$hours]['tasks'][] = $row;
        }
        return array_values($groups);
    }

    /** @return array<int, array{system: string, total: int}> */
    public function maintenanceSystems(string $category): array
    {
        return Database::select(
            'select system, count(*)::int as total from maintenance_tasks where machine_category = $1 group by system order by total desc',
            [$category]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function recentFaults(int $limit = 4): array
    {
        return Database::select(
            "select id, slug, title, system, severity, difficulty, machine_category, frequency, time_hours, symptom from faults where status = 'publicado' order by created_at desc limit \$1",
            [$limit]
        );
    }
}
