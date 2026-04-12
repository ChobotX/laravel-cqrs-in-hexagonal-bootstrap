<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for set default grid preset in the GridPreset bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Users manage only their own grid presets')]
final readonly class SetDefaultGridPresetCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Human-visible label or title. */
        public string $gridName,
    ) {}
}
