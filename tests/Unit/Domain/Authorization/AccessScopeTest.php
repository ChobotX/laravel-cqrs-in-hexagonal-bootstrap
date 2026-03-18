<?php

declare(strict_types=1);

use App\Domain\Authorization\AccessScope;

it('all is more permissive than team', function (): void {
    expect(AccessScope::All->isMorePermissiveThan(AccessScope::Team))->toBeTrue();
});

it('all is more permissive than own', function (): void {
    expect(AccessScope::All->isMorePermissiveThan(AccessScope::Own))->toBeTrue();
});

it('team is more permissive than own', function (): void {
    expect(AccessScope::Team->isMorePermissiveThan(AccessScope::Own))->toBeTrue();
});

it('own is not more permissive than any other', function (): void {
    expect(AccessScope::Own->isMorePermissiveThan(AccessScope::Team))->toBeFalse()
        ->and(AccessScope::Own->isMorePermissiveThan(AccessScope::All))->toBeFalse();
});

it('same scope is not more permissive than itself', function (): void {
    expect(AccessScope::All->isMorePermissiveThan(AccessScope::All))->toBeFalse()
        ->and(AccessScope::Team->isMorePermissiveThan(AccessScope::Team))->toBeFalse()
        ->and(AccessScope::Own->isMorePermissiveThan(AccessScope::Own))->toBeFalse();
});

it('team is not more permissive than all', function (): void {
    expect(AccessScope::Team->isMorePermissiveThan(AccessScope::All))->toBeFalse();
});

it('own is not more permissive than team', function (): void {
    expect(AccessScope::Own->isMorePermissiveThan(AccessScope::Team))->toBeFalse();
});
