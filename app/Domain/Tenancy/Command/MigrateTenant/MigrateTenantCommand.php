<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Command\MigrateTenant;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck('Tenant migration — CLI-only, no user context')]
final readonly class MigrateTenantCommand implements Command
{
    public function __construct(
        public string $slug,
    ) {}
}
