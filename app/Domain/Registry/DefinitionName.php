<?php

declare(strict_types=1);

namespace App\Domain\Registry;

use App\Domain\Registry\Exception\InvalidDefinitionNameException;
use Stringable;

final readonly class DefinitionName implements Stringable
{
    public const int MAX_LENGTH = 255;

    public string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '' || strlen($trimmed) > self::MAX_LENGTH) {
            throw new InvalidDefinitionNameException($value);
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
