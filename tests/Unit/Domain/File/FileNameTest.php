<?php

declare(strict_types=1);

use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Exception\InvalidFileNameException;

it('creates a valid file name', function (): void {
    $name = new FileName('document.pdf');

    expect($name->value)->toBe('document.pdf')
        ->and((string) $name)->toBe('document.pdf');
});

it('trims whitespace', function (): void {
    $name = new FileName('  report.docx  ');

    expect($name->value)->toBe('report.docx');
});

it('throws on empty string', function (): void {
    new FileName('');
})->throws(InvalidFileNameException::class);

it('throws on whitespace only', function (): void {
    new FileName('   ');
})->throws(InvalidFileNameException::class);

it('throws on too long name', function (): void {
    new FileName(str_repeat('a', 256));
})->throws(InvalidFileNameException::class);

it('accepts max length name', function (): void {
    $name = new FileName(str_repeat('a', 255));

    expect(strlen($name->value))->toBe(255);
});

it('compares equality', function (): void {
    $a = new FileName('report.pdf');
    $b = new FileName('report.pdf');
    $c = new FileName('other.pdf');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
