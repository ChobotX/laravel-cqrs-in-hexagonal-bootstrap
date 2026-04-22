<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Attribute\SkipTransaction;
use App\Contract\Command\Command;

/**
 * Orchestrates tenant creation and admin initialization via one public registration dispatch.
 * Handler fans out to {@see CreateTenantCommand} and {@see InitializeTenantAdminCommand}.
 */
#[SkipPermissionCheck('Public tenant registration')]
#[SkipTransaction(reason: 'Inner CreateTenantCommand runs DDL/migrations on landlord connection')]
final readonly class RegisterTenantWithAdminCommand implements Command
{
    public function __construct(
        /** Tenant display name. */
        public string $name,
        /** URL-safe unique tenant slug. */
        public string $slug,
        /** Optional custom domain for the tenant. */
        public ?string $domain,
        /** Stable id for the admin user being provisioned. */
        public string $adminId,
        /** Admin human-visible name. */
        public string $adminName,
        /** Admin email for login. */
        public string $adminEmail,
    ) {}
}
