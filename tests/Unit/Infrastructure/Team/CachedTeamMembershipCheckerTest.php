<?php

declare(strict_types=1);

use App\Contract\Team\TeamMembershipChecker;
use App\Infrastructure\Team\CachedTeamMembershipChecker;

it('memoizes memberTeamIds per userId', function (): void {
    $inner = new CachedCheckerTestTrackingChecker(memberTeamIds: ['team-1', 'team-2']);
    $cached = new CachedTeamMembershipChecker($inner);

    $first = $cached->memberTeamIds('user-1');
    $second = $cached->memberTeamIds('user-1');

    expect($first)->toBe(['team-1', 'team-2'])
        ->and($second)->toBe(['team-1', 'team-2'])
        ->and($inner->memberTeamIdsCallCount)->toBe(1);
});

it('memoizes visibleUserIds per userId', function (): void {
    $inner = new CachedCheckerTestTrackingChecker(visibleUserIds: ['user-1', 'user-2']);
    $cached = new CachedTeamMembershipChecker($inner);

    $first = $cached->visibleUserIds('user-1');
    $second = $cached->visibleUserIds('user-1');

    expect($first)->toBe(['user-1', 'user-2'])
        ->and($second)->toBe(['user-1', 'user-2'])
        ->and($inner->visibleUserIdsCallCount)->toBe(1);
});

it('separates cache by userId', function (): void {
    $inner = new CachedCheckerTestTrackingChecker(memberTeamIds: ['team-1']);
    $cached = new CachedTeamMembershipChecker($inner);

    $cached->memberTeamIds('user-1');
    $cached->memberTeamIds('user-2');

    expect($inner->memberTeamIdsCallCount)->toBe(2);
});

it('delegates isTeamMember without caching', function (): void {
    $inner = new CachedCheckerTestTrackingChecker;
    $cached = new CachedTeamMembershipChecker($inner);

    $cached->isTeamMember('user-1', 'team-1');
    $cached->isTeamMember('user-1', 'team-1');

    expect($inner->isTeamMemberCallCount)->toBe(2);
});

final class CachedCheckerTestTrackingChecker implements TeamMembershipChecker
{
    public int $memberTeamIdsCallCount = 0;

    public int $visibleUserIdsCallCount = 0;

    public int $isTeamMemberCallCount = 0;

    /**
     * @param  list<string>  $memberTeamIds
     * @param  list<string>  $visibleUserIds
     */
    public function __construct(
        private readonly array $memberTeamIds = [],
        private readonly array $visibleUserIds = [],
    ) {}

    public function isTeamMember(string $userId, string $teamId): bool
    {
        $this->isTeamMemberCallCount++;

        return false;
    }

    /** @return list<string> */
    public function memberTeamIds(string $userId): array
    {
        $this->memberTeamIdsCallCount++;

        return $this->memberTeamIds;
    }

    /** @return list<string> */
    public function visibleUserIds(string $userId): array
    {
        $this->visibleUserIdsCallCount++;

        return $this->visibleUserIds;
    }
}
