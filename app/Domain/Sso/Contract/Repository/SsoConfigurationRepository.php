<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Repository;

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;

/**
 * Persistence port for tenant SSO configurations; lives on the tenant DB connection.
 */
interface SsoConfigurationRepository
{
    /** @return list<SsoConfiguration> */
    public function all(): array;

    /** @return list<SsoConfiguration> */
    public function allEnabled(): array;

    public function findById(SsoConfigurationId $ssoConfigurationId): ?SsoConfiguration;

    public function findBySlug(ProviderType $providerType, string $slug): ?SsoConfiguration;

    /** True when any enabled configuration has the enforce flag set. */
    public function hasEnforcedConfiguration(): bool;

    public function create(SsoConfiguration $ssoConfiguration): void;

    public function update(SsoConfiguration $ssoConfiguration): void;

    public function delete(SsoConfigurationId $ssoConfigurationId): void;
}
