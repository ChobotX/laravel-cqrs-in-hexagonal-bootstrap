<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\GridPreset;

use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\Repository\GridPresetRepository;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;

final readonly class EloquentGridPresetRepository implements GridPresetRepository
{
    public function __construct(
        private GridPresetMapper $gridPresetMapper,
    ) {}

    /** @return list<GridPreset> */
    public function findVisibleByGrid(string $userId, string $gridName, array $teamIds = []): array
    {
        return array_values(
            GridPresetModel::where('grid_name', $gridName)
                ->where(function ($query) use ($userId, $teamIds): void {
                    $query->where(function ($q) use ($userId): void {
                        $q->where('user_id', $userId)->where('scope', 'personal');
                    })
                        ->orWhere('scope', 'global');

                    if ($teamIds !== []) {
                        $query->orWhere(function ($q) use ($teamIds): void {
                            $q->where('scope', 'team')->whereIn('team_id', $teamIds);
                        });
                    }
                })
                ->orderByRaw("CASE scope WHEN 'global' THEN 0 WHEN 'team' THEN 1 ELSE 2 END")
                ->orderBy('position')
                ->orderBy('created_at')
                ->get()
                ->map(fn (GridPresetModel $gridPresetModel): GridPreset => $this->gridPresetMapper->toDomain($gridPresetModel))
                ->all(),
        );
    }

    public function findById(GridPresetId $gridPresetId): ?GridPreset
    {
        $model = GridPresetModel::find($gridPresetId->value);

        if (! $model instanceof GridPresetModel) {
            return null;
        }

        return $this->gridPresetMapper->toDomain($model);
    }

    public function save(GridPreset $gridPreset): void
    {
        GridPresetModel::updateOrCreate(
            ['id' => $gridPreset->id->value],
            [
                'user_id' => $gridPreset->userId,
                'grid_name' => $gridPreset->gridName,
                'name' => $gridPreset->name,
                'filters' => $gridPreset->filters,
                'sorting' => $gridPreset->sorting,
                'search' => $gridPreset->search,
                'is_default' => $gridPreset->isDefault,
                'position' => $gridPreset->position,
                'scope' => $gridPreset->scope->value,
                'team_id' => $gridPreset->teamId,
            ],
        );
    }

    public function delete(GridPresetId $gridPresetId): void
    {
        GridPresetModel::where('id', $gridPresetId->value)->delete();
    }

    public function clearDefault(string $userId, string $gridName): void
    {
        GridPresetModel::where('user_id', $userId)
            ->where('grid_name', $gridName)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }
}
