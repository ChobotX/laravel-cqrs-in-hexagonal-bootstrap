<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\Exception\InvalidEntryIdException;

it('can be constructed with a valid UUID', function (): void {
    $id = new EntryId('550e8400-e29b-41d4-a716-446655440000');

    expect($id->value)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('rejects a non-UUID string', function (): void {
    new EntryId('not-a-uuid');
})->throws(InvalidEntryIdException::class, 'Value [not-a-uuid] is not a valid UUID.');

it('rejects an empty string', function (): void {
    new EntryId('');
})->throws(InvalidEntryIdException::class, 'Value [] is not a valid UUID.');

it('can compare equality with another EntryId', function (): void {
    $id1 = new EntryId('550e8400-e29b-41d4-a716-446655440000');
    $id2 = new EntryId('550e8400-e29b-41d4-a716-446655440000');
    $id3 = new EntryId('660e8400-e29b-41d4-a716-446655440000');

    expect($id1->equals($id2))->toBeTrue()
        ->and($id1->equals($id3))->toBeFalse();
});

it('can be cast to string', function (): void {
    $id = new EntryId('550e8400-e29b-41d4-a716-446655440000');

    expect((string) $id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});
