<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Entity\EmailTemplate;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateId;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use App\Domain\EmailTemplate\Exception\EmailTemplateNotFoundException;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use App\Infrastructure\Email\TemplatedEmailDispatcherImpl;
use Tests\Helper\FakeEmailSender;
use Tests\Helper\FakeEmailTemplateRepository;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeIdGenerator;
use Tests\Helper\FakeTemplateCompiler;
use Tests\Helper\FakeTenantContext;
use Tests\Helper\FakeUserRepository;

function makeDispatcher(
    FakeEmailTemplateRepository $fakeEmailTemplateRepository,
    FakeUserRepository $fakeUserRepository,
    ?FakeEmailSender $fakeEmailSender = null,
    ?FakeEventCollector $fakeEventCollector = null,
): TemplatedEmailDispatcherImpl {
    return new TemplatedEmailDispatcherImpl(
        $fakeEmailTemplateRepository,
        $fakeUserRepository,
        new FakeTemplateCompiler,
        $fakeEmailSender ?? new FakeEmailSender,
        new FakeTenantContext(tenantName: 'Test Org'),
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
        $fakeEventCollector ?? new FakeEventCollector,
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

// ─── Happy path ─────────────────────────────────────────────────────────────

it('dispatches email to user using the exact locale template', function (): void {
    $user = dispatchUser();
    $templateRepo = new FakeEmailTemplateRepository([
        'user_invite:en' => dispatchTemplate('user_invite', 'en'),
    ]);
    $userRepo = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440001' => $user]);
    $emailSender = new FakeEmailSender;

    $templatedEmailDispatcherImpl = makeDispatcher($templateRepo, $userRepo, $emailSender);
    $templatedEmailDispatcherImpl->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'en',
        ['userName' => 'John Doe', 'link' => 'https://example.com/invite'],
    );

    expect($emailSender->sent)->toHaveCount(1)
        ->and($emailSender->sent[0]['recipientEmail'])->toBe('john@example.com');
});

it('collects a TemplatedEmailSent event after dispatching', function (): void {
    $user = dispatchUser();
    $templateRepo = new FakeEmailTemplateRepository([
        'user_invite:en' => dispatchTemplate('user_invite', 'en'),
    ]);
    $userRepo = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440001' => $user]);
    $eventCollector = new FakeEventCollector;

    $templatedEmailDispatcherImpl = makeDispatcher($templateRepo, $userRepo, fakeEventCollector: $eventCollector);
    $templatedEmailDispatcherImpl->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'en',
        ['userName' => 'John Doe', 'link' => 'https://example.com'],
    );

    expect($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent::class);
});

// ─── Fallback locale (line 47) ───────────────────────────────────────────────

it('falls back to english template when user locale template is missing', function (): void {
    $user = dispatchUser();
    $templateRepo = new FakeEmailTemplateRepository([
        'user_invite:en' => dispatchTemplate('user_invite', 'en'),
        // no 'user_invite:cs'
    ]);
    $userRepo = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440001' => $user]);
    $emailSender = new FakeEmailSender;

    $templatedEmailDispatcherImpl = makeDispatcher($templateRepo, $userRepo, $emailSender);
    $templatedEmailDispatcherImpl->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'cs',                          // user locale
        ['userName' => 'John Doe', 'link' => 'https://example.com'],
    );

    expect($emailSender->sent)->toHaveCount(1)
        ->and($emailSender->sent[0]['recipientEmail'])->toBe('john@example.com');
});

// ─── Template not found in fallback locale (line 51) ────────────────────────

it('throws EmailTemplateNotFoundException when template missing in user locale and fallback', function (): void {
    $user = dispatchUser();
    $templateRepo = new FakeEmailTemplateRepository([]); // nothing seeded
    $userRepo = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440001' => $user]);

    $templatedEmailDispatcherImpl = makeDispatcher($templateRepo, $userRepo);

    expect(fn () => $templatedEmailDispatcherImpl->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'cs',
        ['userName' => 'John Doe'],
    ))->toThrow(EmailTemplateNotFoundException::class);
});

it('throws EmailTemplateNotFoundException when template is missing in english too', function (): void {
    $user = dispatchUser();
    $templateRepo = new FakeEmailTemplateRepository([]); // no templates at all
    $userRepo = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440001' => $user]);

    $templatedEmailDispatcherImpl = makeDispatcher($templateRepo, $userRepo);

    expect(fn () => $templatedEmailDispatcherImpl->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'en',                          // en locale, but still not found
        ['userName' => 'John Doe'],
    ))->toThrow(EmailTemplateNotFoundException::class);
});

// ─── User not found (line 57) ────────────────────────────────────────────────

it('throws UserNotFoundException when user does not exist', function (): void {
    $templateRepo = new FakeEmailTemplateRepository([
        'user_invite:en' => dispatchTemplate('user_invite', 'en'),
    ]);
    $userRepo = new FakeUserRepository([]); // no users

    $templatedEmailDispatcherImpl = makeDispatcher($templateRepo, $userRepo);

    expect(fn () => $templatedEmailDispatcherImpl->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'en',
        ['userName' => 'Ghost'],
    ))->toThrow(UserNotFoundException::class);
});

// ─── Sensitive variable masking (line 100) ───────────────────────────────────

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
    $userRepo = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440001' => $user]);
    $emailSender = new FakeEmailSender;
    $eventCollector = new FakeEventCollector;

    $templatedEmailDispatcherImpl = makeDispatcher($templateRepo, $userRepo, $emailSender, $eventCollector);
    $templatedEmailDispatcherImpl->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        'user_invite',
        'en',
        [
            'userName' => 'John Doe',
            'link' => 'https://secret-invite-url.com/abc123', // sensitive per EmailTemplateTypes
            'tenantName' => 'Acme Corp',
        ],
    );

    expect($emailSender->sent)->toHaveCount(1);

    /** @var App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent $event */
    $event = $eventCollector->collected[0];

    // The masked body must not contain the real link
    expect($event->renderedBodyMasked)->not->toContain('https://secret-invite-url.com/abc123')
        ->and($event->renderedBodyMasked)->toContain('***');
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
    $userRepo = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440001' => $user]);
    $emailSender = new FakeEmailSender;
    $eventCollector = new FakeEventCollector;

    $templatedEmailDispatcherImpl = makeDispatcher($templateRepo, $userRepo, $emailSender, $eventCollector);
    $templatedEmailDispatcherImpl->dispatch(
        '550e8400-e29b-41d4-a716-446655440001',
        $unknownType,
        'en',
        ['secret' => 'top-secret-value'],
    );

    /** @var App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent $event */
    $event = $eventCollector->collected[0];

    // No masking applied — value should appear in the masked body
    expect($event->renderedBodyMasked)->toContain('top-secret-value');
});
