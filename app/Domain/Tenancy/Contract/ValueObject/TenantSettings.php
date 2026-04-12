<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\ValueObject;

/**
 * Contract-level value object for tenant settings used across Tenancy commands, queries, and events.
 */
final readonly class TenantSettings
{
    public function __construct(
        /** Human-visible label or title. */
        public string $name,
        /** Absolute or application-relative URL for clients. */
        public ?string $logoUrl,
    ) {}
}
