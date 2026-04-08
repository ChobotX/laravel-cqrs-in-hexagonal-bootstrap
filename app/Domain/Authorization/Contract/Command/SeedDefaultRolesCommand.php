<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\SkipAuditLog;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'System bootstrap command run during setup')]
#[SkipAuditLog(reason: 'System bootstrap command with no user context')]
final readonly class SeedDefaultRolesCommand implements Command {}
