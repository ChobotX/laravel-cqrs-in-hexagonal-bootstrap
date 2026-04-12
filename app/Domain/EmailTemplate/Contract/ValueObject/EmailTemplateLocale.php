<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\ValueObject;

use App\Domain\EmailTemplate\Exception\InvalidEmailTemplateLocaleException;
use Stringable;

/**
 * Contract-level value object for email template locale used across EmailTemplate commands, queries, and events.
 */
final readonly class EmailTemplateLocale implements Stringable
{
    private const string LOCALE_PATTERN = '/^[a-z]{2}$/';

    public function __construct(
        /** Field `value` for this contract; see module docs for validation rules. */
        public string $value,
    ) {
        if (preg_match(self::LOCALE_PATTERN, $value) !== 1) {
            throw new InvalidEmailTemplateLocaleException($value);
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
