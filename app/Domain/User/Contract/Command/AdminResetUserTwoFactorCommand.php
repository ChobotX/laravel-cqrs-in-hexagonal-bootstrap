<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('user_recovery.two_factor.update')]
final readonly class AdminResetUserTwoFactorCommand implements Command
{
    public function __construct(
        public string $targetUserId,
    ) {}
}
