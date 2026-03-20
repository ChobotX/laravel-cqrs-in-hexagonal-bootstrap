<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Organization;

use App\Domain\Organization\TeamMember;
use App\Domain\Organization\TeamMemberRepository;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

final readonly class EloquentTeamMemberRepository implements TeamMemberRepository
{
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
    public function directMemberTeamIds(string $userId, string $organizationId): array
    {
        /** @var list<string> $ids */
        $ids = array_values(
            TeamMemberModel::query()
                ->join('teams', 'teams.id', '=', 'team_members.team_id')
                ->where('team_members.user_id', $userId)
                ->where('teams.organization_id', $organizationId)
                ->whereNull('teams.deleted_at')
                ->pluck('team_members.team_id')
                ->all(),
        );

        return $ids;
    }

    /** @return list<string> */
    public function memberTeamIds(string $userId, string $organizationId): array
    {
        $directTeamIds = TeamMemberModel::query()
            ->join('teams', 'teams.id', '=', 'team_members.team_id')
            ->where('team_members.user_id', $userId)
            ->where('teams.organization_id', $organizationId)
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
    public function listMembers(string $teamId): array
    {
        $models = TeamMemberModel::with('user')
            ->where('team_id', $teamId)
            ->get();

        return array_values(
            $models->map(fn (TeamMemberModel $teamMemberModel): TeamMember => $this->teamMemberMapper->toDomain($teamMemberModel))->all(),
        );
    }

    public function removeAllByUserAndOrganization(string $userId, string $organizationId): void
    {
        DB::table('team_members')
            ->join('teams', 'teams.id', '=', 'team_members.team_id')
            ->where('team_members.user_id', $userId)
            ->where('teams.organization_id', $organizationId)
            ->whereNull('teams.deleted_at')
            ->delete();
    }
}
