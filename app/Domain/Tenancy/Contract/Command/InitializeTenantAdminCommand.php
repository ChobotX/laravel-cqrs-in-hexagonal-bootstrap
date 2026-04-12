<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for initialize tenant admin in the Tenancy bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck('System initialization during tenant registration — no user context')]
final readonly class InitializeTenantAdminCommand implements Command
{
    public function __construct(
        /** URL-safe unique key for routing or display. */
        public string $tenantSlug,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $adminId,
        /** Human-visible label or title. */
        public string $adminName,
        /** Email address used for lookup, delivery, or authentication flows. */
        public string $adminEmail,
    ) {}
}
