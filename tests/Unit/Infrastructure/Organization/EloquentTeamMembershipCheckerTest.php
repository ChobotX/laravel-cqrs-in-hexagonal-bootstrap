<?php

declare(strict_types=1);

use App\Infrastructure\Organization\EloquentTeamMembershipChecker;
use Tests\Helper\FakeTeamMemberRepository;

it('delegates isTeamMember to repository', function (): void {
    $repo = new FakeTeamMemberRepository([
        'user-1' => ['team-1'],
    ]);
    $checker = new EloquentTeamMembershipChecker($repo);

    expect($checker->isTeamMember('user-1', 'team-1'))->toBeTrue()
        ->and($checker->isTeamMember('user-1', 'team-2'))->toBeFalse();
});

it('delegates memberTeamIds to repository', function (): void {
    $repo = new FakeTeamMemberRepository(
        memberships: [
            'user-1' => ['team-1', 'team-2'],
        ],
        teamOrganizations: [
            'team-1' => 'org-1',
            'team-2' => 'org-1',
        ],
    );
    $checker = new EloquentTeamMembershipChecker($repo);

    $ids = $checker->memberTeamIds('user-1', 'org-1');

    expect($ids)->toHaveCount(2)
        ->and($ids)->toContain('team-1', 'team-2');
});

it('returns empty for non-member', function (): void {
    $repo = new FakeTeamMemberRepository;
    $checker = new EloquentTeamMembershipChecker($repo);

    expect($checker->memberTeamIds('user-1', 'org-1'))->toBe([]);
});
