<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use RuntimeException;

final class InvalidSchemaNameException extends RuntimeException
{
    public function __construct(string $schemaName)
    {
        parent::__construct(sprintf(
            'Schema name "%s" is invalid. Must match /^[a-z_][a-z0-9_]{0,62}$/.',
            $schemaName,
        ));
    }
}
