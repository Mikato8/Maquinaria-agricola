<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Core\Str;

final class WorkshopRepository
{
    /**
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    public function published(array $filters, int $page = 1, int $perPage = 9): array
    {
        $where = ["c.status = 'publicado'"];
        $params = [];
        $tsParam = null;

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $params[] = $q;
            $tsParam = count($params);
            $params[] = '%' . $q . '%';
            $likeParam = count($params);
            $where[] = "(c.search_vector @@ plainto_tsquery('spanish', f_unaccent(\$$tsParam)) or f_unaccent(c.title) ilike f_unaccent(\$$likeParam)
                         or f_unaccent(c.machine_label) ilike f_unaccent(\$$likeParam))";
        }
        if (($filters['category'] ?? '') !== '') {
            $params[] = $filters['category'];
            $where[] = 'c.machine_category = $' . count($params);
        }
        if (($filters['outcome'] ?? '') !== '') {
            $params[] = $filters['outcome'];
            $where[] = 'c.outcome = $' . count($params);
        }
        if (($filters['state'] ?? '') !== '') {
            $params[] = $filters['state'];
            $where[] = 'c.state_code = $' . count($params);
        }

        $whereSql = implode(' and ', $where);
        $total = (int) Database::value('select count(*) from workshop_cases c where ' . $whereSql, $params);

        $order = match ((string) ($filters['sort'] ?? 'recientes')) {
            'vistos' => 'c.views desc, c.published_at desc',
            'util' => '(select count(*) from reactions r where r.target_type = \'taller\' and r.target_id = c.id) desc, c.published_at desc',
            default => $tsParam !== null
                ? 'ts_rank(c.search_vector, plainto_tsquery(\'spanish\', f_unaccent($' . $tsParam . '))) desc, c.published_at desc'
                : 'c.featured desc, c.published_at desc',
        };

        $limit = max(1, min(36, $perPage));
        $params[] = $limit;
        $params[] = max(0, ($page - 1) * $limit);

        $items = Database::select(
            'select c.*, u.name as author_name, u.headline as author_headline, u.workshop_name as author_workshop,
                    u.avatar_url as author_avatar, u.role as author_role, s.name as state_name,
                    (select count(*) from reactions r where r.target_type = \'taller\' and r.target_id = c.id)::int as reactions_count,
                    (select count(*) from comments cm where cm.target_type = \'taller\' and cm.target_id = c.id and cm.status = \'visible\')::int as comments_count,
                    (select url from workshop_case_images i where i.case_id = c.id order by i.sort_order asc, i.id asc limit 1) as cover_url
             from workshop_cases c
             join users u on u.id = c.author_id
             left join states s on s.code = c.state_code
             where ' . $whereSql . ' order by ' . $order .
            ' limit $' . (count($params) - 1) . ' offset $' . count($params),
            $params
        );

        return ['items' => $items, 'total' => $total];
    }

    public function bySlug(string $slug): ?array
    {
        return Database::first(
            'select c.*, u.name as author_name, u.headline as author_headline, u.workshop_name as author_workshop,
                    u.avatar_url as author_avatar, u.role as author_role, u.bio as author_bio,
                    u.status as author_status, u.state_code as author_state, s.name as state_name,
                    (select count(*) from reactions r where r.target_type = \'taller\' and r.target_id = c.id)::int as reactions_count
             from workshop_cases c
             join users u on u.id = c.author_id
             left join states s on s.code = c.state_code
             where c.slug = $1 and c.status = \'publicado\'',
            [$slug]
        );
    }

    public function byId(int $id): ?array
    {
        return Database::first('select * from workshop_cases where id = $1', [$id]);
    }

    /** @return array<int, array<string, mixed>> */
    public function images(int $caseId): array
    {
        return Database::select(
            'select * from workshop_case_images where case_id = $1 order by sort_order asc, id asc',
            [$caseId]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function comments(int $caseId): array
    {
        return Database::select(
            "select cm.*, u.name as author_name, u.headline as author_headline, u.avatar_url as author_avatar,
                    u.role as author_role, u.workshop_name as author_workshop
             from comments cm join users u on u.id = cm.user_id
             where cm.target_type = 'taller' and cm.target_id = $1 and cm.status = 'visible'
             order by cm.created_at asc",
            [$caseId]
        );
    }

    public function addComment(int $caseId, int $userId, string $body): void
    {
        Database::execute(
            "insert into comments (target_type, target_id, user_id, body) values ('taller', $1, $2, $3)",
            [$caseId, $userId, $body]
        );
    }

    /** @return array<string, mixed>|null */
    public function commentById(int $id): ?array
    {
        return Database::first('select * from comments where id = $1', [$id]);
    }

    public function hideComment(int $id): void
    {
        Database::execute("update comments set status = 'oculto' where id = $1", [$id]);
    }

    public function hasReacted(int $caseId, int $userId): bool
    {
        return Database::value(
            "select 1 from reactions where target_type = 'taller' and target_id = $1 and user_id = $2",
            [$caseId, $userId]
        ) !== null;
    }

    public function toggleReaction(int $caseId, int $userId): bool
    {
        if ($this->hasReacted($caseId, $userId)) {
            Database::execute(
                "delete from reactions where target_type = 'taller' and target_id = $1 and user_id = $2",
                [$caseId, $userId]
            );
            return false;
        }
        Database::execute(
            "insert into reactions (target_type, target_id, user_id) values ('taller', $1, $2) on conflict do nothing",
            [$caseId, $userId]
        );
        return true;
    }

    /** @return array<int, array<string, mixed>> */
    public function userCases(int $userId): array
    {
        return Database::select(
            'select c.*, (select count(*) from comments cm where cm.target_type = \'taller\' and cm.target_id = c.id)::int as comments_count
             from workshop_cases c where c.author_id = $1 order by c.updated_at desc',
            [$userId]
        );
    }

    /** @param array<string, mixed> $data */
    public function create(int $userId, array $data): int
    {
        $slug = $this->uniqueSlug((string) $data['title']);
        $next = (int) Database::value('select coalesce(max(id), 0) + 1 from workshop_cases');
        $folio = 'TAL-' . date('Y') . '-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);

        $row = Database::insert(
            'insert into workshop_cases (slug, folio, author_id, title, summary, machine_category, machine_label, machine_id,
                state_code, symptom, diagnosis, repair, parts, cost_mxn, time_hours, difficulty, outcome, safety, tips, status)
             values ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16, $17, $18, $19, \'borrador\')
             returning id',
            [
                $slug,
                $folio,
                $userId,
                $data['title'],
                $data['summary'] ?? '',
                $data['machine_category'] ?? 'tractor',
                $data['machine_label'] ?? '',
                $data['machine_id'] ?? null,
                $data['state_code'] ?? null,
                $data['symptom'] ?? '',
                $data['diagnosis'] ?? '',
                $data['repair'] ?? '',
                $data['parts'] ?? '',
                $data['cost_mxn'] ?? null,
                $data['time_hours'] ?? null,
                $data['difficulty'] ?? 3,
                $data['outcome'] ?? 'resuelto',
                $data['safety'] ?? '',
                $data['tips'] ?? '',
            ]
        );

        return (int) ($row['id'] ?? 0);
    }

    /** @param array<string, mixed> $data */
    public function update(int $id, int $userId, array $data): void
    {
        Database::execute(
            'update workshop_cases set title = $1, summary = $2, machine_category = $3, machine_label = $4,
                    machine_id = $5, state_code = $6, symptom = $7, diagnosis = $8, repair = $9, parts = $10,
                    cost_mxn = $11, time_hours = $12, difficulty = $13, outcome = $14, safety = $15, tips = $16,
                    status = case when status = \'rechazado\' then \'borrador\' else status end, updated_at = now()
             where id = $17 and author_id = $18',
            [
                $data['title'],
                $data['summary'] ?? '',
                $data['machine_category'] ?? 'tractor',
                $data['machine_label'] ?? '',
                $data['machine_id'] ?? null,
                $data['state_code'] ?? null,
                $data['symptom'] ?? '',
                $data['diagnosis'] ?? '',
                $data['repair'] ?? '',
                $data['parts'] ?? '',
                $data['cost_mxn'] ?? null,
                $data['time_hours'] ?? null,
                $data['difficulty'] ?? 3,
                $data['outcome'] ?? 'resuelto',
                $data['safety'] ?? '',
                $data['tips'] ?? '',
                $id,
                $userId,
            ]
        );
    }

    public function submit(int $id, int $userId): void
    {
        Database::execute(
            "update workshop_cases set status = 'en_revision', updated_at = now()
             where id = $1 and author_id = $2 and status in ('borrador', 'cambios_solicitados')",
            [$id, $userId]
        );
    }

    public function remove(int $id, int $userId): void
    {
        Database::execute('delete from workshop_case_images where case_id = $1', [$id]);
        Database::execute('delete from workshop_cases where id = $1 and author_id = $2', [$id, $userId]);
    }

    public function addImage(int $caseId, string $url, string $caption = ''): void
    {
        Database::execute(
            'insert into workshop_case_images (case_id, url, caption, sort_order)
             values ($1, $2, $3, coalesce((select max(sort_order) + 1 from workshop_case_images where case_id = $1), 0))',
            [$caseId, $url, $caption]
        );
    }

    public function deleteImage(int $imageId, int $caseId, int $userId): ?string
    {
        $image = Database::first(
            'select i.* from workshop_case_images i join workshop_cases c on c.id = i.case_id
             where i.id = $1 and i.case_id = $2 and c.author_id = $3',
            [$imageId, $caseId, $userId]
        );
        if ($image === null) {
            return null;
        }
        Database::execute('delete from workshop_case_images where id = $1', [$imageId]);
        return (string) $image['url'];
    }

    /** @return array<int, array<string, mixed>> */
    public function pending(int $limit = 20): array
    {
        return Database::select(
            "select c.*, u.name as author_name from workshop_cases c join users u on u.id = c.author_id
             where c.status in ('en_revision', 'cambios_solicitados') order by c.updated_at asc limit $1",
            [$limit]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function reviewHistory(int $caseId): array
    {
        return Database::select(
            'select r.*, u.name as reviewer_name from reviews r left join users u on u.id = r.reviewer_id
             where r.target_type = \'taller\' and r.target_id = $1 order by r.created_at asc',
            [$caseId]
        );
    }

    public function decide(int $caseId, int $reviewerId, string $decision, string $notes): void
    {
        $status = match ($decision) {
            'publicar' => 'publicado',
            'cambios' => 'cambios_solicitados',
            default => 'rechazado',
        };
        Database::execute(
            'update workshop_cases set status = $1, updated_at = now(),
                    published_at = case when $1 = \'publicado\' then coalesce(published_at, now()) else published_at end
             where id = $2',
            [$status, $caseId]
        );
        Database::execute(
            "insert into reviews (target_type, target_id, reviewer_id, decision, notes)
             values ('taller', $1, $2, $3, $4)",
            [$caseId, $reviewerId, $decision, $notes]
        );
    }

    public function feature(int $caseId, bool $featured): void
    {
        Database::execute('update workshop_cases set featured = $1 where id = $2', [$featured, $caseId]);
    }

    /** @return array<int, array<string, mixed>> */
    public function featured(int $limit = 2): array
    {
        return Database::select(
            "select c.*, u.name as author_name, u.workshop_name as author_workshop, u.avatar_url as author_avatar,
                    (select count(*) from reactions r where r.target_type = 'taller' and r.target_id = c.id)::int as reactions_count,
                    (select url from workshop_case_images i where i.case_id = c.id order by i.sort_order asc, i.id asc limit 1) as cover_url
             from workshop_cases c join users u on u.id = c.author_id
             where c.status = 'publicado' order by c.featured desc, c.published_at desc limit $1",
            [$limit]
        );
    }

    /** @return array<string, int> */
    public function counts(): array
    {
        return [
            'publicados' => (int) Database::value("select count(*) from workshop_cases where status = 'publicado'"),
            'revision' => (int) Database::value("select count(*) from workshop_cases where status = 'en_revision'"),
            'borradores' => (int) Database::value("select count(*) from workshop_cases where status = 'borrador'"),
            'comentarios' => (int) Database::value("select count(*) from comments where status = 'visible'"),
        ];
    }

    /** @return array<int, array{category: string, total: int}> */
    public function categoryCounts(): array
    {
        return Database::select(
            "select machine_category as category, count(*)::int as total from workshop_cases
             where status = 'publicado' group by machine_category order by total desc"
        );
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title, 70);
        if ($base === '') {
            $base = 'caso-de-taller';
        }
        $slug = $base;
        $suffix = 2;
        while (Database::value('select 1 from workshop_cases where slug = $1', [$slug]) !== null) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }
        return $slug;
    }
}
