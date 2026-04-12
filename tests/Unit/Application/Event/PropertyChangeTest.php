<?php

declare(strict_types=1);

use App\Application\Event\PropertyChange;

it('constructs with scalar values', function (): void {
    $change = new PropertyChange('name', 'old', 'new');

    expect($change->property)->toBe('name')
        ->and($change->old)->toBe('old')
        ->and($change->new)->toBe('new')
        ->and($change->sensitive)->toBeFalse();
});

it('builds a redacted change with null values and sensitive=true', function (): void {
    $propertyChange = PropertyChange::redacted('password');

    expect($propertyChange->property)->toBe('password')
        ->and($propertyChange->old)->toBeNull()
        ->and($propertyChange->new)->toBeNull()
        ->and($propertyChange->sensitive)->toBeTrue();
});
