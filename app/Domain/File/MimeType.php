<?php

declare(strict_types=1);

namespace App\Domain\File;

use App\Domain\File\Exception\InvalidMimeTypeException;
use Stringable;

final readonly class MimeType implements Stringable
{
    public const string MIME_PATTERN = '/^[a-z0-9][a-z0-9!#$&\-^_]*\/[a-z0-9][a-z0-9!#$&\-^_.+]*$/i';

    public function __construct(
        public string $value,
    ) {
        if (preg_match(self::MIME_PATTERN, $value) !== 1) {
            throw new InvalidMimeTypeException($value);
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
