<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Tenancy\Contract\Service\TenantAdminInitializer;

final class FakeTenantAdminInitializer implements TenantAdminInitializer
{
    public ?string $initializedAdminId = null;

    public ?string $initializedAdminName = null;

    public ?string $initializedAdminEmail = null;

    public function initialize(string $adminId, string $adminName, string $adminEmail): void
    {
        $this->initializedAdminId = $adminId;
        $this->initializedAdminName = $adminName;
        $this->initializedAdminEmail = $adminEmail;
    }
}
