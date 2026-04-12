<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\GridPreset\Contract\Entity\GridPreset;

/**
 * Query for get default grid preset in the GridPreset bounded context; dispatched through the query bus.
 *
 * @implements Query<?GridPreset>
 */
#[SkipPermissionCheck(reason: 'Users view only their own grid presets')]
final readonly class GetDefaultGridPresetQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Human-visible label or title. */
        public string $gridName,
    ) {}
}
