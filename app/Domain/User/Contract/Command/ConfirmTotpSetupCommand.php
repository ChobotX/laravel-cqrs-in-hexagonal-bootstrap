<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Contract\Attribute\Sensitive;
use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Users can manage own two-factor setup')]
final readonly class ConfirmTotpSetupCommand implements Command
{
    public function __construct(
        public string $userId,
        #[Sensitive]
        public string $code,
    ) {}
}
