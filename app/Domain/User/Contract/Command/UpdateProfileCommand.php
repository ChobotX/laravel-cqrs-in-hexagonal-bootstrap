<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Contract\Attribute\Sensitive;
use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for update profile in the User bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Profile self-edit is available to all authenticated users')]
final readonly class UpdateProfileCommand implements Command
{
    /**
     * @param  ?string  $email  null = not submitted (field hidden), handler decides whether to apply based on authorization
     * @param  ?string  $rawPassword  null = no change
     */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Human-visible label or title. */
        public string $name,
        /** Email address used for lookup, delivery, or authentication flows. */
        public ?string $email = null,
        #[Sensitive]
        /** Password material as defined by the command (plain or hashed per handler contract). */
        public ?string $rawPassword = null,
        /** Optional avatarFile identifier when absent. */
        public ?string $avatarFileId = null,
    ) {}
}
