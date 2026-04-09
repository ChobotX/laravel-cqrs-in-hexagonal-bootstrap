<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;

/** @implements Query<TenantSettings> */
#[RequiresPermission('settings.tenant.read')]
final readonly class GetTenantSettingsQuery implements Query
{
    public function __construct(
        public string $tenantId,
    ) {}
}
