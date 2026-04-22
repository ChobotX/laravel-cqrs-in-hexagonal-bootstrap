<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for remove label in the Label bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Label removal is enforced per-entity by the calling controller')]
final readonly class RemoveLabelCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $labelId,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $labelableId,
    ) {}
}
