<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for update user in the User bounded context; dispatched through the command bus.
 */
#[RequiresPermission('users.list.update')]
final readonly class UpdateUserCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
        /** Human-visible label or title. */
        public string $name,
        /** Email address used for lookup, delivery, or authentication flows. */
        public string $email,
        /** Optional avatarFile identifier when absent. */
        public ?string $avatarFileId = null,
    ) {}
}
