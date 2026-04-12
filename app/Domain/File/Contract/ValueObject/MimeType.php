<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\ValueObject;

use App\Domain\File\Exception\InvalidMimeTypeException;
use Stringable;

/**
 * Contract-level value object for mime type used across File commands, queries, and events.
 */
final readonly class MimeType implements Stringable
{
    public const string MIME_PATTERN = '/^[a-z0-9][a-z0-9!#$&\-^_]*\/[a-z0-9][a-z0-9!#$&\-^_.+]*$/i';

    public function __construct(
        /** Field `value` for this contract; see module docs for validation rules. */
        public string $value,
    ) {
        if (preg_match(self::MIME_PATTERN, $value) !== 1) {
            throw new InvalidMimeTypeException($value);
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
