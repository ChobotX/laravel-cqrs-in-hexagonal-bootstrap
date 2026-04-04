<?php

declare(strict_types=1);

use App\Domain\Notification\Contract\NotificationChannel;
use App\Domain\Notification\NotificationLevel;
use App\Infrastructure\Eloquent\Notification\NotificationMapper;
use App\Infrastructure\Eloquent\Notification\NotificationModel;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;

it('maps model to domain entity', function (): void {
    $model = new NotificationModel;
    $model->id = '550e8400-e29b-41d4-a716-446655440000';
    $model->recipient_id = '660e8400-e29b-41d4-a716-446655440000';
    $model->type = 'user.welcome';
    $model->title = 'Welcome!';
    $model->body = 'Welcome to the platform.';
    $model->level = 'info';
    $model->link = '/profile';
    $model->channel = 'in_app';
    $model->is_read = false;
    $model->created_at = Carbon::parse('2026-01-15T10:00:00+00:00');
    $model->read_at = null;

    $mapper = new NotificationMapper;
    $notification = $mapper->toDomain($model);

    expect($notification->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($notification->recipientId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($notification->type->value)->toBe('user.welcome')
        ->and($notification->title)->toBe('Welcome!')
        ->and($notification->body)->toBe('Welcome to the platform.')
        ->and($notification->level)->toBe(NotificationLevel::Info)
        ->and($notification->link?->value)->toBe('/profile')
        ->and($notification->channel)->toBe(NotificationChannel::InApp)
        ->and($notification->isRead)->toBeFalse()
        ->and($notification->readAt)->toBeNull();
});

it('maps model with read_at to domain entity', function (): void {
    $model = new NotificationModel;
    $model->id = '550e8400-e29b-41d4-a716-446655440000';
    $model->recipient_id = '660e8400-e29b-41d4-a716-446655440000';
    $model->type = 'user.welcome';
    $model->title = 'Welcome!';
    $model->body = 'Body';
    $model->level = 'info';
    $model->link = null;
    $model->channel = 'in_app';
    $model->is_read = true;
    $model->created_at = Carbon::parse('2026-01-15T10:00:00+00:00');
    $model->read_at = CarbonImmutable::parse('2026-01-15T11:00:00+00:00');

    $mapper = new NotificationMapper;
    $notification = $mapper->toDomain($model);

    expect($notification->isRead)->toBeTrue()
        ->and($notification->readAt)->toBeInstanceOf(DateTimeImmutable::class)
        ->and($notification->link)->toBeNull();
});
