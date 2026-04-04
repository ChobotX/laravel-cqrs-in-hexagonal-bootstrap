<?php

declare(strict_types=1);

namespace App\Domain\Registry\ValueObject;

use App\Domain\Registry\Exception\InvalidDefinitionSlugException;
use Stringable;

final readonly class DefinitionSlug implements Stringable
{
    private const string SLUG_PATTERN = '/^[a-z][a-z0-9_]*$/';

    public function __construct(
        public string $value,
    ) {
        if (preg_match(self::SLUG_PATTERN, $value) !== 1) {
            throw new InvalidDefinitionSlugException($value);
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
