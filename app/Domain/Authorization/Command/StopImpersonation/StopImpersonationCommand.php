<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\StopImpersonation;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Users must always be able to stop their own impersonation')]
final readonly class StopImpersonationCommand implements Command
{
    public function __construct(
        public string $impersonatorId,
    ) {}
}
