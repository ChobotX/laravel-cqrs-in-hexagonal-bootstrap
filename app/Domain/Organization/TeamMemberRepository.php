<?php

declare(strict_types=1);

namespace App\Domain\Organization;

interface TeamMemberRepository
{
    public function add(string $userId, string $teamId): void;

    public function remove(string $userId, string $teamId): void;

    public function isMember(string $userId, string $teamId): bool;

    /** @return list<string> Team IDs including descendant teams (for scope filtering) */
    public function memberTeamIds(string $userId, string $organizationId): array;

    /** @return list<string> Only directly assigned team IDs (for membership management) */
    public function directMemberTeamIds(string $userId, string $organizationId): array;

    /** @return list<TeamMember> */
    public function listMembers(string $teamId): array;

    public function removeAllByUserAndOrganization(string $userId, string $organizationId): void;
}
