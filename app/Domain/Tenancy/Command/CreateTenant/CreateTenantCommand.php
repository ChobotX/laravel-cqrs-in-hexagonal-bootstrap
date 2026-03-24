<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Command\CreateTenant;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck('Tenant provisioning — no user context')]
final readonly class CreateTenantCommand implements Command
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $domain,
    ) {}
}
