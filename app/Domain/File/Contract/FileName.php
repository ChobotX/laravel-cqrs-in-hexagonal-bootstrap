<?php

declare(strict_types=1);

namespace App\Domain\File\Contract;

use App\Domain\File\Exception\InvalidFileNameException;
use Stringable;

final readonly class FileName implements Stringable
{
    public const int MAX_LENGTH = 255;

    public string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '' || strlen($trimmed) > self::MAX_LENGTH) {
            throw new InvalidFileNameException($value);
        }

        $this->value = $trimmed;
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
