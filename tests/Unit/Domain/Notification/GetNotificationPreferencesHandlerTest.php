<?php

declare(strict_types=1);

use App\Domain\Notification\Contract\ChannelPreference;
use App\Domain\Notification\Contract\NotificationChannel;
use App\Domain\Notification\Contract\Query\GetNotificationPreferencesQuery;
use App\Domain\Notification\Handler\Query\GetNotificationPreferencesHandler;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\NotificationPreferences;
use Tests\Helper\FakeNotificationPreferenceRepository;

it('returns stored preferences when available', function (): void {
    $stored = new NotificationPreferences(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        preferences: [
            new ChannelPreference(NotificationLevel::Info, [NotificationChannel::InApp, NotificationChannel::Email]),
        ],
    );

    $prefRepo = new FakeNotificationPreferenceRepository(['550e8400-e29b-41d4-a716-446655440000' => $stored]);

    $handler = new GetNotificationPreferencesHandler($prefRepo);
    $notificationPreferences = $handler->handle(new GetNotificationPreferencesQuery(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($notificationPreferences)->toBe($stored);
});

it('returns default preferences when none stored', function (): void {
    $prefRepo = new FakeNotificationPreferenceRepository;

    $handler = new GetNotificationPreferencesHandler($prefRepo);
    $notificationPreferences = $handler->handle(new GetNotificationPreferencesQuery(userId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($notificationPreferences->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($notificationPreferences->preferences)->toHaveCount(4);

    expect($notificationPreferences->preferences[0]->level)->toBe(NotificationLevel::Info)
        ->and($notificationPreferences->preferences[0]->channels)->toBe([NotificationChannel::InApp]);

    expect($notificationPreferences->preferences[1]->level)->toBe(NotificationLevel::Success)
        ->and($notificationPreferences->preferences[1]->channels)->toBe([NotificationChannel::InApp]);

    expect($notificationPreferences->preferences[2]->level)->toBe(NotificationLevel::Warning)
        ->and($notificationPreferences->preferences[2]->channels)->toBe([NotificationChannel::InApp, NotificationChannel::Email]);

    expect($notificationPreferences->preferences[3]->level)->toBe(NotificationLevel::Error)
        ->and($notificationPreferences->preferences[3]->channels)->toBe([NotificationChannel::InApp, NotificationChannel::Email]);
});
