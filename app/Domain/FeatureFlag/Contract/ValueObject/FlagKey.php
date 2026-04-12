<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\ValueObject;

use App\Domain\FeatureFlag\Exception\InvalidFlagKeyException;
use Stringable;

/**
 * Contract-level value object for flag key used across FeatureFlag commands, queries, and events.
 */
final readonly class FlagKey implements Stringable
{
    private const string PATTERN = '/^[a-z][a-z0-9-]*\.[a-z][a-z0-9-]*$/';

    public function __construct(
        /** Field `value` for this contract; see module docs for validation rules. */
        public string $value,
    ) {
        if (preg_match(self::PATTERN, $value) !== 1) {
            throw new InvalidFlagKeyException($value);
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
