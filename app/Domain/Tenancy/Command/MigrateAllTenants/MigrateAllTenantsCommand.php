<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Command\MigrateAllTenants;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck('Tenant migration — CLI-only, no user context')]
final readonly class MigrateAllTenantsCommand implements Command {}
