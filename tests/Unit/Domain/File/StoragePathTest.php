<?php

declare(strict_types=1);

use App\Domain\File\Exception\InvalidStoragePathException;
use App\Domain\File\StoragePath;

it('creates a valid storage path', function (): void {
    $path = new StoragePath('documents/abc-123.pdf');

    expect($path->value)->toBe('documents/abc-123.pdf')
        ->and((string) $path)->toBe('documents/abc-123.pdf');
});

it('throws on empty string', function (): void {
    new StoragePath('');
})->throws(InvalidStoragePathException::class);

it('throws on whitespace only', function (): void {
    new StoragePath('   ');
})->throws(InvalidStoragePathException::class);

it('compares equality', function (): void {
    $a = new StoragePath('docs/file.pdf');
    $b = new StoragePath('docs/file.pdf');
    $c = new StoragePath('docs/other.pdf');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
