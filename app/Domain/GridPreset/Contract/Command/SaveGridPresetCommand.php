<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for save grid preset in the GridPreset bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Users manage only their own grid presets')]
final readonly class SaveGridPresetCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
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
        public string $scope = 'personal',
        /** Optional team identifier when absent. */
        public ?string $teamId = null,
    ) {}
}
