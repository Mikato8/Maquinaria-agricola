<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class CatalogRepository
{
    private const MACHINE_COLUMNS = 'm.id, m.slug, m.model, m.name, m.category, m.traction, m.power_hp, m.pto_hp,
        m.engine, m.displacement_l, m.cylinders, m.aspiration, m.transmission, m.gears, m.pto_rpm,
        m.hydraulic_flow_lpm, m.lift_capacity_kg, m.weight_kg, m.fuel_tank_l, m.turning_radius_m,
        m.year_from, m.year_to, m.price_min_mxn, m.price_max_mxn, m.price_note, m.availability,
        m.summary, m.description, m.features, m.common_uses, m.maintenance_tip, m.image_url, m.views,
        m.status, m.is_demo, m.created_at, b.name as brand_name, b.slug as brand_slug, b.country as brand_country';

    /**
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    public function machines(array $filters, int $page = 1, int $perPage = 12): array
    {
        $where = ["m.status = 'publicado'"];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        $tsParam = null;
        if ($q !== '') {
            $params[] = $q;
            $tsParam = count($params);
            $params[] = '%' . $q . '%';
            $likeParam = count($params);
            $where[] = "(m.search_vector @@ plainto_tsquery('spanish', f_unaccent(\$$tsParam)) or f_unaccent(m.name) ilike f_unaccent(\$$likeParam) or f_unaccent(b.name) ilike f_unaccent(\$$likeParam))";
        }

        $category = (string) ($filters['category'] ?? '');
        if ($category !== '') {
            $params[] = $category;
            $where[] = 'm.category = $' . count($params);
        }

        $brand = (string) ($filters['brand'] ?? '');
        if ($brand !== '') {
            $params[] = $brand;
            $where[] = 'b.slug = $' . count($params);
        }

        $traction = (string) ($filters['traction'] ?? '');
        if ($traction !== '') {
            $params[] = $traction;
            $where[] = 'm.traction = $' . count($params);
        }

        $minHp = (int) ($filters['min_hp'] ?? 0);
        if ($minHp > 0) {
            $params[] = $minHp;
            $where[] = 'm.power_hp >= $' . count($params);
        }

        $maxHp = (int) ($filters['max_hp'] ?? 0);
        if ($maxHp > 0) {
            $params[] = $maxHp;
            $where[] = 'm.power_hp <= $' . count($params);
        }

        $price = (string) ($filters['price'] ?? '');
        if ($price === 'hasta500') {
            $where[] = 'm.price_min_mxn <= 500000';
        } elseif ($price === '500a1000') {
            $where[] = 'm.price_min_mxn between 500000 and 1000000';
        } elseif ($price === 'mas1000') {
            $where[] = 'm.price_min_mxn > 1000000';
        }

        $whereSql = implode(' and ', $where);
        $total = (int) Database::value(
            'select count(*) from machines m join brands b on b.id = m.brand_id where ' . $whereSql,
            $params
        );

        $order = match ((string) ($filters['sort'] ?? 'relevancia')) {
            'potencia_asc' => 'm.power_hp asc nulls last, m.name asc',
            'potencia_desc' => 'm.power_hp desc nulls last, m.name asc',
            'precio_asc' => 'm.price_min_mxn asc nulls last, m.name asc',
            'precio_desc' => 'm.price_min_mxn desc nulls last, m.name asc',
            'nuevos' => 'm.year_from desc nulls last, m.name asc',
            default => $tsParam !== null
                ? 'ts_rank(m.search_vector, plainto_tsquery(\'spanish\', f_unaccent($' . $tsParam . '))) desc, m.power_hp desc nulls last'
                : "case m.category when 'tractor' then 0 when 'cosechadora' then 1 when 'pulverizadora' then 2 when 'forrajera' then 3 else 4 end, m.power_hp asc nulls last, m.name asc",
        };

        $limit = max(1, min(48, $perPage));
        $offset = max(0, ($page - 1) * $limit);
        $params[] = $limit;
        $params[] = $offset;

        $items = Database::select(
            'select ' . self::MACHINE_COLUMNS . ' from machines m join brands b on b.id = m.brand_id
             where ' . $whereSql . ' order by ' . $order . ' limit $' . (count($params) - 1) . ' offset $' . count($params),
            $params
        );

        return ['items' => $items, 'total' => $total];
    }

    public function machineBySlug(string $slug): ?array
    {
        return Database::first(
            'select ' . self::MACHINE_COLUMNS . ', b.website as brand_website, b.notes as brand_notes,
                    b.in_mexico as brand_in_mexico, b.segment as brand_segment
             from machines m join brands b on b.id = m.brand_id
             where m.slug = $1 and m.status = \'publicado\'',
            [$slug]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function machineByIds(array $ids): array
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if ($ids === []) {
            return [];
        }
        $params = [];
        $placeholders = [];
        foreach ($ids as $id) {
            $params[] = $id;
            $placeholders[] = '$' . count($params);
        }
        return Database::select(
            'select ' . self::MACHINE_COLUMNS . ' from machines m join brands b on b.id = m.brand_id
             where m.id in (' . implode(',', $placeholders) . ') order by m.power_hp asc nulls last',
            $params
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function relatedMachines(string $category, int $excludeId, int $limit = 3): array
    {
        return Database::select(
            'select ' . self::MACHINE_COLUMNS . ' from machines m join brands b on b.id = m.brand_id
             where m.category = $1 and m.id <> $2 and m.status = \'publicado\'
             order by abs(coalesce(m.power_hp, 0) - (select coalesce(power_hp, 0) from machines where id = $2)) asc
             limit $3',
            [$category, $excludeId, $limit]
        );
    }

    /** @return array<int, array{slug: string, name: string, count: int}> */
    public function brandsWithCounts(): array
    {
        return Database::select(
            'select b.slug, b.name, count(m.id)::int as count
             from brands b left join machines m on m.brand_id = b.id and m.status = \'publicado\'
             group by b.id, b.slug, b.name having count(m.id) > 0 order by b.name'
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function allBrands(): array
    {
        return Database::select('select * from brands order by name');
    }

    // -----------------------------------------------------------------------
    // Implementos
    // -----------------------------------------------------------------------
    /**
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    public function implements(array $filters, int $page = 1, int $perPage = 12): array
    {
        $where = ["status = 'publicado'"];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $params[] = $q;
            $tsParam = count($params);
            $params[] = '%' . $q . '%';
            $likeParam = count($params);
            $where[] = "(search_vector @@ plainto_tsquery('spanish', f_unaccent(\$$tsParam)) or f_unaccent(name) ilike f_unaccent(\$$likeParam))";
        }
        if (($filters['category'] ?? '') !== '') {
            $params[] = $filters['category'];
            $where[] = 'category = $' . count($params);
        }
        if (($filters['tractor_hp'] ?? 0) > 0) {
            $params[] = (int) $filters['tractor_hp'];
            $where[] = 'power_min_hp <= $' . count($params) . ' and (power_max_hp is null or power_max_hp >= $' . count($params) . ')';
        }
        if (($filters['budget'] ?? 0) > 0) {
            $params[] = (int) $filters['budget'];
            $where[] = 'price_min_mxn <= $' . count($params);
        }

        $whereSql = implode(' and ', $where);
        $total = (int) Database::value('select count(*) from implements where ' . $whereSql, $params);

        $order = match ((string) ($filters['sort'] ?? 'relevancia')) {
            'precio_asc' => 'price_min_mxn asc nulls last, name',
            'precio_desc' => 'price_min_mxn desc nulls last, name',
            'ancho' => 'working_width_m desc nulls last, name',
            default => 'category asc, power_min_hp asc, name',
        };

        $limit = max(1, min(48, $perPage));
        $params[] = $limit;
        $params[] = max(0, ($page - 1) * $limit);

        $items = Database::select(
            'select * from implements where ' . $whereSql . ' order by ' . $order .
            ' limit $' . (count($params) - 1) . ' offset $' . count($params),
            $params
        );

        return ['items' => $items, 'total' => $total];
    }

    public function implementBySlug(string $slug): ?array
    {
        return Database::first("select * from implements where slug = \$1 and status = 'publicado'", [$slug]);
    }

    /** @return array<int, array<string, mixed>> */
    public function implementsByIds(array $ids): array
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if ($ids === []) {
            return [];
        }
        $params = [];
        $holders = [];
        foreach ($ids as $id) {
            $params[] = $id;
            $holders[] = '$' . count($params);
        }
        return Database::select('select * from implements where id in (' . implode(',', $holders) . ') order by name', $params);
    }

    /** @return array<int, array<string, mixed>> */
    public function implementsForPower(int $hp, int $limit = 8): array
    {
        return Database::select(
            "select * from implements where status = 'publicado' and power_min_hp <= $1
             and (power_max_hp is null or power_max_hp >= $1)
             order by category, power_min_hp asc limit $2",
            [$hp, $limit]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function machinesForImplement(int $powerMin, int $powerMax = 0, int $limit = 6): array
    {
        $params = [$powerMin];
        $where = 'm.status = \'publicado\' and m.category = \'tractor\' and coalesce(m.power_hp, 0) >= $1';
        if ($powerMax > 0) {
            $params[] = $powerMax * 1.35;
            $where .= ' and coalesce(m.power_hp, 0) <= $2';
        }
        $params[] = $limit;
        return Database::select(
            'select ' . self::MACHINE_COLUMNS . ' from machines m join brands b on b.id = m.brand_id
             where ' . $where . ' order by abs(coalesce(m.power_hp, 0) - $1) asc limit $' . count($params),
            $params
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function relatedImplements(string $category, int $excludeId, int $limit = 3): array
    {
        return Database::select(
            "select * from implements where category = \$1 and id <> \$2 and status = 'publicado' order by power_min_hp asc limit \$3",
            [$category, $excludeId, $limit]
        );
    }

    /** @return array<int, array{category: string, count: int}> */
    public function implementCounts(): array
    {
        return Database::select(
            "select category, count(*)::int as count from implements where status = 'publicado' group by category order by category"
        );
    }

    /** @return array<string, int> */
    public function catalogStats(): array
    {
        return [
            'machines' => (int) Database::value("select count(*) from machines where status = 'publicado'"),
            'implements' => (int) Database::value("select count(*) from implements where status = 'publicado'"),
            'brands' => (int) Database::value('select count(*) from brands'),
            'faults' => (int) Database::value("select count(*) from faults where status = 'publicado'"),
            'guides' => (int) Database::value('select count(*) from guides'),
            'tech' => (int) Database::value('select count(*) from tech_resources'),
            'providers' => (int) Database::value('select count(*) from providers'),
            'cases' => (int) Database::value("select count(*) from workshop_cases where status = 'publicado'"),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function featuredMachines(int $limit = 3): array
    {
        return Database::select(
            'select ' . self::MACHINE_COLUMNS . ' from machines m join brands b on b.id = m.brand_id
             where m.status = \'publicado\' and m.power_hp is not null
             order by m.views desc, m.power_hp asc limit $1',
            [$limit]
        );
    }

    public function incrementViews(string $table, int $id): void
    {
        $allowed = ['machines', 'implements', 'faults', 'workshop_cases', 'guides'];
        if (!in_array($table, $allowed, true)) {
            return;
        }
        Database::execute('update ' . $table . ' set views = views + 1 where id = $1', [$id]);
    }
}
