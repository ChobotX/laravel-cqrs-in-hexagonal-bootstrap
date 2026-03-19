<?php

declare(strict_types=1);

namespace App\Domain\Organization;

use App\Domain\Organization\Exception\InvalidOrganizationSlugException;
use Stringable;

final readonly class OrganizationSlug implements Stringable
{
    private const string SLUG_PATTERN = '/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/';

    private const int MIN_LENGTH = 2;

    private const int MAX_LENGTH = 63;

    public function __construct(
        public string $value,
    ) {
        $length = strlen($value);

        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH || preg_match(self::SLUG_PATTERN, $value) !== 1) {
            throw new InvalidOrganizationSlugException($value);
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
