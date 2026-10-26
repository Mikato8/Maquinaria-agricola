<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class AdminRepository
{
    /** @return array<int, array<string, mixed>> */
    public function comments(int $limit = 60): array
    {
        return Database::select(
            "select cm.*, u.name as author_name, u.role as author_role, c.title as case_title, c.slug as case_slug
             from comments cm
             join users u on u.id = cm.user_id
             left join workshop_cases c on c.id = cm.target_id and cm.target_type = 'taller'
             order by cm.created_at desc limit $1",
            [$limit]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function inquiries(bool $onlyPending = false, int $limit = 60): array
    {
        $where = $onlyPending ? 'where handled = false' : '';
        return Database::select(
            "select * from inquiries {$where} order by created_at desc limit $1",
            [$limit]
        );
    }

    public function markInquiry(int $id, bool $handled = true): void
    {
        Database::execute('update inquiries set handled = $1 where id = $2', [$handled, $id]);
    }

    public function unhandledInquiries(): int
    {
        return (int) Database::value('select count(*) from inquiries where handled = false');
    }

    /** @return array<string, int> */
    public function dashboardStats(): array
    {
        return [
            'maquinas' => (int) Database::value("select count(*) from machines where status = 'publicado'"),
            'implementos' => (int) Database::value("select count(*) from implements where status = 'publicado'"),
            'fallas' => (int) Database::value("select count(*) from faults where status = 'publicado'"),
            'guias' => (int) Database::value('select count(*) from guides'),
            'recursos' => (int) Database::value('select count(*) from tech_resources'),
            'proveedores' => (int) Database::value('select count(*) from providers'),
            'usuarios' => (int) Database::value('select count(*) from users where deleted_at is null'),
            'pendientes' => (int) Database::value("select count(*) from users where status = 'pendiente' and deleted_at is null"),
            'casos' => (int) Database::value("select count(*) from workshop_cases where status = 'publicado'"),
            'revision' => (int) Database::value("select count(*) from workshop_cases where status = 'en_revision'"),
            'comentarios' => (int) Database::value("select count(*) from comments where status = 'visible'"),
            'mensajes' => (int) Database::value('select count(*) from inquiries where handled = false'),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function audit(int $limit = 100): array
    {
        return Database::select(
            'select a.*, u.name as actor_name from audit_log a left join users u on u.id = a.actor_id
             order by a.created_at desc limit $1',
            [$limit]
        );
    }

    /** @param array<string, mixed> $detail */
    public function log(?int $actorId, string $action, string $targetType = '', ?int $targetId = null, array $detail = [], string $ip = ''): void
    {
        Database::execute(
            'insert into audit_log (actor_id, action, target_type, target_id, detail, ip) values ($1, $2, $3, $4, $5, $6)',
            [$actorId, $action, $targetType, $targetId, json_encode($detail, JSON_UNESCAPED_UNICODE), $ip]
        );
    }
}
