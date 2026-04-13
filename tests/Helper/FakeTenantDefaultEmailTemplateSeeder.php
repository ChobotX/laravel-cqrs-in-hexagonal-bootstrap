<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Tenancy\Contract\Service\TenantDefaultEmailTemplateSeeder;

final class FakeTenantDefaultEmailTemplateSeeder implements TenantDefaultEmailTemplateSeeder
{
    public int $seedCallCount = 0;

    public function seed(): void
    {
        $this->seedCallCount++;
    }
}
