<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Authenticated users can request own challenge')]
final readonly class IssueEmailTwoFactorChallengeCommand implements Command
{
    public function __construct(
        public string $userId,
    ) {}
}
