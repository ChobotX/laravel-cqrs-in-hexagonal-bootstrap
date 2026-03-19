<?php

declare(strict_types=1);

use App\Domain\Organization\Exception\InvalidOrganizationSlugException;
use App\Domain\Organization\OrganizationSlug;

it('creates a valid slug', function (): void {
    $slug = new OrganizationSlug('acme-corp');

    expect($slug->value)->toBe('acme-corp')
        ->and((string) $slug)->toBe('acme-corp');
});

it('accepts minimum length of 2', function (): void {
    $slug = new OrganizationSlug('ab');

    expect($slug->value)->toBe('ab');
});

it('accepts maximum length of 63', function (): void {
    $slug = new OrganizationSlug(str_repeat('a', 63));

    expect($slug->value)->toHaveLength(63);
});

it('accepts numeric slugs', function (): void {
    $slug = new OrganizationSlug('42');

    expect($slug->value)->toBe('42');
});

it('accepts slugs with hyphens in the middle', function (): void {
    $slug = new OrganizationSlug('my-cool-org');

    expect($slug->value)->toBe('my-cool-org');
});

it('throws on single character', function (): void {
    new OrganizationSlug('a');
})->throws(InvalidOrganizationSlugException::class);

it('throws on exceeding 63 characters', function (): void {
    new OrganizationSlug(str_repeat('a', 64));
})->throws(InvalidOrganizationSlugException::class);

it('throws on uppercase letters', function (): void {
    new OrganizationSlug('Acme');
})->throws(InvalidOrganizationSlugException::class);

it('throws on leading hyphen', function (): void {
    new OrganizationSlug('-acme');
})->throws(InvalidOrganizationSlugException::class);

it('throws on trailing hyphen', function (): void {
    new OrganizationSlug('acme-');
})->throws(InvalidOrganizationSlugException::class);

it('throws on spaces', function (): void {
    new OrganizationSlug('acme corp');
})->throws(InvalidOrganizationSlugException::class);

it('throws on special characters', function (): void {
    new OrganizationSlug('acme_corp');
})->throws(InvalidOrganizationSlugException::class);

it('throws on empty string', function (): void {
    new OrganizationSlug('');
})->throws(InvalidOrganizationSlugException::class);

it('compares equality', function (): void {
    $a = new OrganizationSlug('acme');
    $b = new OrganizationSlug('acme');
    $c = new OrganizationSlug('other');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
