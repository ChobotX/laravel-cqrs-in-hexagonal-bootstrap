<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for delete grid preset in the GridPreset bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Users manage only their own grid presets')]
final readonly class DeleteGridPresetCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
    ) {}
}
