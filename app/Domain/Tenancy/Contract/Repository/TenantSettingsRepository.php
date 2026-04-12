<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Repository;

use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;
use SplFileInfo;

/**
 * Persistence port for tenant settings data in the Tenancy context; implementations live in Infrastructure.
 */
interface TenantSettingsRepository
{
    /** Loads a record or value object, or null when absent. */
    public function findByTenantId(string $tenantId): ?TenantSettings;

    /** Contract operation `updateSettings`; see infrastructure for behavior. */
    public function updateSettings(string $tenantId, string $name, ?SplFileInfo $logo, bool $removeLogo): void;
}
