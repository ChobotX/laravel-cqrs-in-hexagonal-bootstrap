<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for seed default roles in the Authorization bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'System bootstrap command run during setup')]
final readonly class SeedDefaultRolesCommand implements Command {}
