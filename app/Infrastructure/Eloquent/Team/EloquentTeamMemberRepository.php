<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Team;

use App\Domain\Team\TeamMember;
use App\Domain\Team\TeamMemberRepository;
use App\Infrastructure\Eloquent\SortsQuery;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

final readonly class EloquentTeamMemberRepository implements TeamMemberRepository
{
    use SortsQuery;

    public function __construct(
        private TeamMemberMapper $teamMemberMapper,
    ) {}

    public function add(string $userId, string $teamId): void
    {
        $teamMemberModel = new TeamMemberModel;
        $teamMemberModel->user_id = $userId;
        $teamMemberModel->team_id = $teamId;
        $teamMemberModel->joined_at = (new DateTimeImmutable)->format('Y-m-d H:i:s');
        $teamMemberModel->save();
    }

    public function remove(string $userId, string $teamId): void
    {
        TeamMemberModel::where('user_id', $userId)
            ->where('team_id', $teamId)
            ->delete();
    }

    public function isMember(string $userId, string $teamId): bool
    {
        return TeamMemberModel::where('user_id', $userId)
            ->where('team_id', $teamId)
            ->exists();
    }

    /** @return list<string> */
    public function directMemberTeamIds(string $userId): array
    {
        /** @var list<string> $ids */
        $ids = array_values(
            TeamMemberModel::query()
                ->join('teams', 'teams.id', '=', 'team_members.team_id')
                ->where('team_members.user_id', $userId)
                ->whereNull('teams.deleted_at')
                ->pluck('team_members.team_id')
                ->all(),
        );

        return $ids;
    }

    /** @return list<string> */
    public function memberTeamIds(string $userId): array
    {
        $directTeamIds = TeamMemberModel::query()
            ->join('teams', 'teams.id', '=', 'team_members.team_id')
            ->where('team_members.user_id', $userId)
            ->whereNull('teams.deleted_at')
            ->pluck('team_members.team_id')
            ->all();

        if ($directTeamIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($directTeamIds), '?'));

        /** @var list<object{id: string}> $rows */
        $rows = DB::select(<<<SQL
            WITH RECURSIVE team_tree AS (
                SELECT id FROM teams WHERE id IN ({$placeholders}) AND deleted_at IS NULL
                UNION ALL
                SELECT t.id FROM teams t JOIN team_tree tt ON t.parent_team_id = tt.id WHERE t.deleted_at IS NULL
            )
            SELECT id FROM team_tree
            SQL, $directTeamIds);

        return array_values(array_unique(array_map(
            fn (object $row): string => $row->id,
            $rows,
        )));
    }

    /** @return list<TeamMember> */
    public function listMembers(string $teamId, array $sortings = []): array
    {
        $query = TeamMemberModel::with('user')
            ->where('team_id', $teamId);

        foreach ($sortings as $sorting) {
            if ($sorting->column === 'permission_score') {
                $query->selectRaw(<<<'SQL'
                    team_members.*,
                    CASE WHEN EXISTS(
                        SELECT 1 FROM user_roles ur
                        JOIN roles r ON ur.role_id = r.id
                        WHERE ur.user_id = team_members.user_id AND r.is_system = true AND r.deleted_at IS NULL
                    ) THEN 999999
                    ELSE (
                        COALESCE((
                            SELECT SUM(CASE rp.scope WHEN 'all' THEN 3 WHEN 'team' THEN 2 WHEN 'own' THEN 1 ELSE 0 END)
                            FROM user_roles ur
                            JOIN roles r ON ur.role_id = r.id AND r.deleted_at IS NULL
                            JOIN role_permissions rp ON r.id = rp.role_id
                            WHERE ur.user_id = team_members.user_id
                        ), 0)
                        +
                        COALESCE((
                            SELECT SUM(
                                CASE upo.type WHEN 'grant' THEN 1 WHEN 'deny' THEN -1 ELSE 0 END
                                * CASE upo.scope WHEN 'all' THEN 3 WHEN 'team' THEN 2 WHEN 'own' THEN 1 ELSE 0 END
                            )
                            FROM user_permission_overrides upo
                            WHERE upo.user_id = team_members.user_id
                        ), 0)
                    ) END AS permission_score
                    SQL);

                break;
            }
        }

        $query = $this->sortBuilder($query, $sortings);

        return array_values(
            $query->get()->map(fn (TeamMemberModel $teamMemberModel): TeamMember => $this->teamMemberMapper->toDomain($teamMemberModel))->all(),
        );
    }

    public function removeAllByUser(string $userId): void
    {
        TeamMemberModel::where('user_id', $userId)->delete();
    }
}
