<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\SkipAuditLog;
use App\Application\Bus\SkipTransaction;
use App\Contract\Command\Command;

#[SkipPermissionCheck('Tenant provisioning — no user context')]
#[SkipAuditLog(reason: 'CLI tenant provisioning with no user context')]
#[SkipTransaction(reason: 'Writes to landlord connection and runs DDL/migrations')]
final readonly class CreateTenantCommand implements Command
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $domain,
    ) {}
}
