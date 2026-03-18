<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use RuntimeException;

final class HandlerNotFoundException extends RuntimeException
{
    public static function forCommand(string $commandClass): self
    {
        return new self('No handler registered for '.$commandClass);
    }

    public static function forQuery(string $queryClass): self
    {
        return new self('No handler registered for '.$queryClass);
    }
}
