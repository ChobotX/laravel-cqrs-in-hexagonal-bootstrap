<?php

declare(strict_types=1);

namespace App\Infrastructure\Translation;

use RuntimeException;

final class UnexpectedTranslationTypeException extends RuntimeException
{
    public static function expectedString(string $key): self
    {
        return new self(sprintf("Expected translation for key '%s' to be a string.", $key));
    }
}
