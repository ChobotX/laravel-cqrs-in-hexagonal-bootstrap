<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

/** Updates an existing SsoConfiguration. */
#[RequiresPermission('sso.management.update')]
final readonly class UpdateSsoConfigurationCommand implements Command
{
    /**
     * @param  list<string>  $allowedEmailDomains
     * @param  array<string, scalar|array<int|string, mixed>|null>  $config
     */
    public function __construct(
        /** Target configuration UUID. */
        public string $id,
        /** Display name shown on the login button. */
        public string $displayName,
        /** Whether the provider is visible on the login page. */
        public bool $enabled,
        /** Whether non-admin password login is rejected when enabled. */
        public bool $enforce,
        /** JitMode backing string. */
        public string $jitMode,
        /** Lowercase email domains; empty list = no restriction. */
        public array $allowedEmailDomains,
        /** Provider-specific configuration map. */
        public array $config,
    ) {}
}
