<?php

declare(strict_types=1);

use App\Domain\File\Contract\FileId;
use App\Domain\File\Exception\InvalidFileIdException;

it('creates a valid file id', function (): void {
    $id = new FileId('550e8400-e29b-41d4-a716-446655440000');

    expect($id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and((string) $id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('throws on invalid uuid', function (): void {
    new FileId('not-a-uuid');
})->throws(InvalidFileIdException::class);

it('throws on empty string', function (): void {
    new FileId('');
})->throws(InvalidFileIdException::class);

it('compares equality', function (): void {
    $a = new FileId('550e8400-e29b-41d4-a716-446655440000');
    $b = new FileId('550e8400-e29b-41d4-a716-446655440000');
    $c = new FileId('660e8400-e29b-41d4-a716-446655440000');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
