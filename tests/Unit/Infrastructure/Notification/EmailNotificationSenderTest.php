<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use App\Infrastructure\Notification\EmailNotificationSender;
use Psr\Log\NullLogger;
use Tests\Helper\FakeEmailSender;
use Tests\Helper\FakeEmailTemplateRepository;
use Tests\Helper\FakeQueryBus;
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

it('sends email to resolved user', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $queryBus = new FakeQueryBus([
        GetUserByIdQuery::class => $user,
    ]);

    $templateRepository = new FakeEmailTemplateRepository([
        'notification:en' => notificationTemplate(),
    ]);
    $compiler = new FakeTemplateCompiler;
    $emailSender = new FakeEmailSender;

    $sender = new EmailNotificationSender($queryBus, $templateRepository, $compiler, $emailSender, new FakeTranslator, new NullLogger);

    $sender->send('550e8400-e29b-41d4-a716-446655440000', 'user.welcome', 'Welcome!', 'Body text', 'info', null);

    expect($emailSender->sent)->toHaveCount(1)
        ->and($emailSender->sent[0]['recipientEmail'])->toBe('john@example.com')
        ->and($emailSender->sent[0]['subject'])->toBe('Welcome!');
});

it('supports email channel', function (): void {
    $queryBus = new FakeQueryBus;
    $templateRepository = new FakeEmailTemplateRepository;
    $compiler = new FakeTemplateCompiler;
    $emailSender = new FakeEmailSender;

    $sender = new EmailNotificationSender($queryBus, $templateRepository, $compiler, $emailSender, new FakeTranslator, new NullLogger);

    expect($sender->supports())->toBe('email');
});
