<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\SkipTransaction;
use App\Contract\Command\Command;

#[RequiresPermission('users.list.update')]
#[SkipTransaction(reason: 'No database writes, sends external email')]
final readonly class ResendUserInviteCommand implements Command
{
    public function __construct(
        public string $userId,
    ) {}
}
