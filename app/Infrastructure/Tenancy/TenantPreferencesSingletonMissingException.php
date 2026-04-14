<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use RuntimeException;

final class TenantPreferencesSingletonMissingException extends RuntimeException
{
    public function __construct(string $tenantSlug)
    {
        parent::__construct(sprintf(
            'tenant_preferences singleton row missing after tenant migrations (slug: %s).',
            $tenantSlug,
        ));
    }
}
