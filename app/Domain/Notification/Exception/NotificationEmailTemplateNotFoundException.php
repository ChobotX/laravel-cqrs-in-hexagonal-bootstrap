<?php

declare(strict_types=1);

namespace App\Domain\Notification\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

/**
 * Raised when the notification email wrapper template is missing for the active and fallback locales.
 */
final class NotificationEmailTemplateNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $locale,
        public readonly string $fallbackLocale,
    ) {
        parent::__construct(sprintf(
            'Notification email template of type "notification" not found for locale [%s] or fallback [%s].',
            $locale,
            $fallbackLocale,
        ));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.notification_email_template_not_found', [
            'locale' => $this->locale,
            'fallbackLocale' => $this->fallbackLocale,
        ]);
    }

    public function statusCode(): int
    {
        return HttpStatus::INTERNAL_SERVER_ERROR;
    }
}
