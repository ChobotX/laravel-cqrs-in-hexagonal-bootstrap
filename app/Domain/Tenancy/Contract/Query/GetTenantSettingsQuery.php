<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;

/**
 * Query for get tenant settings in the Tenancy bounded context; dispatched through the query bus.
 *
 * @implements Query<TenantSettings>
 */
#[RequiresPermission('settings.tenant.read')]
final readonly class GetTenantSettingsQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $tenantId,
    ) {}
}
