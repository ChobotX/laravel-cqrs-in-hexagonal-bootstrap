<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Team;

use App\Domain\Team\Exception\TeamCycleDetectedException;
use App\Domain\Team\Team;
use App\Domain\Team\TeamId;
use App\Domain\Team\TeamRepository;
use App\Domain\Team\TeamSlug;
use Illuminate\Support\Facades\DB;

final readonly class EloquentTeamRepository implements TeamRepository
{
    public function __construct(
        private TeamMapper $teamMapper,
    ) {}

    /** @return list<Team> */
    public function findAll(?array $onlyIds = null): array
    {
        $query = TeamModel::query();

        if ($onlyIds !== null) {
            $query->whereIn('id', $onlyIds);
        }

        return array_values(
            $query->get()->map(fn (TeamModel $teamModel): Team => $this->teamMapper->toDomain($teamModel))->all(),
        );
    }

    public function findById(TeamId $teamId): ?Team
    {
        $model = TeamModel::find($teamId->value);

        if ($model === null) {
            return null;
        }

        return $this->teamMapper->toDomain($model);
    }

    public function findBySlug(TeamSlug $teamSlug): ?Team
    {
        $model = TeamModel::where('slug', $teamSlug->value)->first();

        if (! $model instanceof TeamModel) {
            return null;
        }

        return $this->teamMapper->toDomain($model);
    }

    public function create(Team $team): void
    {
        DB::transaction(function () use ($team): void {
            if ($team->parentTeamId instanceof TeamId) {
                $this->guardAgainstCycle($team->id->value, $team->parentTeamId->value);
            }

            $teamModel = new TeamModel;
            $teamModel->id = $team->id->value;
            $teamModel->parent_team_id = $team->parentTeamId?->value;
            $teamModel->name = $team->name->value;
            $teamModel->slug = $team->slug->value;
            $teamModel->description = $team->description;
            $teamModel->save();
        });
    }

    public function update(Team $team): void
    {
        DB::transaction(function () use ($team): void {
            if ($team->parentTeamId instanceof TeamId) {
                $this->guardAgainstCycle($team->id->value, $team->parentTeamId->value);
            }

            $model = TeamModel::findOrFail($team->id->value);
            $model->parent_team_id = $team->parentTeamId?->value;
            $model->name = $team->name->value;
            $model->slug = $team->slug->value;
            $model->description = $team->description;
            $model->save();
        });
    }

    /** @return list<Team> */
    public function search(string $term, array $excludeTeamIds, int $limit): array
    {
        $builder = TeamModel::query();

        if ($term !== '') {
            $builder->whereRaw('LOWER(name) LIKE ?', [sprintf('%%%s%%', mb_strtolower($term))]);
        }

        if ($excludeTeamIds !== []) {
            $builder->whereNotIn('id', $excludeTeamIds);
        }

        return array_values(
            $builder->limit($limit)
                ->get()
                ->map(fn (TeamModel $teamModel): Team => $this->teamMapper->toDomain($teamModel))
                ->all(),
        );
    }

    public function count(): int
    {
        return TeamModel::count();
    }

    public function delete(TeamId $teamId): void
    {
        $model = TeamModel::find($teamId->value);

        if ($model instanceof TeamModel) {
            DB::transaction(function () use ($model): void {
                TeamModel::where('parent_team_id', $model->id)
                    ->update(['parent_team_id' => $model->parent_team_id]);

                $model->delete();
            });
        }
    }

    private function guardAgainstCycle(string $teamId, string $parentTeamId): void
    {
        /** @var list<object{id: string}> $descendants */
        $descendants = DB::select(<<<'SQL'
            WITH RECURSIVE team_tree AS (
                SELECT id FROM teams WHERE parent_team_id = ? AND deleted_at IS NULL
                UNION ALL
                SELECT t.id FROM teams t JOIN team_tree tt ON t.parent_team_id = tt.id WHERE t.deleted_at IS NULL
            )
            SELECT id FROM team_tree
            SQL, [$teamId]);

        foreach ($descendants as $descendant) {
            if ($descendant->id === $parentTeamId) {
                throw new TeamCycleDetectedException($teamId, $parentTeamId);
            }
        }
    }
}
