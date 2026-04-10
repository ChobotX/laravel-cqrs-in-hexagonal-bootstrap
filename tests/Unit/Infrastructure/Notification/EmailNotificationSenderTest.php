<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
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

/** @return object{warningCalls: list<array{message: string, context: array<mixed>}>}&NullLogger */
function makeSpyLogger(): NullLogger
{
    return new class extends NullLogger
    {
        /** @var list<array{message: string, context: array<mixed>}> */
        public array $warningCalls = [];

        /** @param array<mixed> $context */
        public function warning(string|Stringable $message, array $context = []): void
        {
            $this->warningCalls[] = ['message' => (string) $message, 'context' => $context];
        }
    };
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

it('falls back to english template when locale template is missing', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('Jane Doe'),
        new Email('jane@example.com'),
    );

    $queryBus = new FakeQueryBus([
        GetUserByIdQuery::class => $user,
    ]);

    $templateRepository = new FakeEmailTemplateRepository([
        'notification:en' => notificationTemplate(), // only english, no 'cs'
    ]);

    $emailSender = new FakeEmailSender;

    $sender = new EmailNotificationSender(
        $queryBus,
        $templateRepository,
        new FakeTemplateCompiler,
        $emailSender,
        makeLocaleTranslator('cs'),
        new NullLogger,
    );
    $sender->send('550e8400-e29b-41d4-a716-446655440000', 'user.welcome', 'Welcome!', 'Body text', 'info', null);

    expect($emailSender->sent)->toHaveCount(1)
        ->and($emailSender->sent[0]['recipientEmail'])->toBe('jane@example.com');
});

it('logs warning and skips email when notification template is not found in any locale', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440001'),
        new UserName('Bob Smith'),
        new Email('bob@example.com'),
    );

    $queryBus = new FakeQueryBus([
        GetUserByIdQuery::class => $user,
    ]);

    $templateRepository = new FakeEmailTemplateRepository([]); // no templates at all
    $emailSender = new FakeEmailSender;
    $logger = makeSpyLogger();

    $sender = new EmailNotificationSender($queryBus, $templateRepository, new FakeTemplateCompiler, $emailSender, new FakeTranslator, $logger);
    $sender->send('550e8400-e29b-41d4-a716-446655440001', 'user.welcome', 'Title', 'Body', 'info', null);

    expect($emailSender->sent)->toBeEmpty()
        ->and($logger->warningCalls)->toHaveCount(1)
        ->and($logger->warningCalls[0]['message'])->toContain('not found');
});

it('logs warning and skips email when template missing for non-english locale with no en fallback', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440002'),
        new UserName('Alice'),
        new Email('alice@example.com'),
    );

    $queryBus = new FakeQueryBus([
        GetUserByIdQuery::class => $user,
    ]);

    // Neither 'cs' nor 'en' template exists
    $templateRepository = new FakeEmailTemplateRepository([]);
    $emailSender = new FakeEmailSender;
    $logger = makeSpyLogger();

    $sender = new EmailNotificationSender(
        $queryBus,
        $templateRepository,
        new FakeTemplateCompiler,
        $emailSender,
        makeLocaleTranslator('cs'),
        $logger,
    );
    $sender->send('550e8400-e29b-41d4-a716-446655440002', 'user.alert', 'Alert', 'Something happened', 'warning', '/alerts/1');

    expect($emailSender->sent)->toBeEmpty()
        ->and($logger->warningCalls)->toHaveCount(1)
        ->and($logger->warningCalls[0]['context']['recipientId'])->toBe('550e8400-e29b-41d4-a716-446655440002');
});
