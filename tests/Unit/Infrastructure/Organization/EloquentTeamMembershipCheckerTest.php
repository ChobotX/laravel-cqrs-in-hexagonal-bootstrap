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

it('returns visible user IDs including team members', function (): void {
    $repo = new FakeTeamMemberRepository(
        memberships: [
            'user-1' => ['team-1'],
            'user-2' => ['team-1'],
            'user-3' => ['team-2'],
        ],
        teamOrganizations: [
            'team-1' => 'org-1',
            'team-2' => 'org-1',
        ],
        userInfo: [
            'user-1' => ['name' => 'A', 'email' => 'a@test.com'],
            'user-2' => ['name' => 'B', 'email' => 'b@test.com'],
            'user-3' => ['name' => 'C', 'email' => 'c@test.com'],
        ],
    );
    $checker = new EloquentTeamMembershipChecker($repo);

    $ids = $checker->visibleUserIds('user-1', 'org-1');

    expect($ids)->toContain('user-1', 'user-2')
        ->and($ids)->not->toContain('user-3');
});

it('returns only self when no teams', function (): void {
    $repo = new FakeTeamMemberRepository;
    $checker = new EloquentTeamMembershipChecker($repo);

    expect($checker->visibleUserIds('user-1', 'org-1'))->toBe(['user-1']);
});
