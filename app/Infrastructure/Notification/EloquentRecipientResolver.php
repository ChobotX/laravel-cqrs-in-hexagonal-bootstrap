<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Notification\Contract\Service\RecipientResolver;
use Illuminate\Support\Facades\DB;

final readonly class EloquentRecipientResolver implements RecipientResolver
{
    /** @return list<string> */
    public function resolveTeamMembers(string $teamId): array
    {
        /** @var list<string> $userIds */
        $userIds = DB::table('team_members')
            ->where('team_id', $teamId)
            ->pluck('user_id')
            ->all();

        return $userIds;
    }

    /** @return list<string> */
    public function resolveTeamWithSubteamMembers(string $teamId): array
    {
        /** @var list<object{user_id: string}> $rows */
        $rows = DB::select(<<<'SQL'
            WITH RECURSIVE team_tree AS (
                SELECT id FROM teams WHERE id = ? AND deleted_at IS NULL
                UNION ALL
                SELECT t.id FROM teams t JOIN team_tree tt ON t.parent_team_id = tt.id WHERE t.deleted_at IS NULL
            )
            SELECT DISTINCT tm.user_id FROM team_members tm
            JOIN team_tree tt ON tm.team_id = tt.id
            SQL, [$teamId]);

        return array_map(static fn (object $row): string => $row->user_id, $rows);
    }
}
