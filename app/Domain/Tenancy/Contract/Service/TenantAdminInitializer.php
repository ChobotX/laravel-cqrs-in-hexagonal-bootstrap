<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Service;

interface TenantAdminInitializer
{
    public function initialize(string $adminId, string $adminName, string $adminEmail): void;
}
