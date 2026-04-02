<?php

declare(strict_types=1);

namespace App\Domain\File;

use App\Domain\File\Exception\InvalidFileNamespaceException;
use Stringable;

final readonly class FileNamespace implements Stringable
{
    public const string SLUG_PATTERN = '/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/';

    public const int MIN_LENGTH = 2;

    public const int MAX_LENGTH = 63;

    public function __construct(
        public string $value,
    ) {
        $length = strlen($value);

        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH || preg_match(self::SLUG_PATTERN, $value) !== 1) {
            throw new InvalidFileNamespaceException($value);
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
