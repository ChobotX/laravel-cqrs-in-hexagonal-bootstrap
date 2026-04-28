<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Attribute\Sensitive;
use App\Contract\Command\Command;
use App\Domain\Tenancy\Contract\Enum\MailProvider;

/**
 * Command payload for upserting the tenant's custom mail transport. Pass `useCustom=false` to revert to the default.
 */
#[RequiresPermission('settings.tenant.update')]
final readonly class UpdateTenantMailTransportCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) of the tenant whose transport is being changed. */
        public string $tenantId,
        /** When false, removes the tenant override and the platform default takes over. */
        public bool $useCustom,
        /** Provider preset; `null` is treated as `Custom`. Ignored when `useCustom` is false. */
        public ?MailProvider $provider,
        /** SMTP host. */
        public ?string $host,
        /** SMTP port (1..65535). */
        public ?int $port,
        /** SMTP username, or null for unauthenticated submission. */
        public ?string $username,
        #[Sensitive]
        /** SMTP password / API secret. Empty string keeps the existing password (UI write-only field). */
        public ?string $password,
        /** Encryption: `tls`, `ssl`, or null. */
        public ?string $encryption,
        /** RFC 5322 from-address. */
        public ?string $fromAddress,
        /** Display name for the from-header. */
        public ?string $fromName,
    ) {}
}
