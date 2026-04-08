<?php

declare(strict_types=1);

use App\Domain\GridPreset\Contract\ValueObject\GridPresetId;
use App\Domain\GridPreset\Exception\InvalidGridPresetIdException;

it('accepts valid uuid', function (): void {
    $id = new GridPresetId('550e8400-e29b-41d4-a716-446655440000');

    expect($id->value)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('implements stringable', function (): void {
    $id = new GridPresetId('550e8400-e29b-41d4-a716-446655440000');

    expect((string) $id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('throws on invalid uuid', function (): void {
    new GridPresetId('not-a-uuid');
})->throws(InvalidGridPresetIdException::class);

it('throws on empty string', function (): void {
    new GridPresetId('');
})->throws(InvalidGridPresetIdException::class);

it('checks equality', function (): void {
    $id1 = new GridPresetId('550e8400-e29b-41d4-a716-446655440000');
    $id2 = new GridPresetId('550e8400-e29b-41d4-a716-446655440000');
    $id3 = new GridPresetId('660e8400-e29b-41d4-a716-446655440000');

    expect($id1->equals($id2))->toBeTrue()
        ->and($id1->equals($id3))->toBeFalse();
});
