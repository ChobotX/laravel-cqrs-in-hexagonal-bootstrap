<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Service;

use App\Domain\User\Contract\Event\UserCreated;

/**
 * Domain service contract for tenant admin in the Tenancy bounded context.
 */
interface TenantAdminInitializer
{
    /**
     * Seeds tenant defaults, provisions roles, creates the admin user, and returns the domain event
     * the caller must collect (command handler owns {@see \App\Contract\Event\EventCollector}).
     */
    public function initialize(string $adminId, string $adminName, string $adminEmail): UserCreated;
}
