<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Team\TeamMember;
use App\Domain\Team\TeamMemberRepository;
use DateTimeImmutable;

final class FakeTeamMemberRepository implements TeamMemberRepository
{
    /** @var list<array{userId: string, teamId: string}> */
    public array $added = [];

    /** @var list<array{userId: string, teamId: string}> */
    public array $removed = [];

    /**
     * @param  array<string, list<string>>  $memberships  userId => list<teamId>
     * @param  array<string, array{name: string, email: string}>  $userInfo
     */
    public function __construct(
        private array $memberships = [],
        private array $userInfo = [],
    ) {}

    public function add(string $userId, string $teamId): void
    {
        $this->added[] = ['userId' => $userId, 'teamId' => $teamId];

        if (! isset($this->memberships[$userId])) {
            $this->memberships[$userId] = [];
        }

        $this->memberships[$userId][] = $teamId;
    }

    public function remove(string $userId, string $teamId): void
    {
        $this->removed[] = ['userId' => $userId, 'teamId' => $teamId];

        if (isset($this->memberships[$userId])) {
            $this->memberships[$userId] = array_values(array_filter(
                $this->memberships[$userId],
                fn (string $id): bool => $id !== $teamId,
            ));
        }
    }

    public function isMember(string $userId, string $teamId): bool
    {
        return isset($this->memberships[$userId]) && in_array($teamId, $this->memberships[$userId], true);
    }

    /** @return list<string> */
    public function memberTeamIds(string $userId): array
    {
        return $this->memberships[$userId] ?? [];
    }

    /** @return list<string> */
    public function directMemberTeamIds(string $userId): array
    {
        return $this->memberships[$userId] ?? [];
    }

    /** @return list<TeamMember> */
    public function listMembers(string $teamId, array $sortings = []): array
    {
        $members = [];

        foreach ($this->memberships as $userId => $teamIds) {
            if (in_array($teamId, $teamIds, true)) {
                $info = $this->userInfo[$userId] ?? ['name' => 'Unknown', 'email' => 'unknown@test.com'];
                $members[] = new TeamMember(
                    userId: $userId,
                    teamId: $teamId,
                    userName: $info['name'],
                    userEmail: $info['email'],
                    joinedAt: new DateTimeImmutable('2024-01-01 00:00:00'),
                );
            }
        }

        return $members;
    }

    /** @return list<string> */
    public function visibleUserIds(string $userId): array
    {
        $teamIds = $this->memberTeamIds($userId);

        if ($teamIds === []) {
            return [$userId];
        }

        $userIds = [$userId];

        foreach ($this->memberships as $otherUserId => $otherTeamIds) {
            foreach ($otherTeamIds as $otherTeamId) {
                if (in_array($otherTeamId, $teamIds, true)) {
                    $userIds[] = $otherUserId;

                    break;
                }
            }
        }

        return array_values(array_unique($userIds));
    }

    public function removeAllByUser(string $userId): void
    {
        unset($this->memberships[$userId]);
    }
}
