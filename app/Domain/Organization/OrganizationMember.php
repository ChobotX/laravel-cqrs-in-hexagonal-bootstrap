<?php

declare(strict_types=1);

namespace App\Domain\Organization;

use DateTimeImmutable;

final readonly class OrganizationMember
{
    public function __construct(
        public string $userId,
        public string $organizationId,
        public string $userName,
        public string $userEmail,
        public DateTimeImmutable $joinedAt,
    ) {}
}
