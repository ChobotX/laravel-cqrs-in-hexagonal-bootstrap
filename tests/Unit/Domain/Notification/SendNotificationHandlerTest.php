<?php

declare(strict_types=1);

use App\Domain\Notification\ChannelPreference;
use App\Domain\Notification\Command\SendNotification\SendNotificationCommand;
use App\Domain\Notification\Command\SendNotification\SendNotificationHandler;
use App\Domain\Notification\Event\NotificationCreated;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\NotificationPreferences;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeIdGenerator;
use Tests\Helper\FakeNotificationChannelSender;
use Tests\Helper\FakeNotificationChannelSenderRegistry;
use Tests\Helper\FakeNotificationPreferenceRepository;
use Tests\Helper\FakeNotificationRepository;

function createSendHandler(
    FakeNotificationRepository $fakeNotificationRepository,
    FakeNotificationPreferenceRepository $fakeNotificationPreferenceRepository,
    FakeNotificationChannelSenderRegistry $fakeNotificationChannelSenderRegistry,
    FakeIdGenerator $idGenerator,
    FakeEventCollector $fakeEventCollector,
): SendNotificationHandler {
    return new SendNotificationHandler($fakeNotificationRepository, $fakeNotificationPreferenceRepository, $fakeNotificationChannelSenderRegistry, $idGenerator, $fakeEventCollector);
}

it('creates in-app notification for info level with default preferences', function (): void {
    $repo = new FakeNotificationRepository;
    $prefRepo = new FakeNotificationPreferenceRepository;
    $notificationChannelSender = new FakeNotificationChannelSender;
    $notificationChannelSenderRegistry = new FakeNotificationChannelSenderRegistry($notificationChannelSender);
    $idGenerator = new FakeIdGenerator;
    $eventCollector = new FakeEventCollector;

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, $idGenerator, $eventCollector);

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

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, $idGenerator, $eventCollector);

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

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, $idGenerator, $eventCollector);

    $sendNotificationHandler->handle(new SendNotificationCommand(
        recipientIds: ['550e8400-e29b-41d4-a716-446655440000'],
        type: 'system.warning',
        title: 'Warning!',
        body: 'Something happened.',
        level: 'warning',
        link: null,
    ));

    expect($repo->created)->toHaveCount(1)
        ->and($notificationChannelSender->sent)->toHaveCount(1)
        ->and($notificationChannelSender->sent[0]['recipientId'])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($notificationChannelSender->sent[0]['type'])->toBe('system.warning')
        ->and($notificationChannelSender->sent[0]['title'])->toBe('Warning!');
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

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, $idGenerator, $eventCollector);

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

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, $idGenerator, $eventCollector);

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

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, $idGenerator, $eventCollector);

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

it('sends email for error level with default preferences', function (): void {
    $repo = new FakeNotificationRepository;
    $prefRepo = new FakeNotificationPreferenceRepository;
    $notificationChannelSender = new FakeNotificationChannelSender;
    $notificationChannelSenderRegistry = new FakeNotificationChannelSenderRegistry($notificationChannelSender);
    $idGenerator = new FakeIdGenerator;
    $eventCollector = new FakeEventCollector;

    $sendNotificationHandler = createSendHandler($repo, $prefRepo, $notificationChannelSenderRegistry, $idGenerator, $eventCollector);

    $sendNotificationHandler->handle(new SendNotificationCommand(
        recipientIds: ['550e8400-e29b-41d4-a716-446655440000'],
        type: 'system.error',
        title: 'Error!',
        body: 'Something failed.',
        level: 'error',
        link: '/errors/123',
    ));

    expect($repo->created)->toHaveCount(1)
        ->and($notificationChannelSender->sent)->toHaveCount(1)
        ->and($notificationChannelSender->sent[0]['link'])->toBe('/errors/123');
});
