<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\GridPreset\Contract\ValueObject\PresetShareCapabilities;

/** @implements Query<PresetShareCapabilities> */
#[SkipPermissionCheck(reason: 'Capabilities are resolved for the authenticated user only')]
final readonly class GetPresetShareCapabilitiesQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}
