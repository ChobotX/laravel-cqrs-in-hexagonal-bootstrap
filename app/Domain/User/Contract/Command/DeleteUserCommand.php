<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for delete user in the User bounded context; dispatched through the command bus.
 */
#[RequiresPermission('users.list.delete')]
final readonly class DeleteUserCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
    ) {}
}
