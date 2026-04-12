<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\ValueObject;

use App\Domain\Team\Exception\InvalidTeamSlugException;
use Stringable;

/**
 * Contract-level value object for team slug used across Team commands, queries, and events.
 */
final readonly class TeamSlug implements Stringable
{
    public const string SLUG_PATTERN = '/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/';

    public const int MIN_LENGTH = 2;

    public const int MAX_LENGTH = 63;

    public function __construct(
        /** Field `value` for this contract; see module docs for validation rules. */
        public string $value,
    ) {
        $length = strlen($value);

        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH || preg_match(self::SLUG_PATTERN, $value) !== 1) {
            throw new InvalidTeamSlugException($value);
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
