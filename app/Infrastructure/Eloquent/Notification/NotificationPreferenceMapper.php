<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Notification;

use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\ValueObject\ChannelPreference;
use App\Domain\Notification\Enum\NotificationLevel;
use App\Domain\Notification\ValueObject\NotificationPreferences;
use Illuminate\Support\Collection;

final readonly class NotificationPreferenceMapper
{
    /** @param Collection<int, NotificationPreferenceModel> $models */
    public function toDomain(string $userId, Collection $models): NotificationPreferences
    {
        $preferences = [];

        foreach ($models as $model) {
            $preferences[] = new ChannelPreference(
                level: NotificationLevel::from($model->level),
                channels: array_map(
                    NotificationChannel::from(...),
                    $model->channels,
                ),
            );
        }

        return new NotificationPreferences(
            userId: $userId,
            preferences: $preferences,
        );
    }
}
