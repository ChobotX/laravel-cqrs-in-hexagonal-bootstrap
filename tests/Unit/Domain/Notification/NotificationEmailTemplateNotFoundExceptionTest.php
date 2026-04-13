<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Notification\Exception\NotificationEmailTemplateNotFoundException;

it('exposes locale fields', function (): void {
    $exception = new NotificationEmailTemplateNotFoundException('cs', 'en');

    expect($exception->locale)->toBe('cs')
        ->and($exception->fallbackLocale)->toBe('en');
});

it('has a technical message', function (): void {
    $exception = new NotificationEmailTemplateNotFoundException('cs', 'en');

    expect($exception->getMessage())->toBe(
        'Notification email template of type "notification" not found for locale [cs] or fallback [en].',
    );
});

it('returns translated user message', function (): void {
    $exception = new NotificationEmailTemplateNotFoundException('cs', 'en');

    $translator = new class implements Translator
    {
        /** @param array<string, string|int> $params */
        public function translate(string $key, array $params = []): string
        {
            return sprintf(
                'translated: %s [locale=%s fallback=%s]',
                $key,
                $params['locale'],
                $params['fallbackLocale'],
            );
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    expect($exception->userMessage($translator))->toBe(
        'translated: messages.exceptions.notification_email_template_not_found [locale=cs fallback=en]',
    );
});

it('returns 500 status code', function (): void {
    $exception = new NotificationEmailTemplateNotFoundException('cs', 'en');

    expect($exception->statusCode())->toBe(500);
});
