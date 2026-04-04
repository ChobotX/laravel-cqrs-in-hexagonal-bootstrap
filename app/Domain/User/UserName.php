<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Contract\Exception\InvalidUserDataException;
use Stringable;

final readonly class UserName implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new InvalidUserDataException('User name must not be empty.');
        }

        $this->value = $trimmed;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
