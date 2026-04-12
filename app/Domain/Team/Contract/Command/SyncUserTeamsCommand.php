<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for sync user teams in the Team bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Null-safe — null input means skip, controller decides based on form visibility')]
final readonly class SyncUserTeamsCommand implements Command
{
    /**
     * @param  list<string>|null  $submittedTeamIds  null = skip sync, [] = remove all
     */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** List of stable identifiers for batch operations. */
        public ?array $submittedTeamIds,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $actingUserId,
    ) {}
}
