<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Handler enforces super-admin check internally')]
final readonly class StartImpersonationCommand implements Command
{
    public function __construct(
        public string $impersonatorId,
        public string $targetUserId,
    ) {}
}
