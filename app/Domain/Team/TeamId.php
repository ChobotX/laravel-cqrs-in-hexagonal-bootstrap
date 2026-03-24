<?php

declare(strict_types=1);

namespace App\Domain\Team;

use App\Domain\Team\Exception\InvalidTeamIdException;
use Stringable;

final readonly class TeamId implements Stringable
{
    private const string UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    public function __construct(
        public string $value,
    ) {
        if (preg_match(self::UUID_PATTERN, $value) !== 1) {
            throw new InvalidTeamIdException($value);
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
