<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Service;

/**
 * Domain service contract for tenant admin in the Tenancy bounded context.
 */
interface TenantAdminInitializer
{
    /** Contract operation `initialize`; see infrastructure for behavior. */
    public function initialize(string $adminId, string $adminName, string $adminEmail): void;
}
