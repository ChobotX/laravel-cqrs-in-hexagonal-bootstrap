<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\SkipTransaction;
use App\Contract\Command\Command;

/**
 * Command payload for request password reset in the User bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Guest action for password recovery')]
#[SkipTransaction(reason: 'No database writes, sends external email')]
final readonly class RequestPasswordResetCommand implements Command
{
    public function __construct(
        /** Email address used for lookup, delivery, or authentication flows. */
        public string $email,
    ) {}
}
