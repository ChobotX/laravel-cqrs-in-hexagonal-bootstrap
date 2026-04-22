<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Contract\Attribute\Sensitive;
use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for accept invite in the User bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Guest action via signed invite link')]
final readonly class AcceptInviteCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        #[Sensitive]
        /** Password material as defined by the command (plain or hashed per handler contract). */
        public string $rawPassword,
    ) {}
}
