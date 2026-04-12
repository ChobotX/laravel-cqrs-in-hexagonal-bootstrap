<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\ValueObject;

use DateTimeImmutable;

/**
 * Contract-level value object for team member used across Team commands, queries, and events.
 */
final readonly class TeamMember
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $teamId,
        /** Human-visible label or title. */
        public string $userName,
        /** Email address used for lookup, delivery, or authentication flows. */
        public string $userEmail,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $joinedAt,
        /** Optional avatarFile identifier when absent. */
        public ?string $avatarFileId = null,
    ) {}
}
