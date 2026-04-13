<?php

declare(strict_types=1);

use App\Domain\Notification\Contract\Command\SendNotificationCommand;
use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\Event\NotificationCreated;
use App\Domain\Notification\Contract\ValueObject\ChannelPreference;
use App\Domain\Notification\Enum\NotificationLevel;
use App\Domain\Notification\Handler\Command\SendNotificationHandler;
use App\Domain\Notification\ValueObject\NotificationPreferences;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeIdGenerator;
use Tests\Helper\FakeNotificationChannelSender;
use Tests\Helper\FakeNotificationChannelSenderRegistry;
use Tests\Helper\FakeNotificationPreferenceRepository;
use Tests\Helper\FakeNotificationRepository;
use Tests\Helper\FakeUserRepository;

function createSendHandler(
    FakeNotificationRepository $fakeNotificationRepository,
    FakeNotificationPreferenceRepository $fakeNotificationPreferenceRepository,
    FakeNotificationChannelSenderRegistry $fakeNotificationChannelSenderRegistry,
    FakeUserRepository $fakeUserRepository,
    FakeIdGenerator $idGenerator,
    FakeEventCollector $fakeEventCollector,
): SendNotificationHandler {
    return new SendNotificationHandler(
        $fakeNotificationRepository,
        $fakeNotificationPreferenceRepository,
        $fakeNotificationChannelSenderRegistry,
        $fakeUserRepository,
        $idGenerator,
        $fakeEventCollector,
    );
}

function userRepositoryForNotificationRecipients(): FakeUserRepository
{
    $makeUser = static fn (string $id): User => new User(
        new UserId($id),
        new UserName('Recipient'),
        new Email(sprintf('user+%s@test.com', str_replace('-', '', $id))),
    );

    $ids = [
        '550e8400-e29b-41d4-a716-446655440000',
        '550e8400-e29b-41d4-a716-446655440001',
        '550e8400-e29b-41d4-a716-446655440002',
    ];
    $users = [];
    foreach ($ids as $id) {
        $users[$id] = $makeUser($id);
    }

    return new FakeUserRepository($users);
}

it('creates in-app notification for info level with default preferences', function (): void {
    $repo = new FakeNotificationRepository;
    $prefRepo = new FakeNotificationPreferenceRepository;
    $notificationChannelSender = new FakeNotificationChannelSender;
    $notificationChannelSenderRegistry = new FakeNotificationChannelSenderRegistry($notificationChannelSender);
    $idGenerator = new FakeIdGenerator;
    $eventCollector = new FakeEventCollector;

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, userRepositoryForNotificationRecipients(), $idGenerator, $eventCollector);

    $sendNotificationHandler->handle(new SendNotificationCommand(
        recipientIds: ['550e8400-e29b-41d4-a716-446655440000'],
        type: 'user.welcome',
        title: 'Welcome!',
        body: 'Welcome to the platform.',
        level: 'info',
        link: '/profile',
    ));

    expect($repo->created)->toHaveCount(1)
        ->and($repo->created[0]->recipientId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($repo->created[0]->type->value)->toBe('user.welcome')
        ->and($repo->created[0]->title)->toBe('Welcome!')
        ->and($repo->created[0]->body)->toBe('Welcome to the platform.')
        ->and($repo->created[0]->level)->toBe(NotificationLevel::Info)
        ->and($repo->created[0]->link?->value)->toBe('/profile')
        ->and($repo->created[0]->channel)->toBe(NotificationChannel::InApp)
        ->and($repo->created[0]->isRead)->toBeFalse()
        ->and($notificationChannelSender->sent)->toHaveCount(0);
});

