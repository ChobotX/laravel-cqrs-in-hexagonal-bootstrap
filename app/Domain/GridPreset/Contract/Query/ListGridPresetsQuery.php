<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\GridPreset\Contract\Entity\GridPreset;

/**
 * Query for list grid presets in the GridPreset bounded context; dispatched through the query bus.
 *
 * @implements Query<list<GridPreset>>
 */
#[SkipPermissionCheck(reason: 'Users view only their own grid presets')]
final readonly class ListGridPresetsQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Human-visible label or title. */
        public string $gridName,
    ) {}
}
