<?php

declare(strict_types=1);

use App\Application\Event\PropertyChangeBuilder;

it('diffs a map of pairs, omitting strict-equal ones', function (): void {
    $changes = (new PropertyChangeBuilder)->diff([
        'name' => ['Old', 'New'],
        'email' => ['same@example.com', 'same@example.com'],
        'age' => [30, 31],
        'active' => [true, false],
        'missing' => [null, 'added'],
    ]);

    expect($changes)->toHaveCount(4)
        ->and($changes[0]->property)->toBe('name')
        ->and($changes[1]->property)->toBe('age')
        ->and($changes[2]->property)->toBe('active')
        ->and($changes[3]->property)->toBe('missing')
        ->and($changes[3]->old)->toBeNull()
        ->and($changes[3]->new)->toBe('added');
});

it('returns an empty list when no pair differs', function (): void {
    $changes = (new PropertyChangeBuilder)->diff([
        'name' => ['same', 'same'],
        'count' => [1, 1],
    ]);

    expect($changes)->toBe([]);
});

it('treats null and empty string as distinct', function (): void {
    $changes = (new PropertyChangeBuilder)->diff([
        'nullable' => [null, ''],
    ]);

    expect($changes)->toHaveCount(1)
        ->and($changes[0]->old)->toBeNull()
        ->and($changes[0]->new)->toBe('');
});

it('marks changes as non-sensitive by default', function (): void {
    $changes = (new PropertyChangeBuilder)->diff([
        'name' => ['a', 'b'],
    ]);

    expect($changes[0]->sensitive)->toBeFalse();
});
