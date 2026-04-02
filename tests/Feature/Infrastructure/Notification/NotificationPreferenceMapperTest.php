<?php

declare(strict_types=1);

use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\NotificationLevel;
use App\Infrastructure\Eloquent\Notification\NotificationPreferenceMapper;
use App\Infrastructure\Eloquent\Notification\NotificationPreferenceModel;
use Illuminate\Support\Collection;

it('maps collection of preference models to domain', function (): void {
    $model1 = new NotificationPreferenceModel;
    $model1->user_id = '550e8400-e29b-41d4-a716-446655440000';
    $model1->level = 'info';
    $model1->channels = ['in_app'];

    $model2 = new NotificationPreferenceModel;
    $model2->user_id = '550e8400-e29b-41d4-a716-446655440000';
    $model2->level = 'error';
    $model2->channels = ['in_app', 'email'];

    $collection = new Collection([$model1, $model2]);

    $mapper = new NotificationPreferenceMapper;
    $notificationPreferences = $mapper->toDomain('550e8400-e29b-41d4-a716-446655440000', $collection);

    expect($notificationPreferences->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($notificationPreferences->preferences)->toHaveCount(2)
        ->and($notificationPreferences->preferences[0]->level)->toBe(NotificationLevel::Info)
        ->and($notificationPreferences->preferences[0]->channels)->toBe([NotificationChannel::InApp])
        ->and($notificationPreferences->preferences[1]->level)->toBe(NotificationLevel::Error)
        ->and($notificationPreferences->preferences[1]->channels)->toBe([NotificationChannel::InApp, NotificationChannel::Email]);
});

it('has no primary key', function (): void {
    $model = new NotificationPreferenceModel;

    expect($model->getKey())->toBeNull();
});

it('maps empty collection to empty preferences', function (): void {
    $collection = new Collection([]);

    $mapper = new NotificationPreferenceMapper;
    $notificationPreferences = $mapper->toDomain('550e8400-e29b-41d4-a716-446655440000', $collection);

    expect($notificationPreferences->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($notificationPreferences->preferences)->toBe([]);
});
