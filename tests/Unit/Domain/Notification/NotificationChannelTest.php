<?php

declare(strict_types=1);

use App\Domain\Notification\Contract\NotificationChannel;

it('has all expected cases', function (): void {
    expect(NotificationChannel::cases())->toHaveCount(2)
        ->and(NotificationChannel::InApp->value)->toBe('in_app')
        ->and(NotificationChannel::Email->value)->toBe('email');
});

it('can be created from value', function (): void {
    expect(NotificationChannel::from('in_app'))->toBe(NotificationChannel::InApp)
        ->and(NotificationChannel::from('email'))->toBe(NotificationChannel::Email);
});

it('throws on invalid value', function (): void {
    NotificationChannel::from('push');
})->throws(ValueError::class);
