<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\Sensitive;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Authenticated users verify own challenge')]
final readonly class VerifyTwoFactorChallengeCommand implements Command
{
    public function __construct(
        public string $userId,
        public string $method,
        #[Sensitive]
        public string $code,
    ) {}
}
