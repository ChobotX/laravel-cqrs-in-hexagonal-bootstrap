<?php

declare(strict_types=1);

namespace App\Domain\Notification;

use App\Domain\Notification\Exception\InvalidNotificationTypeException;
use Stringable;

final readonly class NotificationType implements Stringable
{
    private const string SLUG_PATTERN = '/^[a-z][a-z0-9_.]*$/';

    public function __construct(
        public string $value,
    ) {
        if (preg_match(self::SLUG_PATTERN, $value) !== 1) {
            throw new InvalidNotificationTypeException($value);
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
