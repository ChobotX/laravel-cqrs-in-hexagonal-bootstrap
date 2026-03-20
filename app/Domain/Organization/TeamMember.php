<?php

declare(strict_types=1);

namespace App\Domain\Organization;

use App\Application\Organization\TenantAgnostic;
use DateTimeImmutable;

#[TenantAgnostic(reason: 'Team membership is scoped through teams, which belong to organizations')]
final readonly class TeamMember
{
    public function __construct(
        public string $userId,
        public string $teamId,
        public string $userName,
        public string $userEmail,
        public DateTimeImmutable $joinedAt,
    ) {}
}
