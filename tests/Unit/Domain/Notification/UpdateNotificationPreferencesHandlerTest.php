<?php

declare(strict_types=1);

use App\Domain\Notification\Command\UpdateNotificationPreferences\UpdateNotificationPreferencesCommand;
use App\Domain\Notification\Command\UpdateNotificationPreferences\UpdateNotificationPreferencesHandler;
use App\Domain\Notification\Contract\Event\NotificationPreferencesUpdated;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\NotificationLevel;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeNotificationPreferenceRepository;

it('saves preferences from command data', function (): void {
    $prefRepo = new FakeNotificationPreferenceRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateNotificationPreferencesHandler($prefRepo, $eventCollector);

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

    $handler = new UpdateNotificationPreferencesHandler($prefRepo, $eventCollector);

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
        ->and($eventCollector->collected[0]->occurredAt())->toBeInstanceOf(DateTimeImmutable::class);
});

it('enforces in_app channel is always present', function (): void {
    $prefRepo = new FakeNotificationPreferenceRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateNotificationPreferencesHandler($prefRepo, $eventCollector);

    $handler->handle(new UpdateNotificationPreferencesCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        preferences: [
            'error' => ['email'],
        ],
    ));

    expect($prefRepo->saved)->toHaveCount(1);
    $saved = $prefRepo->saved[0];
    expect($saved->preferences[0]->channels)->toBe([NotificationChannel::Email, NotificationChannel::InApp]);
});

it('throws on invalid level', function (): void {
    $prefRepo = new FakeNotificationPreferenceRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateNotificationPreferencesHandler($prefRepo, $eventCollector);

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

    $handler = new UpdateNotificationPreferencesHandler($prefRepo, $eventCollector);

    $handler->handle(new UpdateNotificationPreferencesCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        preferences: [
            'info' => ['push'],
        ],
    ));
})->throws(ValueError::class);
