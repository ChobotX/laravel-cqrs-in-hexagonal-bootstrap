<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\ValueObject\EmailLogId;
use App\Domain\EmailTemplate\Exception\InvalidEmailLogIdException;

it('can be constructed with a valid UUID', function (): void {
    $id = new EmailLogId('550e8400-e29b-41d4-a716-446655440000');

    expect($id->value)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('rejects a non-UUID string', function (): void {
    new EmailLogId('not-a-uuid');
})->throws(InvalidEmailLogIdException::class, 'Value [not-a-uuid] is not a valid UUID.');

it('rejects an empty string', function (): void {
    new EmailLogId('');
})->throws(InvalidEmailLogIdException::class, 'Value [] is not a valid UUID.');

it('can compare equality with another EmailLogId', function (): void {
    $id1 = new EmailLogId('550e8400-e29b-41d4-a716-446655440000');
    $id2 = new EmailLogId('550e8400-e29b-41d4-a716-446655440000');
    $id3 = new EmailLogId('660e8400-e29b-41d4-a716-446655440000');

    expect($id1->equals($id2))->toBeTrue()
        ->and($id1->equals($id3))->toBeFalse();
});

it('can be cast to string', function (): void {
    $id = new EmailLogId('550e8400-e29b-41d4-a716-446655440000');

    expect((string) $id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});
