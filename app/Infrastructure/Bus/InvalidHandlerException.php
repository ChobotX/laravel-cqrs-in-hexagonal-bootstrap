<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use RuntimeException;

final class InvalidHandlerException extends RuntimeException
{
    public static function expectedType(string $handlerClass, string $expectedType): self
    {
        return new self(sprintf("Container resolved '%s' but it is not an instance of %s.", $handlerClass, $expectedType));
    }

    public static function unexpectedMessageType(string $actualClass, string $expectedType): self
    {
        return new self(sprintf("Pipeline received '%s' but expected an instance of %s.", $actualClass, $expectedType));
    }
}
