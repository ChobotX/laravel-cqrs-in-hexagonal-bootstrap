<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Contract\Organization\OrganizationMembershipChecker;
use App\Domain\Organization\OrganizationMember;
use App\Domain\Organization\OrganizationMemberRepository;
use DateTimeImmutable;

final class FakeOrganizationMemberRepository implements OrganizationMemberRepository, OrganizationMembershipChecker
{
    /** @var list<array{userId: string, organizationId: string}> */
    public array $added = [];

    /** @var list<array{userId: string, organizationId: string}> */
    public array $removed = [];

    /**
     * @param  array<string, list<string>>  $memberships
     * @param  array<string, array{name: string, email: string}>  $userInfo
     */
    public function __construct(private array $memberships = [], private array $userInfo = []) {}

    public function add(string $userId, string $organizationId): void
    {
        $this->added[] = ['userId' => $userId, 'organizationId' => $organizationId];

        if (! isset($this->memberships[$userId])) {
            $this->memberships[$userId] = [];
        }

        $this->memberships[$userId][] = $organizationId;
    }

    public function remove(string $userId, string $organizationId): void
    {
        $this->removed[] = ['userId' => $userId, 'organizationId' => $organizationId];

        if (isset($this->memberships[$userId])) {
            $this->memberships[$userId] = array_values(array_filter(
                $this->memberships[$userId],
                fn (string $id): bool => $id !== $organizationId,
            ));
        }
    }

    public function isMember(string $userId, string $organizationId): bool
    {
        return isset($this->memberships[$userId]) && in_array($organizationId, $this->memberships[$userId], true);
    }

    /** @return list<string> */
    public function memberOrganizationIds(string $userId): array
    {
        return $this->memberships[$userId] ?? [];
    }

    /** @return list<OrganizationMember> */
    public function listMembers(string $organizationId): array
    {
        $members = [];

        foreach ($this->memberships as $userId => $orgIds) {
            if (in_array($organizationId, $orgIds, true)) {
                $info = $this->userInfo[$userId] ?? ['name' => 'Unknown', 'email' => 'unknown@test.com'];
                $members[] = new OrganizationMember(
                    userId: $userId,
                    organizationId: $organizationId,
                    userName: $info['name'],
                    userEmail: $info['email'],
                    joinedAt: new DateTimeImmutable('2024-01-01 00:00:00'),
                );
            }
        }

        return $members;
    }
}
