<?php

declare(strict_types=1);

use App\Domain\File\Exception\InvalidFileNamespaceException;
use App\Domain\File\ValueObject\FileNamespace;

it('creates a valid file namespace', function (): void {
    $ns = new FileNamespace('user-avatars');

    expect($ns->value)->toBe('user-avatars')
        ->and((string) $ns)->toBe('user-avatars');
});

it('accepts minimum length slug', function (): void {
    $ns = new FileNamespace('ab');

    expect($ns->value)->toBe('ab');
});

it('accepts max length slug', function (): void {
    $slug = str_repeat('a', 63);
    $ns = new FileNamespace($slug);

    expect($ns->value)->toBe($slug);
});

it('throws on too short', function (): void {
    new FileNamespace('a');
})->throws(InvalidFileNamespaceException::class);

it('throws on too long', function (): void {
    new FileNamespace(str_repeat('a', 64));
})->throws(InvalidFileNamespaceException::class);

it('throws on uppercase', function (): void {
    new FileNamespace('User-Avatars');
})->throws(InvalidFileNamespaceException::class);

it('throws on spaces', function (): void {
    new FileNamespace('user avatars');
})->throws(InvalidFileNamespaceException::class);

it('throws on leading hyphen', function (): void {
    new FileNamespace('-avatars');
})->throws(InvalidFileNamespaceException::class);

it('throws on trailing hyphen', function (): void {
    new FileNamespace('avatars-');
})->throws(InvalidFileNamespaceException::class);

it('throws on empty string', function (): void {
    new FileNamespace('');
})->throws(InvalidFileNamespaceException::class);

it('compares equality', function (): void {
    $a = new FileNamespace('documents');
    $b = new FileNamespace('documents');
    $c = new FileNamespace('avatars');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
