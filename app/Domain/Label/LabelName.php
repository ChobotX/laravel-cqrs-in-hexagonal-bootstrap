<?php

declare(strict_types=1);

namespace App\Domain\Label;

use App\Domain\Label\Exception\InvalidLabelNameException;
use Stringable;

final readonly class LabelName implements Stringable
{
    public const int MAX_LENGTH = 100;

    public string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '' || strlen($trimmed) > self::MAX_LENGTH) {
            throw new InvalidLabelNameException($value);
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
