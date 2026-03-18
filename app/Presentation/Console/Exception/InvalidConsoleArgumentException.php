<?php

declare(strict_types=1);

namespace App\Presentation\Console\Exception;

use InvalidArgumentException;

final class InvalidConsoleArgumentException extends InvalidArgumentException
{
    public static function expectedString(string $key): self
    {
        return new self(sprintf("Expected argument/option '%s' to be a string.", $key));
    }
}
