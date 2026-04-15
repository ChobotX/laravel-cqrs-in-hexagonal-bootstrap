<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Repository;

use App\Application\Sorting\Sorting;
use App\Domain\Team\Contract\ValueObject\TeamMember;

/**
 * Persistence port for team member data in the Team context; implementations live in Infrastructure.
 */
interface TeamMemberRepository
{
    /** Contract operation `add`; see infrastructure for behavior. */
    public function add(string $userId, string $teamId): void;

    /** Deletes or soft-deletes the targeted record. */
    public function remove(string $userId, string $teamId): void;

    /** Evaluates the rule without mutating domain state. */
    public function isMember(string $userId, string $teamId): bool;

    /** @return list<string> Team IDs including descendant teams (for scope filtering) */
    public function memberTeamIds(string $userId): array;

    /** @return list<string> Only directly assigned team IDs (for membership management) */
    public function directMemberTeamIds(string $userId): array;

    /**
     * @param  list<string>  $userIds
     * @return array<string, list<string>> userId => directTeamIds
     *                                     Contract operation `directMemberTeamIdsForUsers`; see infrastructure for behavior.
     */
    public function directMemberTeamIdsForUsers(array $userIds): array;

    /**
     * @param  list<Sorting>  $sortings
     * @return list<TeamMember>
     *                          Returns a filtered collection according to repository rules.
     */
    public function listMembers(string $teamId, array $sortings = []): array;

    /** @return list<string> User IDs visible to this user (members of their teams + descendants, always includes self) */
    public function visibleUserIds(string $userId): array;

    /** @return list<string> User IDs from directly assigned teams only (always includes self) */
    public function directVisibleUserIds(string $userId): array;

    /** @return list<string> User IDs with direct membership in the given team */
    public function memberUserIdsForTeam(string $teamId): array;

    /** @return list<string> User IDs in the team or any descendant team (distinct) */
    public function memberUserIdsForTeamSubtree(string $teamId): array;

    /** Deletes or soft-deletes the targeted record. */
    public function removeAllByUser(string $userId): void;
}
