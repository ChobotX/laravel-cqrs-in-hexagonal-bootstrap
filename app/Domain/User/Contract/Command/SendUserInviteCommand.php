<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\SkipTransaction;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Dispatched internally by event handler after user creation')]
#[SkipTransaction(reason: 'No database writes, sends external email')]
final readonly class SendUserInviteCommand implements Command
{
    public function __construct(
        public string $userId,
    ) {}
}
