<?php

declare(strict_types=1);

use App\Domain\File\Contract\MimeType;
use App\Domain\File\Exception\InvalidMimeTypeException;

it('creates a valid mime type', function (): void {
    $mime = new MimeType('application/pdf');

    expect($mime->value)->toBe('application/pdf')
        ->and((string) $mime)->toBe('application/pdf');
});

it('accepts image types', function (): void {
    $mime = new MimeType('image/png');

    expect($mime->value)->toBe('image/png');
});

it('accepts types with plus suffix', function (): void {
    $mime = new MimeType('application/vnd.api+json');

    expect($mime->value)->toBe('application/vnd.api+json');
});

it('throws on empty string', function (): void {
    new MimeType('');
})->throws(InvalidMimeTypeException::class);

it('throws on missing slash', function (): void {
    new MimeType('applicationpdf');
})->throws(InvalidMimeTypeException::class);

it('throws on double slash', function (): void {
    new MimeType('application//pdf');
})->throws(InvalidMimeTypeException::class);

it('compares equality', function (): void {
    $a = new MimeType('text/plain');
    $b = new MimeType('text/plain');
    $c = new MimeType('text/html');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
