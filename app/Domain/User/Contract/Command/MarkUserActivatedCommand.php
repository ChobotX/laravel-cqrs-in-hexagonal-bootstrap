<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Marks a user as activated without requiring them to set a password.
 *
 * Used when activation is driven by an out-of-band identity assertion (e.g. SSO)
 * rather than the interactive invite-accept flow.
 */
#[SkipPermissionCheck('Invoked only from trusted activation flows (SSO identity linking).')]
final readonly class MarkUserActivatedCommand implements Command
{
    public function __construct(
        /** User to mark as activated. */
        public string $userId,
    ) {}
}
