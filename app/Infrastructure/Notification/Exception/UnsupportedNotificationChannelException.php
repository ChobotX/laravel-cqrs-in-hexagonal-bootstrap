<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\Exception;

use RuntimeException;

final class UnsupportedNotificationChannelException extends RuntimeException
{
    public function __construct(string $channel)
    {
        parent::__construct(sprintf('No notification channel sender registered for channel [%s].', $channel));
    }
}
