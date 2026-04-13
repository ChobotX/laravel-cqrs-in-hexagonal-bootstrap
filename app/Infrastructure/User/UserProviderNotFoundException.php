<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use RuntimeException;

final class UserProviderNotFoundException extends RuntimeException
{
    public function __construct(string $driver)
    {
        parent::__construct(sprintf("User provider '%s' could not be created.", $driver));
    }
}
