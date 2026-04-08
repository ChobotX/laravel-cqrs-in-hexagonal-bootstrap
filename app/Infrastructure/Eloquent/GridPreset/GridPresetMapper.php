<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\GridPreset;

use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\Enum\PresetScope;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;

final readonly class GridPresetMapper
{
    public function toDomain(GridPresetModel $gridPresetModel): GridPreset
    {
        return new GridPreset(
            new GridPresetId($gridPresetModel->id),
            $gridPresetModel->user_id,
            $gridPresetModel->grid_name,
            $gridPresetModel->name,
            $gridPresetModel->filters,
            $gridPresetModel->sorting,
            $gridPresetModel->search,
            $gridPresetModel->is_default,
            $gridPresetModel->position,
            PresetScope::from($gridPresetModel->scope),
            $gridPresetModel->team_id,
        );
    }
}
