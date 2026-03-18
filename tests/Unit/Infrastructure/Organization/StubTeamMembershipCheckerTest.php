<?php

declare(strict_types=1);

use App\Infrastructure\Organization\StubTeamMembershipChecker;

it('returns false for isTeamMember', function (): void {
    $checker = new StubTeamMembershipChecker;

    expect($checker->isTeamMember('user-1', 'team-1'))->toBeFalse();
});

it('returns empty array for memberTeamIds', function (): void {
    $checker = new StubTeamMembershipChecker;

    expect($checker->memberTeamIds('user-1', 'org-1'))->toBe([]);
});
