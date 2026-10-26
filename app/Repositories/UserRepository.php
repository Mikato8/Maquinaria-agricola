<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Core\Str;

final class UserRepository
{
    /** @param array<string, mixed> $data */
    public function create(array $data): int
    {
        $row = Database::insert(
            'insert into users (name, email, password_hash, role, status, headline, workshop_name, city, state_code,
                                phone, bio, verification_method, terms_accepted_at, email_verified, created_at)
             values ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, now(), $13, now())
             returning id',
            [
                $data['name'],
                Str::normalizeEmail((string) $data['email']),
                password_hash((string) $data['password'], PASSWORD_DEFAULT),
                $data['role'] ?? 'productor',
                $data['status'] ?? 'pendiente',
                $data['headline'] ?? '',
                $data['workshop_name'] ?? '',
                $data['city'] ?? '',
                $data['state_code'] ?? null,
                $data['phone'] ?? '',
                $data['bio'] ?? '',
                $data['verification_method'] ?? 'manual',
                !empty($data['email_verified']),
            ]
        );
        return (int) ($row['id'] ?? 0);
    }

    public function byEmail(string $email): ?array
    {
        return Database::first('select * from users where email = $1 and deleted_at is null', [Str::normalizeEmail($email)]);
    }

    public function byId(int $id): ?array
    {
        return Database::first(
            'select u.*, s.name as state_name from users u left join states s on s.code = u.state_code
             where u.id = $1 and u.deleted_at is null',
            [$id]
        );
    }

    public function byIdWithStats(int $id): ?array
    {
        return Database::first(
            "select u.*, s.name as state_name,
                    (select count(*) from workshop_cases c where c.author_id = u.id and c.status = 'publicado')::int as cases_count,
                    (select count(*) from workshop_cases c where c.author_id = u.id)::int as cases_total,
                    (select coalesce(sum(views), 0) from workshop_cases c where c.author_id = u.id)::int as views_total,
                    (select count(*) from reactions r join workshop_cases c on c.id = r.target_id
                        where r.target_type = 'taller' and c.author_id = u.id)::int as thanks_total
             from users u left join states s on s.code = u.state_code
             where u.id = \$1 and u.deleted_at is null",
            [$id]
        );
    }

    public function findByEmail(string $email): ?array
    {
        return $this->byEmail($email);
    }

    public function createPasswordReset(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        Database::execute(
            "insert into password_resets (user_id, token_hash, expires_at) values ($1, $2, now() + interval '60 minutes')",
            [$userId, hash('sha256', $token)]
        );
        return $token;
    }

    public function findByResetToken(string $token): ?array
    {
        return Database::first(
            'select u.*, r.id as reset_id from password_resets r join users u on u.id = r.user_id
             where r.token_hash = $1 and r.used_at is null and r.expires_at > now() and u.deleted_at is null',
            [hash('sha256', $token)]
        );
    }

    public function consumeResetToken(string $token): void
    {
        Database::execute(
            "update password_resets set used_at = now() where token_hash = $1",
            [hash('sha256', $token)]
        );
    }

    public function emailExists(string $email): bool
    {
        return Database::value('select 1 from users where email = $1', [Str::normalizeEmail($email)]) !== null;
    }

    /** @param array<string, mixed> $data */
    public function updateProfile(int $id, array $data): void
    {
        Database::execute(
            'update users set name = $1, headline = $2, workshop_name = $3, city = $4, state_code = $5,
                    phone = $6, bio = $7, updated_at = now()
             where id = $8',
            [
                $data['name'],
                $data['headline'] ?? '',
                $data['workshop_name'] ?? '',
                $data['city'] ?? '',
                $data['state_code'] ?? null,
                $data['phone'] ?? '',
                $data['bio'] ?? '',
                $id,
            ]
        );
    }

    public function updatePassword(int $id, string $hash): void
    {
        Database::execute('update users set password_hash = $1, updated_at = now() where id = $2', [$hash, $id]);
    }

    public function setAvatar(int $id, ?string $url): void
    {
        Database::execute('update users set avatar_url = $1, updated_at = now() where id = $2', [$url, $id]);
    }

    public function setProof(int $id, string $path): void
    {
        Database::execute('update users set proof_path = $1, updated_at = now() where id = $2', [$path, $id]);
    }

    public function verify(int $id, string $method, string $note = ''): void
    {
        Database::execute(
            "update users set status = 'verificado', verification_method = $1, verification_note = $2,
                    verified_at = now(), updated_at = now()
             where id = $3",
            [$method, $note, $id]
        );
    }

    public function suspend(int $id, bool $suspend): void
    {
        Database::execute(
            'update users set status = $1, updated_at = now() where id = $2',
            [$suspend ? 'suspendido' : 'pendiente', $id]
        );
    }

    public function setRole(int $id, string $role): void
    {
        Database::execute('update users set role = $1, updated_at = now() where id = $2', [$role, $id]);
    }

    public function changeEmail(int $id, string $email): void
    {
        Database::execute('update users set email = $1, email_verified = false, updated_at = now() where id = $2', [
            Str::normalizeEmail($email),
            $id,
        ]);
    }

    public function deleteAccount(int $id): void
    {
        Database::execute(
            "update users set deleted_at = now(), status = 'suspendido', email = concat('eliminado-', id, '@example.invalid'),
                    name = 'Cuenta eliminada', headline = '', workshop_name = '', city = '', phone = '', bio = '', avatar_url = null
             where id = $1",
            [$id]
        );
    }

    /**
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    public function adminList(array $filters, int $page = 1, int $perPage = 20): array
    {
        $where = ['u.deleted_at is null'];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $params[] = '%' . $q . '%';
            $p = count($params);
            $where[] = "(u.name ilike \$$p or u.email ilike \$$p or u.workshop_name ilike \$$p)";
        }
        if (($filters['status'] ?? '') !== '') {
            $params[] = $filters['status'];
            $where[] = 'u.status = $' . count($params);
        }
        if (($filters['role'] ?? '') !== '') {
            $params[] = $filters['role'];
            $where[] = 'u.role = $' . count($params);
        }

        $whereSql = implode(' and ', $where);
        $total = (int) Database::value('select count(*) from users u where ' . $whereSql, $params);

        $limit = max(1, min(100, $perPage));
        $params[] = $limit;
        $params[] = max(0, ($page - 1) * $limit);

        $items = Database::select(
            'select u.*, s.name as state_name,
                    (select count(*) from workshop_cases c where c.author_id = u.id)::int as cases_count
             from users u left join states s on s.code = u.state_code
             where ' . $whereSql . ' order by u.created_at desc limit $' . (count($params) - 1) . ' offset $' . count($params),
            $params
        );

        return ['items' => $items, 'total' => $total];
    }

    /** @return array<int, array<string, mixed>> */
    public function pendingForVerification(int $limit = 8): array
    {
        return Database::select(
            "select u.*, s.name as state_name from users u left join states s on s.code = u.state_code
             where u.status = 'pendiente' and u.deleted_at is null order by u.created_at asc limit $1",
            [$limit]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function topContributors(int $limit = 4): array
    {
        return Database::select(
            "select u.id, u.name, u.headline, u.workshop_name, u.avatar_url, u.role, u.state_code, s.name as state_name,
                    count(c.id)::int as cases_count, coalesce(sum(c.views), 0)::int as views_total
             from users u join workshop_cases c on c.author_id = u.id and c.status = 'publicado'
             left join states s on s.code = u.state_code
             where u.deleted_at is null
             group by u.id, u.name, u.headline, u.workshop_name, u.avatar_url, u.role, u.state_code, s.name
             order by count(c.id) desc, sum(c.views) desc limit $1",
            [$limit]
        );
    }

    /** @return array<string, int> */
    public function counts(): array
    {
        return [
            'total' => (int) Database::value('select count(*) from users where deleted_at is null'),
            'pendientes' => (int) Database::value("select count(*) from users where status = 'pendiente' and deleted_at is null"),
            'verificados' => (int) Database::value("select count(*) from users where status = 'verificado' and deleted_at is null"),
            'suspendidos' => (int) Database::value("select count(*) from users where status = 'suspendido' and deleted_at is null"),
        ];
    }
}
