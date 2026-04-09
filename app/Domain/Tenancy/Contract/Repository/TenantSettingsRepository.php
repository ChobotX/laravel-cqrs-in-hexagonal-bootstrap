<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Repository;

use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;
use SplFileInfo;

interface TenantSettingsRepository
{
    public function findByTenantId(string $tenantId): ?TenantSettings;

    public function updateSettings(string $tenantId, string $name, ?SplFileInfo $logo, bool $removeLogo): void;
}
