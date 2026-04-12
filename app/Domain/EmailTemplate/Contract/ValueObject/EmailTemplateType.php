<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\ValueObject;

use App\Domain\EmailTemplate\Exception\InvalidEmailTemplateTypeException;
use Stringable;

/**
 * Contract-level value object for email template type used across EmailTemplate commands, queries, and events.
 */
final readonly class EmailTemplateType implements Stringable
{
    private const string SLUG_PATTERN = '/^[a-z][a-z0-9_]*$/';

    public function __construct(
        /** Field `value` for this contract; see module docs for validation rules. */
        public string $value,
    ) {
        if (preg_match(self::SLUG_PATTERN, $value) !== 1) {
            throw new InvalidEmailTemplateTypeException($value);
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
