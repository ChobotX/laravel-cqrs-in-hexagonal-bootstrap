<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for stop impersonation in the Authorization bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Users must always be able to stop their own impersonation')]
final readonly class StopImpersonationCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $impersonatorId,
    ) {}
}
