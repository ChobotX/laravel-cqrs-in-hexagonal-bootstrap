<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/** Deletes an SsoConfiguration and unlinks all user identities issued under it. */
#[RequiresPermission('sso.management.delete')]
final readonly class DeleteSsoConfigurationCommand implements Command
{
    public function __construct(
        /** Target configuration UUID. */
        public string $id,
    ) {}
}
