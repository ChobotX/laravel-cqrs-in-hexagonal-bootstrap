<?php

declare(strict_types=1);

namespace App\Domain\User\Command\UpdateProfile;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Profile self-edit is available to all authenticated users')]
final readonly class UpdateProfileCommand implements Command
{
    public function __construct(
        public string $userId,
        public string $name,
        public ?string $rawPassword,
    ) {}
}
