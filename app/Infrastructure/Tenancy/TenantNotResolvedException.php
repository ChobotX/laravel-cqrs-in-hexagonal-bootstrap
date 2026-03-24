<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use RuntimeException;

final class TenantNotResolvedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Tenant context has not been resolved.');
    }
}
