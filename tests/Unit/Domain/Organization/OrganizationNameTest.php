<?php

declare(strict_types=1);

use App\Domain\Organization\Exception\InvalidOrganizationNameException;
use App\Domain\Organization\OrganizationName;

it('creates a valid name', function (): void {
    $name = new OrganizationName('Acme Corp');

    expect($name->value)->toBe('Acme Corp')
        ->and((string) $name)->toBe('Acme Corp');
});

it('trims whitespace', function (): void {
    $name = new OrganizationName('  Acme Corp  ');

    expect($name->value)->toBe('Acme Corp');
});

it('throws on empty string', function (): void {
    new OrganizationName('');
})->throws(InvalidOrganizationNameException::class);

it('throws on whitespace only', function (): void {
    new OrganizationName('   ');
})->throws(InvalidOrganizationNameException::class);

it('compares equality', function (): void {
    $a = new OrganizationName('Acme');
    $b = new OrganizationName('Acme');
    $c = new OrganizationName('Other');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
