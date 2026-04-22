<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use App\Domain\EmailTemplate\Exception\EmailTemplateNotFoundException;
use App\Domain\EmailTemplate\Service\DefaultTemplatedEmailDispatcher;
use App\Domain\Tenancy\Contract\Query\GetCurrentTenantNameQuery;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeEmailSender;
use Tests\Helper\FakeEmailTemplateRepository;
use Tests\Helper\FakeIdGenerator;
use Tests\Helper\FakeQueryBus;
use Tests\Helper\FakeTemplateCompiler;

/** @param array<string, User> $usersById */
function makeDefaultDispatcher(
    FakeEmailTemplateRepository $fakeEmailTemplateRepository,
    array $usersById = [],
    ?FakeEmailSender $fakeEmailSender = null,
): DefaultTemplatedEmailDispatcher {
    return new DefaultTemplatedEmailDispatcher(
        $fakeEmailTemplateRepository,
        new FakeTemplateCompiler,
        $fakeEmailSender ?? new FakeEmailSender,
        new FakeQueryBus([
            GetCurrentTenantNameQuery::class => 'Test Org',
            GetUserByIdQuery::class => fn (GetUserByIdQuery $getUserByIdQuery): User => $usersById[$getUserByIdQuery->id]
                ?? throw new UserNotFoundException($getUserByIdQuery->id),
        ]),
        new readonly class implements App\Contract\Tracing\TraceContext
        {
            public function traceId(): ?string
            {
                return null;
            }

            public function userId(): ?string
            {
                return null;
            }

            public function tenantId(): ?string
            {
                return null;
            }
        },
        new FakeIdGenerator,
    );
}

function dispatchUser(): User
{
    return new User(
        new UserId('550e8400-e29b-41d4-a716-446655440001'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );
}

function dispatchTemplate(string $type = 'user_invite', string $locale = 'en'): EmailTemplate
{
    return new EmailTemplate(
        new EmailTemplateId('550e8400-e29b-41d4-a716-446655440099'),
        new EmailTemplateType($type),
        new EmailTemplateLocale($locale),
        'Welcome to {{tenantName}}, {{userName}}!',
        '<p>Hello {{userName}}, link: {{link}}</p>',
        new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
        new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
    );
}

it('dispatches email to user using the exact locale template', function (): void {
    $user = dispatchUser();
    $templateRepo = new FakeEmailTemplateRepository([
        'user_invite:en' => dispatchTemplate('user_invite', 'en'),
    ]);
    $users = ['550e8400-e29b-41d4-a716-446655440001' => $user];
    $emailSender = new FakeEmailSender;

    $defaultTemplatedEmailDispatcher = makeDefaultDispatcher($templateRepo, $users, $emailSender);
    $defaultTemplatedEmailDispatcher->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'en',
        ['userName' => 'John Doe', 'link' => 'https://example.com/invite'],
    );

    expect($emailSender->sent)->toHaveCount(1)
        ->and($emailSender->sent[0]['recipientEmail'])->toBe('john@example.com');
});

it('returns a TemplatedEmailSent event after dispatching', function (): void {
    $user = dispatchUser();
    $templateRepo = new FakeEmailTemplateRepository([
        'user_invite:en' => dispatchTemplate('user_invite', 'en'),
    ]);
    $users = ['550e8400-e29b-41d4-a716-446655440001' => $user];

    $defaultTemplatedEmailDispatcher = makeDefaultDispatcher($templateRepo, $users);
    $templatedEmailSent = $defaultTemplatedEmailDispatcher->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'en',
        ['userName' => 'John Doe', 'link' => 'https://example.com'],
    );

    expect($templatedEmailSent)->toBeInstanceOf(App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent::class);
});

it('falls back to english template when user locale template is missing', function (): void {
    $user = dispatchUser();
    $templateRepo = new FakeEmailTemplateRepository([
        'user_invite:en' => dispatchTemplate('user_invite', 'en'),
    ]);
    $users = ['550e8400-e29b-41d4-a716-446655440001' => $user];
    $emailSender = new FakeEmailSender;

    $defaultTemplatedEmailDispatcher = makeDefaultDispatcher($templateRepo, $users, $emailSender);
    $defaultTemplatedEmailDispatcher->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'cs',
        ['userName' => 'John Doe', 'link' => 'https://example.com'],
    );

    expect($emailSender->sent)->toHaveCount(1)
        ->and($emailSender->sent[0]['recipientEmail'])->toBe('john@example.com');
});

