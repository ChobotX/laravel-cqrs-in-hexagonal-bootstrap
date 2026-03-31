<?php

declare(strict_types=1);

namespace App\Domain\Notification\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class NotificationOwnershipException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $notificationId)
    {
        parent::__construct(sprintf('User does not own notification [%s].', $notificationId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.notification_ownership');
    }

    public function statusCode(): int
    {
        return HttpStatus::FORBIDDEN;
    }
}
