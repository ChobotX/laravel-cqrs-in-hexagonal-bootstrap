<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for assign label in the Label bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Label assignment is enforced per-entity by the calling controller')]
final readonly class AssignLabelCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $labelId,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $labelableId,
        /** Field `expectedNamespace` for this contract; see module docs for validation rules. */
        public string $expectedNamespace,
    ) {}
}
