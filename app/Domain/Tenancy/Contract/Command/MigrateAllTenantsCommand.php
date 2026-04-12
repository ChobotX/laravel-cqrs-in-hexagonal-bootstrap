<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\SkipTransaction;
use App\Contract\Command\Command;

/**
 * Command payload for migrate all tenants in the Tenancy bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck('Tenant migration — CLI-only, no user context')]
#[SkipTransaction(reason: 'Iterates tenants with per-tenant migration cycles')]
final readonly class MigrateAllTenantsCommand implements Command {}
