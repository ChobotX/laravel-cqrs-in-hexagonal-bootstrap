<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Orchestrates team update and label sync via one controller dispatch.
 * Handler fans out to {@see UpdateTeamCommand} and
 * {@see \App\Domain\Label\Contract\Command\SyncEntityLabelsCommand}.
 */
#[RequiresPermission('teams.management.update')]
final readonly class UpdateTeamWithLabelsCommand implements Command
{
    /**
     * @param  list<string>|null  $labelIds  null = leave untouched, [] = sync empty
     */
    public function __construct(
        /** Target team being updated. */
        public string $id,
        /** Human-visible label or title. */
        public string $name,
        /** URL slug. */
        public string $slug,
        /** Longer description. */
        public string $description,
        /** Optional parent team id. */
        public ?string $parentTeamId,
        /** Actor performing the change. */
        public string $actorId,
        public ?array $labelIds = null,
    ) {}
}
