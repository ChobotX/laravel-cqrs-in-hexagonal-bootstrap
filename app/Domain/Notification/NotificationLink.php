<?php

declare(strict_types=1);

namespace App\Domain\Notification;

use App\Domain\Notification\Exception\InvalidNotificationLinkException;
use Stringable;

final readonly class NotificationLink implements Stringable
{
    public function __construct(
        public string $value,
    ) {
        if (trim($value) === '' || ! str_starts_with($value, '/')) {
            throw new InvalidNotificationLinkException($value);
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
