<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\GridPreset\Contract\ValueObject\PresetShareCapabilities;

/**
 * Query for get preset share capabilities in the GridPreset bounded context; dispatched through the query bus.
 *
 * @implements Query<PresetShareCapabilities>
 */
#[SkipPermissionCheck(reason: 'Capabilities are resolved for the authenticated user only')]
final readonly class GetPresetShareCapabilitiesQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
    ) {}
}
