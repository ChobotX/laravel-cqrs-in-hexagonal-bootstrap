<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Attribute\SkipTransaction;
use App\Contract\Command\Command;

/**
 * Command payload for migrate tenant in the Tenancy bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck('Tenant migration — CLI-only, no user context')]
#[SkipTransaction(reason: 'Runs migrations with own transaction management')]
final readonly class MigrateTenantCommand implements Command
{
    public function __construct(
        /** Field `slug` for this contract; see module docs for validation rules. */
        public string $slug,
    ) {}
}
