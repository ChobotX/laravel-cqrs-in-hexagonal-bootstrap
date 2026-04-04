<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract;

use DateTimeImmutable;

final readonly class TeamMember
{
    public function __construct(
        public string $userId,
        public string $teamId,
        public string $userName,
        public string $userEmail,
        public DateTimeImmutable $joinedAt,
        public ?string $avatarFileId = null,
    ) {}
}
