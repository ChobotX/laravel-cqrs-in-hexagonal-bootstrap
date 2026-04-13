<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Service;

/**
 * Persists default email template rows for a new tenant schema. Implemented in Infrastructure.
 */
interface TenantDefaultEmailTemplateSeeder
{
    /** Inserts configured default templates for the active tenant connection. */
    public function seed(): void;
}
