<?php

declare(strict_types=1);

use App\Infrastructure\Organization\StubOrganizationMembershipChecker;

it('returns true for isMember', function (): void {
    $checker = new StubOrganizationMembershipChecker;

    expect($checker->isMember('user-1', 'org-1'))->toBeTrue();
});

it('returns empty array for memberOrganizationIds', function (): void {
    $checker = new StubOrganizationMembershipChecker;

    expect($checker->memberOrganizationIds('user-1'))->toBe([]);
});
