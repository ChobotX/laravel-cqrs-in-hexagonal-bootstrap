<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use RuntimeException;

final class MissingTenantOptionException extends RuntimeException
{
    public function __construct(string $commandClass)
    {
        parent::__construct(sprintf(
            'Command %s requires --tenant option (marked with #[TenantAwareCommand]).',
            $commandClass,
        ));
    }
}
