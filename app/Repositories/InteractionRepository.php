<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class InteractionRepository
{
    private const TYPES = ['taller', 'maquina', 'falla', 'guia', 'implemento', 'recurso'];

    public function toggleBookmark(string $type, int $targetId, int $userId): bool
    {
        if (!in_array($type, self::TYPES, true)) {
            return false;
        }
        if ($this->isBookmarked($type, $targetId, $userId)) {
            Database::execute(
                'delete from bookmarks where target_type = $1 and target_id = $2 and user_id = $3',
                [$type, $targetId, $userId]
            );
            return false;
        }
        Database::execute(
            'insert into bookmarks (target_type, target_id, user_id) values ($1, $2, $3) on conflict do nothing',
            [$type, $targetId, $userId]
        );
        return true;
    }

    public function isBookmarked(string $type, int $targetId, ?int $userId): bool
    {
        if ($userId === null) {
            return false;
        }
        return Database::value(
            'select 1 from bookmarks where target_type = $1 and target_id = $2 and user_id = $3',
            [$type, $targetId, $userId]
        ) !== null;
    }

    /** @return array<int, array<string, mixed>> */
    public function bookmarks(int $userId, int $limit = 20): array
    {
        return Database::select(
            'select * from bookmarks where user_id = $1 order by created_at desc limit $2',
            [$userId, $limit]
        );
    }

    /** @return array{total: int, by_type: array<string, int>} */
    public function bookmarkStats(int $userId): array
    {
        $rows = Database::select(
            'select target_type, count(*)::int as total from bookmarks where user_id = $1 group by target_type',
            [$userId]
        );
        $byType = [];
        $total = 0;
        foreach ($rows as $row) {
            $byType[(string) $row['target_type']] = (int) $row['total'];
            $total += (int) $row['total'];
        }
        return ['total' => $total, 'by_type' => $byType];
    }
}
