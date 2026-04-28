<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Repository;

use App\Domain\Tenancy\Contract\ValueObject\MailTransport;

/**
 * Persistence + default-resolution port for the per-tenant mail transport. Implementations live in Infrastructure.
 */
interface TenantMailTransportRepository
{
    /** Tenant-configured transport, or null when the tenant has not overridden the default. */
    public function findCustom(): ?MailTransport;

    /** Effective default built from the global mail config. */
    public function default(): MailTransport;

    public function save(MailTransport $mailTransport): void;

    /** Removes the tenant override so the default applies again. */
    public function clear(): void;
}
