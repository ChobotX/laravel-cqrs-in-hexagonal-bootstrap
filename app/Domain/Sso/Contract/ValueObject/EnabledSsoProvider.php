<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\ValueObject;

use App\Domain\Sso\Contract\Enum\ProviderType;

/**
 * Slim DTO returned by GetEnabledSsoProvidersQuery for the login page.
 *
 * Carries no secrets — safe to expose pre-auth.
 */
final readonly class EnabledSsoProvider
{
    public function __construct(
        public string $configurationId,
        public ProviderType $providerType,
        public string $slug,
        public string $displayName,
    ) {}
}
