<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Entity;

use App\Domain\GridPreset\Contract\Enum\PresetScope;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;

/**
 * Immutable read-model snapshot of a Grid Preset returned from queries in the GridPreset context.
 */
final readonly class GridPreset
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public GridPresetId $id,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Human-visible label or title. */
        public string $gridName,
        /** Human-visible label or title. */
        public string $name,
        /** Field `filters` for this contract; see module docs for validation rules. */
        public string $filters,
        /** Field `sorting` for this contract; see module docs for validation rules. */
        public string $sorting,
        /** Field `search` for this contract; see module docs for validation rules. */
        public string $search,
        /** Boolean flag for this state or capability. */
        public bool $isDefault,
        /** Field `position` for this contract; see module docs for validation rules. */
        public int $position,
        /** Field `scope` for this contract; see module docs for validation rules. */
        public PresetScope $scope = PresetScope::Personal,
        /** Optional team identifier when absent. */
        public ?string $teamId = null,
    ) {}
}
