<?php

declare(strict_types=1);

namespace App\Domain\File\ValueObject;

use App\Domain\File\Exception\InvalidFileVersionException;

final readonly class FileVersion
{
    public const int MIN_VERSION = 1;

    public function __construct(
        public int $value,
    ) {
        if ($value < self::MIN_VERSION) {
            throw new InvalidFileVersionException($value);
        }
    }
}
