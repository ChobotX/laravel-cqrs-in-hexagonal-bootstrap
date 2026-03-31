<?php

declare(strict_types=1);

namespace App\Domain\Notification\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidNotificationLinkException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $invalidValue)
    {
        parent::__construct(sprintf('Value [%s] is not a valid notification link.', $invalidValue));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.invalid_notification_link', ['value' => $this->invalidValue]);
    }

    public function statusCode(): int
    {
        return 422;
    }
}