it('collects NotificationCreated event', function (): void {
    $repo = new FakeNotificationRepository;
    $prefRepo = new FakeNotificationPreferenceRepository;
    $notificationChannelSender = new FakeNotificationChannelSender;
    $notificationChannelSenderRegistry = new FakeNotificationChannelSenderRegistry($notificationChannelSender);
    $idGenerator = new FakeIdGenerator;
    $eventCollector = new FakeEventCollector;

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, userRepositoryForNotificationRecipients(), $idGenerator, $eventCollector);

    $sendNotificationHandler->handle(new SendNotificationCommand(
        recipientIds: ['550e8400-e29b-41d4-a716-446655440000'],
        type: 'user.welcome',
        title: 'Welcome!',
        body: 'Body',
        level: 'info',
        link: null,
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(NotificationCreated::class);
    assert($eventCollector->collected[0] instanceof NotificationCreated);
    expect($eventCollector->collected[0]->recipientId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->type)->toBe('user.welcome')
        ->and($eventCollector->collected[0]->channel)->toBe('in_app')
        ->and($eventCollector->collected[0]->link)->toBeNull()
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('sends to email channel for warning level with default preferences', function (): void {
    $repo = new FakeNotificationRepository;
    $prefRepo = new FakeNotificationPreferenceRepository;
    $notificationChannelSender = new FakeNotificationChannelSender;
    $notificationChannelSenderRegistry = new FakeNotificationChannelSenderRegistry($notificationChannelSender);
    $idGenerator = new FakeIdGenerator;
    $eventCollector = new FakeEventCollector;

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, userRepositoryForNotificationRecipients(), $idGenerator, $eventCollector);

    $sendNotificationHandler->handle(new SendNotificationCommand(
        recipientIds: ['550e8400-e29b-41d4-a716-446655440000'],
        type: 'system.warning',
        title: 'Warning!',
        body: 'Something happened.',
        level: 'warning',
        link: null,
    ));

    expect($repo->created)->toHaveCount(1)
        ->and($repo->created[0]->channel)->toBe(NotificationChannel::InApp)
        ->and($notificationChannelSender->sent)->toHaveCount(1)
        ->and($notificationChannelSender->sent[0]['recipientId'])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($notificationChannelSender->sent[0]['type'])->toBe('system.warning')
        ->and($notificationChannelSender->sent[0]['title'])->toBe('Warning!');
});

it('skips delivery when user is missing for an email channel', function (): void {
    $repo = new FakeNotificationRepository;
    $prefRepo = new FakeNotificationPreferenceRepository;
    $notificationChannelSender = new FakeNotificationChannelSender;
    $notificationChannelSenderRegistry = new FakeNotificationChannelSenderRegistry($notificationChannelSender);
    $idGenerator = new FakeIdGenerator;
    $eventCollector = new FakeEventCollector;

    $missingId = 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee';
    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, userRepositoryForNotificationRecipients(), $idGenerator, $eventCollector);

    $sendNotificationHandler->handle(new SendNotificationCommand(
        recipientIds: [$missingId],
        type: 'system.warning',
        title: 'Warning!',
        body: 'Something happened.',
        level: 'warning',
        link: null,
    ));

    expect($repo->created)->toHaveCount(0)
        ->and($notificationChannelSender->sent)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});

it('uses stored preferences when available', function (): void {
    $repo = new FakeNotificationRepository;
    $prefRepo = new FakeNotificationPreferenceRepository([
        '550e8400-e29b-41d4-a716-446655440000' => new NotificationPreferences(
            userId: '550e8400-e29b-41d4-a716-446655440000',
            preferences: [
                new ChannelPreference(NotificationLevel::Info, [NotificationChannel::InApp, NotificationChannel::Email]),
            ],
        ),
    ]);
    $notificationChannelSender = new FakeNotificationChannelSender;
    $notificationChannelSenderRegistry = new FakeNotificationChannelSenderRegistry($notificationChannelSender);
    $idGenerator = new FakeIdGenerator;
    $eventCollector = new FakeEventCollector;

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, userRepositoryForNotificationRecipients(), $idGenerator, $eventCollector);

    $sendNotificationHandler->handle(new SendNotificationCommand(
        recipientIds: ['550e8400-e29b-41d4-a716-446655440000'],
        type: 'user.welcome',
        title: 'Welcome!',
        body: 'Body',
        level: 'info',
        link: null,
    ));

    expect($repo->created)->toHaveCount(1)
        ->and($notificationChannelSender->sent)->toHaveCount(1);
});

it('sends to multiple recipients', function (): void {
    $repo = new FakeNotificationRepository;
    $prefRepo = new FakeNotificationPreferenceRepository;
    $notificationChannelSender = new FakeNotificationChannelSender;
    $notificationChannelSenderRegistry = new FakeNotificationChannelSenderRegistry($notificationChannelSender);
    $idGenerator = new FakeIdGenerator;
    $eventCollector = new FakeEventCollector;

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, userRepositoryForNotificationRecipients(), $idGenerator, $eventCollector);

    $sendNotificationHandler->handle(new SendNotificationCommand(
        recipientIds: [
            '550e8400-e29b-41d4-a716-446655440001',
            '550e8400-e29b-41d4-a716-446655440002',
        ],
        type: 'user.welcome',
        title: 'Welcome!',
        body: 'Body',
        level: 'success',
        link: null,
    ));

    expect($repo->created)->toHaveCount(2)
        ->and($repo->created[0]->recipientId)->toBe('550e8400-e29b-41d4-a716-446655440001')
        ->and($repo->created[1]->recipientId)->toBe('550e8400-e29b-41d4-a716-446655440002')
        ->and($eventCollector->collected)->toHaveCount(2);
});

