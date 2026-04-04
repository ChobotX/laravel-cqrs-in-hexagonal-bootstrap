<?php

declare(strict_types=1);

use App\Domain\Registry\Exception\InvalidVersionNumberException;
use App\Domain\Registry\ValueObject\VersionNumber;

it('can be constructed with a valid number', function (int $value): void {
    $version = new VersionNumber($value);

    expect($version->value)->toBe($value);
})->with([1, 5, 100]);

it('rejects zero', function (): void {
    new VersionNumber(0);
})->throws(InvalidVersionNumberException::class, 'Value [0] is not a valid version number.');

it('rejects negative numbers', function (): void {
    new VersionNumber(-1);
})->throws(InvalidVersionNumberException::class, 'Value [-1] is not a valid version number.');

it('can compare equality with another VersionNumber', function (): void {
    $v1 = new VersionNumber(1);
    $v2 = new VersionNumber(1);
    $v3 = new VersionNumber(2);

    expect($v1->equals($v2))->toBeTrue()
        ->and($v1->equals($v3))->toBeFalse();
});

it('can be cast to string', function (): void {
    $version = new VersionNumber(42);

    expect((string) $version)->toBe('42');
});
