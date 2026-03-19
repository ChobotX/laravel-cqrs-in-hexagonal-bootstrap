<?php

declare(strict_types=1);

namespace App\Domain\Organization;

use App\Domain\Organization\Exception\InvalidOrganizationNameException;
use Stringable;

final readonly class OrganizationName implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new InvalidOrganizationNameException($value);
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
