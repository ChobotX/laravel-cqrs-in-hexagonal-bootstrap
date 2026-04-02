<?php

declare(strict_types=1);

namespace App\Domain\File;

use App\Domain\File\Exception\InvalidStoragePathException;
use Stringable;

final readonly class StoragePath implements Stringable
{
    public function __construct(
        public string $value,
    ) {
        if (trim($value) === '') {
            throw new InvalidStoragePathException($value);
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
