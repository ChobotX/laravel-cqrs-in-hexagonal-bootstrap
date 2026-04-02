<?php

declare(strict_types=1);

namespace App\Domain\User\Command\UpdateProfile;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Profile self-edit is available to all authenticated users')]
final readonly class UpdateProfileCommand implements Command
{
    /**
     * @param  ?string  $email  null = not submitted (field hidden), handler decides whether to apply based on authorization
     * @param  ?string  $rawPassword  null = no change
     */
    public function __construct(
        public string $userId,
        public string $name,
        public ?string $email = null,
        public ?string $rawPassword = null,
        public ?string $avatarFileId = null,
    ) {}
}
