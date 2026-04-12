<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\SkipTransaction;
use App\Contract\Command\Command;

/**
 * Command payload for resend user invite in the User bounded context; dispatched through the command bus.
 */
#[RequiresPermission('users.list.update')]
#[SkipTransaction(reason: 'No database writes, sends external email')]
final readonly class ResendUserInviteCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
    ) {}
}
