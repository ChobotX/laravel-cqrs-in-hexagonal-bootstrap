<?php

declare(strict_types=1);

use App\Domain\Organization\Exception\InvalidOrganizationIdException;
use App\Domain\Organization\OrganizationId;

it('creates a valid organization id', function (): void {
    $id = new OrganizationId('550e8400-e29b-41d4-a716-446655440000');

    expect($id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and((string) $id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('throws on invalid uuid', function (): void {
    new OrganizationId('not-a-uuid');
})->throws(InvalidOrganizationIdException::class);

it('throws on empty string', function (): void {
    new OrganizationId('');
})->throws(InvalidOrganizationIdException::class);

it('compares equality', function (): void {
    $a = new OrganizationId('550e8400-e29b-41d4-a716-446655440000');
    $b = new OrganizationId('550e8400-e29b-41d4-a716-446655440000');
    $c = new OrganizationId('660e8400-e29b-41d4-a716-446655440000');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
