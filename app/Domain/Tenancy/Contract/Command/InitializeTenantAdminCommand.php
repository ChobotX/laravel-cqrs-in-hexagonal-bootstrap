<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\SkipAuditLog;
use App\Contract\Command\Command;

#[SkipPermissionCheck('System initialization during tenant registration — no user context')]
#[SkipAuditLog(reason: 'System initialization during tenant registration — no user context')]
final readonly class InitializeTenantAdminCommand implements Command
{
    public function __construct(
        public string $tenantSlug,
        public string $adminId,
        public string $adminName,
        public string $adminEmail,
    ) {}
}
