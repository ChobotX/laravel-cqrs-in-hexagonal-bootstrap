<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Entity;

use App\Domain\GridPreset\Contract\Enum\PresetScope;
use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;

final readonly class GridPreset
{
    public function __construct(
        public GridPresetId $id,
        public string $userId,
        public string $gridName,
        public string $name,
        public string $filters,
        public string $sorting,
        public string $search,
        public bool $isDefault,
        public int $position,
        public PresetScope $scope = PresetScope::Personal,
        public ?string $teamId = null,
    ) {}
}