it('throws EmailTemplateNotFoundException when template missing in user locale and fallback', function (): void {
    $user = dispatchUser();
    $templateRepo = new FakeEmailTemplateRepository([]);
    $users = ['550e8400-e29b-41d4-a716-446655440001' => $user];

    $defaultTemplatedEmailDispatcher = makeDefaultDispatcher($templateRepo, $users);

    expect(fn (): App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent => $defaultTemplatedEmailDispatcher->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'cs',
        ['userName' => 'John Doe'],
    ))->toThrow(EmailTemplateNotFoundException::class);
});

it('throws EmailTemplateNotFoundException when template is missing in english too', function (): void {
    $user = dispatchUser();
    $templateRepo = new FakeEmailTemplateRepository([]);
    $users = ['550e8400-e29b-41d4-a716-446655440001' => $user];

    $defaultTemplatedEmailDispatcher = makeDefaultDispatcher($templateRepo, $users);

    expect(fn (): App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent => $defaultTemplatedEmailDispatcher->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'en',
        ['userName' => 'John Doe'],
    ))->toThrow(EmailTemplateNotFoundException::class);
});

it('throws UserNotFoundException when user does not exist', function (): void {
    $templateRepo = new FakeEmailTemplateRepository([
        'user_invite:en' => dispatchTemplate('user_invite', 'en'),
    ]);
    $users = [];

    $defaultTemplatedEmailDispatcher = makeDefaultDispatcher($templateRepo, $users);

    expect(fn (): App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent => $defaultTemplatedEmailDispatcher->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'en',
        ['userName' => 'Ghost'],
    ))->toThrow(UserNotFoundException::class);
});

it('masks sensitive variables in the logged body but not in the sent email', function (): void {
    $user = dispatchUser();
    $templateRepo = new FakeEmailTemplateRepository([
        'user_invite:en' => new EmailTemplate(
            new EmailTemplateId('550e8400-e29b-41d4-a716-446655440099'),
            new EmailTemplateType('user_invite'),
            new EmailTemplateLocale('en'),
            'Invite for {{userName}}',
            '<p>Click {{link}}</p>',
            new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
            new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
        ),
    ]);
    $users = ['550e8400-e29b-41d4-a716-446655440001' => $user];
    $emailSender = new FakeEmailSender;

    $defaultTemplatedEmailDispatcher = makeDefaultDispatcher($templateRepo, $users, $emailSender);
    $templatedEmailSent = $defaultTemplatedEmailDispatcher->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'en',
        [
            'userName' => 'John Doe',
            'link' => 'https://secret-invite-url.com/abc123',
            'tenantName' => 'Acme Corp',
        ],
    );

    expect($emailSender->sent)->toHaveCount(1);

    expect($templatedEmailSent->renderedBodyMasked)->not->toContain('https://secret-invite-url.com/abc123')
        ->and($templatedEmailSent->renderedBodyMasked)->toContain('***');
});

it('returns variables unchanged when template type config is not defined', function (): void {
    $user = dispatchUser();
    $unknownType = 'unknown_template_type';
    $templateRepo = new FakeEmailTemplateRepository([
        $unknownType.':en' => new EmailTemplate(
            new EmailTemplateId('550e8400-e29b-41d4-a716-446655440099'),
            new EmailTemplateType($unknownType),
            new EmailTemplateLocale('en'),
            'Subject',
            'Body {{secret}}',
            new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
            new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
        ),
    ]);
    $users = ['550e8400-e29b-41d4-a716-446655440001' => $user];
    $emailSender = new FakeEmailSender;

    $defaultTemplatedEmailDispatcher = makeDefaultDispatcher($templateRepo, $users, $emailSender);
    $templatedEmailSent = $defaultTemplatedEmailDispatcher->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        $unknownType,
        'en',
        ['secret' => 'top-secret-value'],
    );

    expect($templatedEmailSent->renderedBodyMasked)->toContain('top-secret-value');
});
