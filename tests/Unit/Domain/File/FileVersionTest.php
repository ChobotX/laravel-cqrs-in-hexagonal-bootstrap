<?php

declare(strict_types=1);

use App\Domain\File\Exception\InvalidFileVersionException;
use App\Domain\File\FileVersion;

it('creates a valid file version', function (): void {
    $version = new FileVersion(1);

    expect($version->value)->toBe(1);
});

it('accepts higher versions', function (): void {
    $version = new FileVersion(42);

    expect($version->value)->toBe(42);
});

it('throws on zero', function (): void {
    new FileVersion(0);
})->throws(InvalidFileVersionException::class);

it('throws on negative', function (): void {
    new FileVersion(-1);
})->throws(InvalidFileVersionException::class);
