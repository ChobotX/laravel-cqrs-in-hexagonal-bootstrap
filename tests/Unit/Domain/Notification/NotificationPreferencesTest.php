<?php

declare(strict_types=1);

use App\Domain\Notification\ChannelPreference;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\NotificationPreferences;

it('can be constructed with user id and preferences', function (): void {
    $prefs = new NotificationPreferences(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        preferences: [
            new ChannelPreference(NotificationLevel::Info, [NotificationChannel::InApp]),
            new ChannelPreference(NotificationLevel::Error, [NotificationChannel::InApp, NotificationChannel::Email]),
        ],
    );

    expect($prefs->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($prefs->preferences)->toHaveCount(2)
        ->and($prefs->preferences[0]->level)->toBe(NotificationLevel::Info)
        ->and($prefs->preferences[1]->channels)->toBe([NotificationChannel::InApp, NotificationChannel::Email]);
});

it('can be constructed with empty preferences', function (): void {
    $prefs = new NotificationPreferences(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        preferences: [],
    );

    expect($prefs->preferences)->toBe([]);
});
