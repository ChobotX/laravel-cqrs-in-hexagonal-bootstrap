<?php

declare(strict_types=1);

use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\ValueObject\ChannelPreference;
use App\Domain\Notification\Enum\NotificationLevel;

it('can be constructed with level and channels', function (): void {
    $pref = new ChannelPreference(
        level: NotificationLevel::Warning,
        channels: [NotificationChannel::InApp, NotificationChannel::Email],
    );

    expect($pref->level)->toBe(NotificationLevel::Warning)
        ->and($pref->channels)->toBe([NotificationChannel::InApp, NotificationChannel::Email]);
});

it('can be constructed with empty channels', function (): void {
    $pref = new ChannelPreference(
        level: NotificationLevel::Info,
        channels: [],
    );

    expect($pref->channels)->toBe([]);
});
