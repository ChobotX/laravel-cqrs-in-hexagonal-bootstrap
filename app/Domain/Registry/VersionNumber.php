<?php

declare(strict_types=1);

namespace App\Domain\Registry;

use App\Domain\Registry\Exception\InvalidVersionNumberException;
use Stringable;

final readonly class VersionNumber implements Stringable
{
    public function __construct(
        public int $value,
    ) {
        if ($value < 1) {
            throw new InvalidVersionNumberException($value);
        }
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
