<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for remove permission override in the Authorization bounded context; dispatched through the command bus.
 */
#[RequiresPermission('users.roles.update')]
final readonly class RemovePermissionOverrideCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Field `permission` for this contract; see module docs for validation rules. */
        public string $permission,
    ) {}
}
