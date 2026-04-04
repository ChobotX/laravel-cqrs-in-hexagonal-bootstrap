<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Notification;

use App\Domain\Notification\Contract\NotificationChannel;
use App\Domain\Notification\Contract\NotificationPreferenceRepository;
use App\Domain\Notification\NotificationPreferences;

final readonly class EloquentNotificationPreferenceRepository implements NotificationPreferenceRepository
{
    public function __construct(
        private NotificationPreferenceMapper $notificationPreferenceMapper,
    ) {}

    public function findByUserId(string $userId): ?NotificationPreferences
    {
        $models = NotificationPreferenceModel::where('user_id', $userId)->get();

        if ($models->isEmpty()) {
            return null;
        }

        return $this->notificationPreferenceMapper->toDomain($userId, $models);
    }

    public function save(NotificationPreferences $notificationPreferences): void
    {
        NotificationPreferenceModel::where('user_id', $notificationPreferences->userId)->delete();

        foreach ($notificationPreferences->preferences as $pref) {
            $model = new NotificationPreferenceModel;
            $model->user_id = $notificationPreferences->userId;
            $model->level = $pref->level->value;
            $model->channels = array_map(
                static fn (NotificationChannel $notificationChannel): string => $notificationChannel->value,
                $pref->channels,
            );
            $model->save();
        }
    }
}
