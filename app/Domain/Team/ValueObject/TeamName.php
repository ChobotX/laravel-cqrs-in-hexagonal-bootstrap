<?php

declare(strict_types=1);

namespace App\Domain\Team\ValueObject;

use App\Domain\Team\Exception\InvalidTeamNameException;
use Stringable;

final readonly class TeamName implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new InvalidTeamNameException($value);
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
