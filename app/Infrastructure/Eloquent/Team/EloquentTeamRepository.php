<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Team;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Team\Contract\Team;
use App\Domain\Team\Contract\TeamId;
use App\Domain\Team\Contract\TeamRepository;
use App\Domain\Team\Contract\TeamSlug;
use App\Domain\Team\Exception\TeamSlugAlreadyExistsException;
use App\Infrastructure\Eloquent\PaginatesQuery;
use App\Infrastructure\Eloquent\SortsQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

final readonly class EloquentTeamRepository implements TeamRepository
{
    use PaginatesQuery;
    use SortsQuery;

    public function __construct(
        private TeamMapper $teamMapper,
    ) {}

    /** @return list<Team> */
    public function findAll(?array $onlyIds = null, array $sortings = []): array
    {
        $builder = $this->sortBuilder($this->baseQuery($onlyIds), $sortings);

        return array_values(
            $builder->get()
                ->map(fn (TeamModel $teamModel): Team => $this->teamMapper->toDomain($teamModel))
                ->all(),
        );
    }

    /** @return PaginatedResult<Team> */
    public function findAllPaginated(Pagination $pagination, ?array $onlyIds = null, array $sortings = []): PaginatedResult
    {
        $builder = $this->sortBuilder($this->baseQuery($onlyIds), $sortings);

        [$models, $total] = $this->paginateBuilder($builder, $pagination);

        return new PaginatedResult(
            array_map($this->teamMapper->toDomain(...), $models),
            $total,
            $pagination,
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
        try {
            $teamModel = new TeamModel;
            $teamModel->id = $team->id->value;
            $teamModel->parent_team_id = $team->parentTeamId?->value;
            $teamModel->name = $team->name->value;
            $teamModel->slug = $team->slug->value;
            $teamModel->description = $team->description;
            $teamModel->save();
        } catch (UniqueConstraintViolationException) {
            throw new TeamSlugAlreadyExistsException($team->slug->value);
        }
    }

    public function update(Team $team): void
    {
        $model = TeamModel::findOrFail($team->id->value);
        $model->parent_team_id = $team->parentTeamId?->value;
        $model->name = $team->name->value;
        $model->slug = $team->slug->value;
        $model->description = $team->description;
        $model->save();
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

    /** @return list<string> */
    public function ancestorTeamIds(TeamId $teamId): array
    {
        /** @var list<object{id: string}> $ancestors */
        $ancestors = DB::select(<<<'SQL'
            WITH RECURSIVE ancestor_tree AS (
                SELECT parent_team_id AS id FROM teams WHERE id = ? AND deleted_at IS NULL AND parent_team_id IS NOT NULL
                UNION ALL
                SELECT t.parent_team_id AS id FROM teams t JOIN ancestor_tree at ON t.id = at.id WHERE t.deleted_at IS NULL AND t.parent_team_id IS NOT NULL
            )
            SELECT id FROM ancestor_tree
            SQL, [$teamId->value]);

        return array_map(fn (object $ancestor): string => $ancestor->id, $ancestors);
    }

    /** @return list<string> */
    private function textSortColumns(): array
    {
        return ['name', 'slug'];
    }

    /**
     * @param  list<string>|null  $onlyIds
     * @return Builder<TeamModel>
     */
    private function baseQuery(?array $onlyIds): Builder
    {
        $query = TeamModel::query();

        if ($onlyIds !== null) {
            $query->whereIn('id', $onlyIds);
        }

        return $query;
    }
}
