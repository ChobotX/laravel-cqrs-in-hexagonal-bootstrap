<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Exception;

use InvalidArgumentException;

final class InvalidRouteParameterException extends InvalidArgumentException
{
    public static function expectedString(string $param): self
    {
        return new self(sprintf('Route parameter [%s] is not a string.', $param));
    }
}
