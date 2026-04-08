<?php

declare(strict_types=1);

use App\Application\Bus\SkipAuditLog;

it('is a class attribute with a reason', function (): void {
    $attribute = new SkipAuditLog(reason: 'Test reason');

    expect($attribute->reason)->toBe('Test reason');
});

it('targets classes only', function (): void {
    $reflection = new ReflectionClass(SkipAuditLog::class);
    $attributes = $reflection->getAttributes(Attribute::class);

    expect($attributes)->toHaveCount(1);

    $attribute = $attributes[0]->newInstance();
    expect($attribute->flags & Attribute::TARGET_CLASS)->toBeTruthy();
});
