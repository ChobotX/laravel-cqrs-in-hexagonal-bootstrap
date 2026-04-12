<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\Sensitive;
use App\Contract\Command\Command;

/**
 * Command payload for set password in the User bounded context; dispatched through the command bus.
 */
#[RequiresPermission('users.list.update')]
final readonly class SetPasswordCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        #[Sensitive]
        /** Password material as defined by the command (plain or hashed per handler contract). */
        public string $rawPassword,
    ) {}
}
