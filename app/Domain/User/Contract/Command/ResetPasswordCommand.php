<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\Sensitive;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Guest action for password recovery')]
final readonly class ResetPasswordCommand implements Command
{
    public function __construct(
        public string $email,
        #[Sensitive]
        public string $token,
        #[Sensitive]
        public string $rawPassword,
    ) {}
}
