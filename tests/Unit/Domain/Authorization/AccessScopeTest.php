<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Enum\AccessScope;

it('all is more permissive than team tree', function (): void {
    expect(AccessScope::All->isMorePermissiveThan(AccessScope::TeamTree))->toBeTrue();
});

it('team tree is more permissive than team', function (): void {
    expect(AccessScope::TeamTree->isMorePermissiveThan(AccessScope::Team))->toBeTrue();
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
        ->and(AccessScope::TeamTree->isMorePermissiveThan(AccessScope::TeamTree))->toBeFalse()
        ->and(AccessScope::Team->isMorePermissiveThan(AccessScope::Team))->toBeFalse()
        ->and(AccessScope::Own->isMorePermissiveThan(AccessScope::Own))->toBeFalse();
});

it('team is not more permissive than all', function (): void {
    expect(AccessScope::Team->isMorePermissiveThan(AccessScope::All))->toBeFalse();
});

it('team is not more permissive than team tree', function (): void {
    expect(AccessScope::Team->isMorePermissiveThan(AccessScope::TeamTree))->toBeFalse();
});

it('own is not more permissive than team', function (): void {
    expect(AccessScope::Own->isMorePermissiveThan(AccessScope::Team))->toBeFalse();
});
