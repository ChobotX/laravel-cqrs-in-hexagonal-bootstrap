<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Attribute\SkipTransaction;
use App\Contract\Command\Command;

/**
 * Command payload for create tenant in the Tenancy bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck('Tenant provisioning — no user context')]
#[SkipTransaction(reason: 'Writes to landlord connection and runs DDL/migrations')]
final readonly class CreateTenantCommand implements Command
{
    public function __construct(
        /** Human-visible label or title. */
        public string $name,
        /** Field `slug` for this contract; see module docs for validation rules. */
        public string $slug,
        /** Optional `domain`; null means not provided or not applicable. */
        public ?string $domain,
    ) {}
}
