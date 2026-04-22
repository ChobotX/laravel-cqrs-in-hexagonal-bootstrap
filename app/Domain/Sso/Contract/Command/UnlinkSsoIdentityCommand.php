<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/** Removes a UserSsoIdentity row, breaking the link between a user and an IdP subject. */
#[RequiresPermission('sso.identities.unlink')]
final readonly class UnlinkSsoIdentityCommand implements Command
{
    public function __construct(
        /** Identity row UUID to remove. */
        public string $id,
    ) {}
}
