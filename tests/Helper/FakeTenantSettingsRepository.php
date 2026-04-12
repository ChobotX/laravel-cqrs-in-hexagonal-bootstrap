<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Tenancy\Contract\Repository\TenantSettingsRepository;
use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;
use SplFileInfo;

final class FakeTenantSettingsRepository implements TenantSettingsRepository
{
    public ?string $updatedName = null;

    public ?SplFileInfo $updatedLogo = null;

    public bool $removedLogo = false;

    public ?string $updatedDisplayTimezone = null;

    public ?string $updatedTenantId = null;

    /** @param array<string, TenantSettings> $settings */
    public function __construct(
        private array $settings = [],
    ) {}

    public function findByTenantId(string $tenantId): ?TenantSettings
    {
        return $this->settings[$tenantId] ?? null;
    }

    public function updateSettings(
        string $tenantId,
        string $name,
        ?SplFileInfo $logo,
        bool $removeLogo,
        ?string $displayTimezone,
    ): void {
        $this->updatedTenantId = $tenantId;
        $this->updatedName = $name;
        $this->updatedLogo = $logo;
        $this->removedLogo = $removeLogo;
        $this->updatedDisplayTimezone = $displayTimezone;

        $previous = $this->settings[$tenantId] ?? null;

        $this->settings[$tenantId] = new TenantSettings(
            name: $name,
            logoUrl: $removeLogo ? null : ($previous?->logoUrl),
            displayTimezone: $displayTimezone,
        );
    }
}
