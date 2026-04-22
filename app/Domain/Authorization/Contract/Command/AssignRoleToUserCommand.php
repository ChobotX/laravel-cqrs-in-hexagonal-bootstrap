<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for assign role to user in the Authorization bounded context; dispatched through the command bus.
 */
#[RequiresPermission('users.roles.update')]
final readonly class AssignRoleToUserCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $roleId,
    ) {}
}
