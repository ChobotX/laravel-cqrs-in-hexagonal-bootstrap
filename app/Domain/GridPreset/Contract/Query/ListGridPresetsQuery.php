<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\GridPreset\Contract\Entity\GridPreset;

/**
 * @implements Query<list<GridPreset>>
 */
#[SkipPermissionCheck(reason: 'Users view only their own grid presets')]
final readonly class ListGridPresetsQuery implements Query
{
    public function __construct(
        public string $userId,
        public string $gridName,
    ) {}
}
