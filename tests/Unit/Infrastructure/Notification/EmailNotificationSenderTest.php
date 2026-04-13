<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use App\Domain\Notification\Exception\NotificationEmailTemplateNotFoundException;
use App\Infrastructure\Notification\EmailNotificationSender;
use Tests\Helper\FakeEmailSender;
use Tests\Helper\FakeEmailTemplateRepository;
use Tests\Helper\FakeTemplateCompiler;
use Tests\Helper\FakeTranslator;

function notificationTemplate(): EmailTemplate
{
    return new EmailTemplate(
        new EmailTemplateId('550e8400-e29b-41d4-a716-446655440099'),
        new EmailTemplateType('notification'),
        new EmailTemplateLocale('en'),
        '{{title}}',
        '<p>{{body}}</p>',
        new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
        new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
    );
}

function makeLocaleTranslator(string $locale): Translator
{
    return new readonly class($locale) implements Translator
    {
        public function __construct(private string $localeValue) {}

        public function translate(string $key, array $params = []): string
        {
            return $key;
        }

        public function locale(): string
        {
            return $this->localeValue;
        }
    };
}

it('sends email using provided recipient email', function (): void {
    $templateRepository = new FakeEmailTemplateRepository([
        'notification:en' => notificationTemplate(),
    ]);
    $compiler = new FakeTemplateCompiler;
    $emailSender = new FakeEmailSender;

    $sender = new EmailNotificationSender($templateRepository, $compiler, $emailSender, new FakeTranslator);

    $sender->send(
        '550e8400-e29b-41d4-a716-446655440000',
        'john@example.com',
        'user.welcome',
        'Welcome!',
        'Body text',
        'info',
        null,
    );

    expect($emailSender->sent)->toHaveCount(1)
        ->and($emailSender->sent[0]['recipientEmail'])->toBe('john@example.com')
        ->and($emailSender->sent[0]['subject'])->toBe('Welcome!');
});

it('supports email channel', function (): void {
    $templateRepository = new FakeEmailTemplateRepository;
    $compiler = new FakeTemplateCompiler;
    $emailSender = new FakeEmailSender;

    $sender = new EmailNotificationSender($templateRepository, $compiler, $emailSender, new FakeTranslator);

    expect($sender->supports())->toBe('email');
});

it('falls back to english template when locale template is missing', function (): void {
    $templateRepository = new FakeEmailTemplateRepository([
        'notification:en' => notificationTemplate(), // only english, no 'cs'
    ]);

    $emailSender = new FakeEmailSender;

    $sender = new EmailNotificationSender(
        $templateRepository,
        new FakeTemplateCompiler,
        $emailSender,
        makeLocaleTranslator('cs'),
    );
    $sender->send(
        '550e8400-e29b-41d4-a716-446655440000',
        'jane@example.com',
        'user.welcome',
        'Welcome!',
        'Body text',
        'info',
        null,
    );

    expect($emailSender->sent)->toHaveCount(1)
        ->and($emailSender->sent[0]['recipientEmail'])->toBe('jane@example.com');
});

it('throws when notification template is not found in any locale', function (): void {
    $templateRepository = new FakeEmailTemplateRepository([]); // no templates at all
    $emailSender = new FakeEmailSender;

    $sender = new EmailNotificationSender($templateRepository, new FakeTemplateCompiler, $emailSender, new FakeTranslator);
    $sender->send(
        '550e8400-e29b-41d4-a716-446655440001',
        'bob@example.com',
        'user.welcome',
        'Title',
        'Body',
        'info',
        null,
    );
})->throws(NotificationEmailTemplateNotFoundException::class);

it('throws when template missing for non-english locale with no en fallback', function (): void {
    // Neither 'cs' nor 'en' template exists
    $templateRepository = new FakeEmailTemplateRepository([]);
    $emailSender = new FakeEmailSender;

    $sender = new EmailNotificationSender(
        $templateRepository,
        new FakeTemplateCompiler,
        $emailSender,
        makeLocaleTranslator('cs'),
    );
    $sender->send(
        '550e8400-e29b-41d4-a716-446655440002',
        'alice@example.com',
        'user.alert',
        'Alert',
        'Something happened',
        'warning',
        '/alerts/1',
    );
})->throws(NotificationEmailTemplateNotFoundException::class);
