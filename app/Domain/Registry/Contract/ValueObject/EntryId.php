<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\ValueObject;

use App\Domain\Registry\Exception\InvalidEntryIdException;
use Stringable;

/**
 * Contract-level value object for entry id used across Registry commands, queries, and events.
 */
final readonly class EntryId implements Stringable
{
    private const string UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    public function __construct(
        /** Field `value` for this contract; see module docs for validation rules. */
        public string $value,
    ) {
        if (preg_match(self::UUID_PATTERN, $value) !== 1) {
            throw new InvalidEntryIdException($value);
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
