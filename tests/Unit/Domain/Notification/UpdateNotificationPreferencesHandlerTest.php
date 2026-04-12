<?php

declare(strict_types=1);

use App\Application\Event\PropertyChange;
use App\Application\Event\PropertyChangeBuilder;
use App\Domain\Notification\Contract\Command\UpdateNotificationPreferencesCommand;
use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\Event\NotificationPreferencesUpdated;
use App\Domain\Notification\Contract\ValueObject\ChannelPreference;
use App\Domain\Notification\Enum\NotificationLevel;
use App\Domain\Notification\Handler\Command\UpdateNotificationPreferencesHandler;
use App\Domain\Notification\ValueObject\NotificationPreferences;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeNotificationPreferenceRepository;

it('saves preferences from command data', function (): void {
    $prefRepo = new FakeNotificationPreferenceRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateNotificationPreferencesHandler($prefRepo, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateNotificationPreferencesCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        preferences: [
            'info' => ['in_app'],
            'warning' => ['in_app', 'email'],
        ],
    ));

    expect($prefRepo->saved)->toHaveCount(1);
    $saved = $prefRepo->saved[0];
    expect($saved->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($saved->preferences)->toHaveCount(2)
        ->and($saved->preferences[0]->level)->toBe(NotificationLevel::Info)
        ->and($saved->preferences[0]->channels)->toBe([NotificationChannel::InApp])
        ->and($saved->preferences[1]->level)->toBe(NotificationLevel::Warning)
        ->and($saved->preferences[1]->channels)->toBe([NotificationChannel::InApp, NotificationChannel::Email]);
});

it('emits NotificationPreferencesUpdated event', function (): void {
    $prefRepo = new FakeNotificationPreferenceRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateNotificationPreferencesHandler($prefRepo, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateNotificationPreferencesCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        preferences: [
            'info' => ['in_app'],
        ],
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(NotificationPreferencesUpdated::class);
    assert($eventCollector->collected[0] instanceof NotificationPreferencesUpdated);
    expect($eventCollector->collected[0]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->changes())->toEqual([
            new PropertyChange('info', null, 'in_app'),
        ])
        ->and($eventCollector->collected[0]->occurredAt())->toBeInstanceOf(DateTimeImmutable::class);
});

it('does not save or collect event when preferences are unchanged', function (): void {
    $existing = new NotificationPreferences(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        preferences: [
            new ChannelPreference(NotificationLevel::Info, [NotificationChannel::InApp]),
        ],
    );

    $prefRepo = new FakeNotificationPreferenceRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $existing,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateNotificationPreferencesHandler($prefRepo, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateNotificationPreferencesCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        preferences: [
            'info' => ['in_app'],
        ],
    ));

    expect($prefRepo->saved)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});

it('enforces in_app channel is always present', function (): void {
    $prefRepo = new FakeNotificationPreferenceRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateNotificationPreferencesHandler($prefRepo, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateNotificationPreferencesCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        preferences: [
            'error' => ['email'],
        ],
    ));

    expect($prefRepo->saved)->toHaveCount(1);
    $saved = $prefRepo->saved[0];
    expect($saved->preferences[0]->channels)->toContain(NotificationChannel::Email)
        ->and($saved->preferences[0]->channels)->toContain(NotificationChannel::InApp)
        ->and($saved->preferences[0]->channels)->toHaveCount(2);
});

it('throws on invalid level', function (): void {
    $prefRepo = new FakeNotificationPreferenceRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateNotificationPreferencesHandler($prefRepo, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateNotificationPreferencesCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        preferences: [
            'critical' => ['in_app'],
        ],
    ));
})->throws(ValueError::class);

it('throws on invalid channel', function (): void {
    $prefRepo = new FakeNotificationPreferenceRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateNotificationPreferencesHandler($prefRepo, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateNotificationPreferencesCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        preferences: [
            'info' => ['push'],
        ],
    ));
})->throws(ValueError::class);
