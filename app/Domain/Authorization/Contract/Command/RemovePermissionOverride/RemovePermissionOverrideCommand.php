<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command\RemovePermissionOverride;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('users.roles.update')]
final readonly class RemovePermissionOverrideCommand implements Command
{
    public function __construct(
        public string $userId,
        public string $permission,
    ) {}
}
