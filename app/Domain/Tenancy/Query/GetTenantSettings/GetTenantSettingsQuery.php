<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Query\GetTenantSettings;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Tenancy\TenantSettings;

/** @implements Query<TenantSettings> */
#[RequiresPermission('settings.tenant.read')]
final readonly class GetTenantSettingsQuery implements Query
{
    public function __construct(
        public string $tenantId,
    ) {}
}