it('falls back to defaults when preferences exist but not for given level', function (): void {
    $repo = new FakeNotificationRepository;
    $prefRepo = new FakeNotificationPreferenceRepository([
        '550e8400-e29b-41d4-a716-446655440000' => new NotificationPreferences(
            userId: '550e8400-e29b-41d4-a716-446655440000',
            preferences: [
                new ChannelPreference(NotificationLevel::Error, [NotificationChannel::InApp]),
            ],
        ),
    ]);
    $notificationChannelSender = new FakeNotificationChannelSender;
    $notificationChannelSenderRegistry = new FakeNotificationChannelSenderRegistry($notificationChannelSender);
    $idGenerator = new FakeIdGenerator;
    $eventCollector = new FakeEventCollector;

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, userRepositoryForNotificationRecipients(), $idGenerator, $eventCollector);

    $sendNotificationHandler->handle(new SendNotificationCommand(
        recipientIds: ['550e8400-e29b-41d4-a716-446655440000'],
        type: 'user.welcome',
        title: 'Welcome!',
        body: 'Body',
        level: 'info',
        link: null,
    ));

    expect($repo->created)->toHaveCount(1)
        ->and($notificationChannelSender->sent)->toHaveCount(0);
});

it('resolves channels per-recipient with different preferences', function (): void {
    $repo = new FakeNotificationRepository;
    $prefRepo = new FakeNotificationPreferenceRepository([
        '550e8400-e29b-41d4-a716-446655440001' => new NotificationPreferences(
            userId: '550e8400-e29b-41d4-a716-446655440001',
            preferences: [
                new ChannelPreference(NotificationLevel::Warning, [NotificationChannel::InApp]),
            ],
        ),
    ]);
    $notificationChannelSender = new FakeNotificationChannelSender;
    $notificationChannelSenderRegistry = new FakeNotificationChannelSenderRegistry($notificationChannelSender);
    $idGenerator = new FakeIdGenerator;
    $eventCollector = new FakeEventCollector;

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, userRepositoryForNotificationRecipients(), $idGenerator, $eventCollector);

    $sendNotificationHandler->handle(new SendNotificationCommand(
        recipientIds: [
            '550e8400-e29b-41d4-a716-446655440001',
            '550e8400-e29b-41d4-a716-446655440002',
        ],
        type: 'system.warning',
        title: 'Alert',
        body: 'Body',
        level: 'warning',
        link: null,
    ));

    expect($repo->created)->toHaveCount(2)
        ->and($repo->created[0]->channel)->toBe(NotificationChannel::InApp)
        ->and($repo->created[1]->channel)->toBe(NotificationChannel::InApp)
        ->and($notificationChannelSender->sent)->toHaveCount(1)
        ->and($notificationChannelSender->sent[0]['recipientId'])->toBe('550e8400-e29b-41d4-a716-446655440002');
});

it('sends email for error level with default preferences', function (): void {
    $repo = new FakeNotificationRepository;
    $prefRepo = new FakeNotificationPreferenceRepository;
    $notificationChannelSender = new FakeNotificationChannelSender;
    $notificationChannelSenderRegistry = new FakeNotificationChannelSenderRegistry($notificationChannelSender);
    $idGenerator = new FakeIdGenerator;
    $eventCollector = new FakeEventCollector;

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, userRepositoryForNotificationRecipients(), $idGenerator, $eventCollector);

    $sendNotificationHandler->handle(new SendNotificationCommand(
        recipientIds: ['550e8400-e29b-41d4-a716-446655440000'],
        type: 'system.error',
        title: 'Error!',
        body: 'Something failed.',
        level: 'error',
        link: '/errors/123',
    ));

    expect($repo->created)->toHaveCount(1)
        ->and($repo->created[0]->channel)->toBe(NotificationChannel::InApp)
        ->and($notificationChannelSender->sent)->toHaveCount(1)
        ->and($notificationChannelSender->sent[0]['link'])->toBe('/errors/123');
});

it('does nothing for empty recipientIds', function (): void {
    $repo = new FakeNotificationRepository;
    $prefRepo = new FakeNotificationPreferenceRepository;
    $notificationChannelSender = new FakeNotificationChannelSender;
    $notificationChannelSenderRegistry = new FakeNotificationChannelSenderRegistry($notificationChannelSender);
    $idGenerator = new FakeIdGenerator;
    $eventCollector = new FakeEventCollector;

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, new FakeUserRepository, $idGenerator, $eventCollector);

    $sendNotificationHandler->handle(new SendNotificationCommand(
        recipientIds: [],
        type: 'user.welcome',
        title: 'Welcome!',
        body: 'Body',
        level: 'info',
        link: null,
    ));

    expect($repo->created)->toBeEmpty()
        ->and($notificationChannelSender->sent)->toBeEmpty()
        ->and($eventCollector->collected)->toBeEmpty();
});
