<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Entity;

use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\ValueObject\AllowedEmailDomains;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use DateTimeImmutable;

/**
 * Tenant-scoped SSO provider configuration as exposed to other layers via queries.
 *
 * Secret-bearing fields live inside `config` and are persisted encrypted at rest.
 */
final readonly class SsoConfiguration
{
    /** @param array<string, scalar|array<int|string, mixed>|null> $config */
    public function __construct(
        /** Stable identifier of this configuration. */
        public SsoConfigurationId $id,
        /** Authentication protocol/IdP family. */
        public ProviderType $providerType,
        /** URL-safe slug used in routes (`/auth/sso/{slug}`); unique per provider type. */
        public string $slug,
        /** Human-visible label shown on the login page button. */
        public string $displayName,
        /** When false the configuration is hidden from login and rejects callbacks. */
        public bool $enabled,
        /** When true non-admin password login is rejected for this tenant. */
        public bool $enforce,
        /** Provisioning policy for unknown subjects. */
        public JitMode $jitMode,
        /** Email-domain allowlist consulted only when `jitMode` is auto-create. */
        public AllowedEmailDomains $allowedEmailDomains,
        /** Provider-specific configuration map (client_id, secret, discovery_url, SAML metadata, ...). */
        public array $config,
        /** When the row was first created. */
        public DateTimeImmutable $createdAt,
        /** When the row was last updated. */
        public DateTimeImmutable $updatedAt,
    ) {}
}
