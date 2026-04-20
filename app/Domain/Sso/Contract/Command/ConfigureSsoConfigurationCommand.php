<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Creates a new SsoConfiguration in the current tenant.
 */
#[RequiresPermission('sso.management.create')]
final readonly class ConfigureSsoConfigurationCommand implements Command
{
    /**
     * @param  list<string>  $allowedEmailDomains
     * @param  array<string, scalar|array<int|string, mixed>|null>  $config
     */
    public function __construct(
        /** New configuration UUID. */
        public string $id,
        /** ProviderType backing string (e.g. `oidc`). */
        public string $providerType,
        /** URL-safe slug; unique per provider type. */
        public string $slug,
        /** Display name shown on the login button. */
        public string $displayName,
        /** Whether the provider is immediately visible on the login page. */
        public bool $enabled,
        /** Whether non-admin password login should be rejected once enabled. */
        public bool $enforce,
        /** JitMode backing string (e.g. `invited_only`). */
        public string $jitMode,
        /** Lowercase email domains; empty list = no restriction. */
        public array $allowedEmailDomains,
        /** Provider-specific configuration map. */
        public array $config,
    ) {}
}
