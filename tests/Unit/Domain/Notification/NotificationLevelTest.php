<?php

declare(strict_types=1);

use App\Domain\Notification\NotificationLevel;

it('has all expected cases', function (): void {
    expect(NotificationLevel::cases())->toHaveCount(4)
        ->and(NotificationLevel::Info->value)->toBe('info')
        ->and(NotificationLevel::Success->value)->toBe('success')
        ->and(NotificationLevel::Warning->value)->toBe('warning')
        ->and(NotificationLevel::Error->value)->toBe('error');
});

it('can be created from value', function (): void {
    expect(NotificationLevel::from('info'))->toBe(NotificationLevel::Info)
        ->and(NotificationLevel::from('error'))->toBe(NotificationLevel::Error);
});

it('throws on invalid value', function (): void {
    NotificationLevel::from('critical');
})->throws(ValueError::class);
