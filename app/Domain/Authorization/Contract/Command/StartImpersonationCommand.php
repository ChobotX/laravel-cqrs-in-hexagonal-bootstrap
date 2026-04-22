<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for start impersonation in the Authorization bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Handler enforces super-admin check internally')]
final readonly class StartImpersonationCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $impersonatorId,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $targetUserId,
    ) {}
}
