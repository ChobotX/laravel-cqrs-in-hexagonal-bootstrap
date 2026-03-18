<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

use App\Domain\Authorization\Exception\InvalidRoleNameException;
use Stringable;

final readonly class RoleName implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new InvalidRoleNameException($value);
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
