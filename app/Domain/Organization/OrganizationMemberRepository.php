<?php

declare(strict_types=1);

namespace App\Domain\Organization;

interface OrganizationMemberRepository
{
    public function add(string $userId, string $organizationId): void;

    public function remove(string $userId, string $organizationId): void;

    public function isMember(string $userId, string $organizationId): bool;

    /** @return list<string> */
    public function memberOrganizationIds(string $userId): array;

    /** @return list<OrganizationMember> */
    public function listMembers(string $organizationId): array;
}
